<?php

namespace App\Services;

use App\Jobs\ProcessStoryMedia;
use App\Jobs\PurgeStoryMedia;
use App\Models\Restaurant;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Models\StoryView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoryService
{

    public function createDraft(Restaurant $restaurant, array $attributes = []): Story
    {
        $this->assertStoriesEnabled($restaurant);

        return Story::create([
            'restaurant_id' => $restaurant->id,
            'title' => $attributes['title'] ?? null,
            'status' => Story::STATUS_DRAFT,
        ]);
    }

    public function attachMedia(Story $story, array $payload, UploadedFile $file, ?UploadedFile $thumbnail = null): StoryMedia
    {
        $this->assertStoriesEnabled($story->restaurant);

        $maxMedia = (int) config('stories.max_media_per_story', 10);

        if ($story->media()->count() >= $maxMedia) {
            throw ValidationException::withMessages([
                'media' => __('You can only attach :limit media items to a story.', ['limit' => $maxMedia]),
            ]);
        }

        $disk = (string) config('stories.media_disk', 'public');
        $nextSequence = $this->determineSequence($story, $payload['sequence'] ?? null);

        $baseDirectory = 'stories/' . $story->id;
        $path = $file->store($baseDirectory, $disk);
        $thumbPath = $thumbnail?->store($baseDirectory . '/thumbnails', $disk);

        $duration = $payload['duration_seconds'] ?? (int) config('stories.default_duration', 5);

        $media = $story->media()->create([
            'sequence' => $nextSequence,
            'media_type' => $payload['media_type'],
            'media_path' => $path,
            'thumbnail_path' => $thumbPath,
            'duration_seconds' => max(1, (int) $duration),
            'caption' => $payload['caption'] ?? null,
            'cta_label' => $payload['cta_label'] ?? null,
            'cta_url' => $payload['cta_url'] ?? null,
        ]);

        DB::afterCommit(function () use ($media) {
            ProcessStoryMedia::dispatch($media->id);
        });

        return $media->fresh();
    }

    public function publish(Story $story, ?Carbon $publishAt = null): Story
    {
        $this->assertStoriesEnabled($story->restaurant);

        if ($story->media()->count() === 0) {
            throw ValidationException::withMessages([
                'story' => __('Story must include at least one media item before publishing.'),
            ]);
        }

        $publishAt = $publishAt ?? Carbon::now();
        $expireAt = $publishAt->copy()->addDay();

        $story->forceFill([
            'status' => $publishAt->isFuture() ? Story::STATUS_SCHEDULED : Story::STATUS_PUBLISHED,
            'publish_at' => $publishAt,
            'expire_at' => $expireAt,
        ])->save();

        if ($publishAt->isFuture()) {
            $story->status = Story::STATUS_SCHEDULED;
        } else {
            $story->status = Story::STATUS_PUBLISHED;
        }

        return $story;
    }

    public function markDraft(Story $story): Story
    {
        $story->forceFill([
            'status' => Story::STATUS_DRAFT,
            'publish_at' => null,
            'expire_at' => null,
        ])->save();

        return $story;
    }

    public function expire(Story $story): Story
    {
        $story->forceFill([
            'status' => Story::STATUS_EXPIRED,
            'expire_at' => Carbon::now(),
        ])->save();

        return $story;
    }

    public function delete(Story $story): void
    {
        $story->loadMissing('media');
        $paths = $story->media->flatMap(function (StoryMedia $media) {
            return array_filter([$media->media_path, $media->thumbnail_path]);
        })->all();

        DB::transaction(function () use ($story) {
            $story->forceFill([
                'status' => Story::STATUS_DELETED,
            ])->save();

            $story->media()->delete();
            $story->delete();
        });

        if (!empty($paths)) {
            DB::afterCommit(function () use ($paths) {
                PurgeStoryMedia::dispatch($paths);
            });
        }
    }

    public function deleteMedia(StoryMedia $media): void
    {
        $paths = array_filter([$media->media_path, $media->thumbnail_path]);

        DB::transaction(function () use ($media) {
            $story = $media->story;
            $media->delete();

            $ordered = $story->media()->orderBy('sequence')->get();
            $ordered->values()->each(function (StoryMedia $item, int $index) {
                $item->update(['sequence' => $index + 1]);
            });
        });

        if (!empty($paths)) {
            DB::afterCommit(function () use ($paths) {
                PurgeStoryMedia::dispatch($paths);
            });
        }
    }

    public function recordView(Story $story, ?User $customer, ?string $sessionKey, bool $completed = false): StoryView
    {
        return StoryView::record($story, $customer, $sessionKey, $completed);
    }

    protected function assertStoriesEnabled(?Restaurant $restaurant): void
    {
        if (!$restaurant || !$restaurant->stories_enabled) {
            throw ValidationException::withMessages([
                'restaurant' => __('Restaurant is not permitted to publish stories.'),
            ]);
        }
    }

    protected function determineSequence(Story $story, ?int $requestedSequence): int
    {
        $existing = $story->media()->pluck('sequence')->all();

        if ($requestedSequence !== null) {
            if (in_array($requestedSequence, $existing, true)) {
                throw ValidationException::withMessages([
                    'sequence' => __('validation.unique', ['attribute' => 'sequence']),
                ]);
            }

            return $requestedSequence;
        }

        return empty($existing) ? 1 : (max($existing) + 1);
    }
}
