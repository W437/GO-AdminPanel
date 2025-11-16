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
        $overlays = $this->normalizeOverlays($attributes['overlays'] ?? null);
        $duration = $this->clampDuration($attributes['duration_seconds'] ?? null);

        return Story::create([
            'restaurant_id' => $restaurant->id,
            'title' => $attributes['title'] ?? null,
            'status' => Story::STATUS_DRAFT,
            'type' => $attributes['type'] ?? null,
            'media_url' => $attributes['media_url'] ?? null,
            'thumbnail_url' => $attributes['thumbnail_url'] ?? null,
            'duration_seconds' => $duration,
            'overlays' => !empty($overlays) ? $overlays : null,
            'has_overlays' => !empty($overlays),
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

        $this->syncStoryPrimaryMedia($story);

        DB::afterCommit(function () use ($media) {
            ProcessStoryMedia::dispatch($media->id);
        });

        return $media->fresh();
    }

    public function publish(Story $story, ?Carbon $publishAt = null): Story
    {
        $this->assertStoriesEnabled($story->restaurant);

        $hasDirectMedia = !empty($story->media_url);

        if ($story->media()->count() === 0 && !$hasDirectMedia) {
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

            $this->syncStoryPrimaryMedia($story);
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

    public function updateOverlays(Story $story, $input): Story
    {
        $normalized = $this->normalizeOverlays($input);

        $story->forceFill([
            'overlays' => !empty($normalized) ? $normalized : null,
            'has_overlays' => !empty($normalized),
        ])->save();

        return $story->fresh();
    }

    public function updateStoryMediaMetadata(Story $story, array $attributes = []): Story
    {
        $updates = [];

        if (array_key_exists('type', $attributes)) {
            $updates['type'] = $attributes['type'];
        }

        if (array_key_exists('media_url', $attributes)) {
            $updates['media_url'] = $attributes['media_url'];
        }

        if (array_key_exists('thumbnail_url', $attributes)) {
            $updates['thumbnail_url'] = $attributes['thumbnail_url'];
        }

        if (array_key_exists('duration_seconds', $attributes)) {
            $updates['duration_seconds'] = $this->clampDuration($attributes['duration_seconds']);
        }

        if (!empty($updates)) {
            $story->forceFill($updates)->save();
        }

        return $story->fresh();
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

    protected function syncStoryPrimaryMedia(Story $story): void
    {
        $primary = $story->media()->orderBy('sequence')->first();

        if (!$primary) {
            $story->forceFill([
                'type' => null,
                'media_url' => null,
                'thumbnail_url' => null,
                'duration_seconds' => (int) config('stories.default_duration', 5),
            ])->save();

            return;
        }

        $story->forceFill([
            'type' => $primary->media_type,
            'media_url' => $primary->media_url,
            'thumbnail_url' => $primary->thumbnail_url,
            'duration_seconds' => $primary->duration_seconds,
        ])->save();
    }

    protected function normalizeOverlays($input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            $input = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        if (!is_array($input)) {
            return [];
        }

        $normalized = [];

        foreach ($input as $overlay) {
            if (!is_array($overlay)) {
                continue;
            }

            $text = isset($overlay['text']) ? trim((string) $overlay['text']) : '';

            if ($text === '') {
                continue;
            }

            $position = $this->formatOverlayPosition($overlay['position'] ?? null);
            $scale = $this->formatOverlayNumber($overlay['scale'] ?? null, 0.1, 10, 4);
            $rotation = $this->formatOverlayNumber($overlay['rotation'] ?? null, -360, 360, 2);
            $zIndex = $this->formatOverlayInteger($overlay['zIndex'] ?? null, 0, 100);

            $entry = [
                'id' => $overlay['id'] ?? null,
                'text' => $text,
                'position' => $position,
                'scale' => $scale,
                'rotation' => $rotation,
                'fontFamily' => $overlay['fontFamily'] ?? null,
                'stylePreset' => $overlay['stylePreset'] ?? null,
                'color' => $overlay['color'] ?? null,
                'backgroundColor' => $overlay['backgroundColor'] ?? null,
                'backgroundMode' => $overlay['backgroundMode'] ?? null,
                'alignment' => $overlay['alignment'] ?? null,
                'zIndex' => $zIndex,
            ];

            foreach ($entry as $key => $value) {
                if ($value === null) {
                    unset($entry[$key]);
                }
            }

            $normalized[] = $entry;
        }

        return $normalized;
    }

    protected function formatOverlayPosition($position): ?array
    {
        if (!is_array($position)) {
            return null;
        }

        $formatted = [];

        if (array_key_exists('x', $position)) {
            $formatted['x'] = $this->clamp((float) $position['x'], 0, 1);
        }

        if (array_key_exists('y', $position)) {
            $formatted['y'] = $this->clamp((float) $position['y'], 0, 1);
        }

        return empty($formatted) ? null : $formatted;
    }

    protected function formatOverlayNumber($value, float $min, float $max, int $precision): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = $this->clamp((float) $value, $min, $max);

        return round($number, $precision);
    }

    protected function formatOverlayInteger($value, int $min, int $max): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = (int) round($value);

        return max($min, min($max, $number));
    }

    protected function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }

    protected function clampDuration($value): int
    {
        $default = (int) config('stories.default_duration', 5);
        $max = (int) config('stories.max_duration_seconds', 60);
        $min = 1;

        if ($value === null || $value === '') {
            return $default;
        }

        $int = (int) $value;

        return max($min, min($max, $int));
    }
}
