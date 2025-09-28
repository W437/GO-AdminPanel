<?php

namespace App\Console\Commands;

use App\Models\Story;
use App\Services\StoryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoryExpirationCommand extends Command
{
    protected $signature = 'stories:expire';

    protected $description = 'Publish scheduled stories, mark expired stories, and purge soft-deleted records';

    public function handle(StoryService $storyService): int
    {
        $now = Carbon::now();

        $this->publishScheduledStories($storyService, $now);
        $this->expireStories($storyService, $now);
        $this->purgeSoftDeletedStories();

        return Command::SUCCESS;
    }

    protected function publishScheduledStories(StoryService $storyService, Carbon $now): void
    {
        Story::with(['restaurant'])
            ->where('status', Story::STATUS_SCHEDULED)
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->withCount('media')
            ->chunkById(50, function ($stories) use ($storyService) {
                foreach ($stories as $story) {
                    if (($story->media_count ?? 0) === 0) {
                        continue;
                    }

                    $storyService->publish($story, $story->publish_at);
                }
            });
    }

    protected function expireStories(StoryService $storyService, Carbon $now): void
    {
        Story::whereIn('status', [Story::STATUS_PUBLISHED, Story::STATUS_SCHEDULED])
            ->whereNotNull('expire_at')
            ->where('expire_at', '<=', $now)
            ->chunkById(50, function ($stories) use ($storyService) {
                foreach ($stories as $story) {
                    $storyService->expire($story);
                }
            });
    }

    protected function purgeSoftDeletedStories(): void
    {
        $retentionDays = (int) config('stories.retention_days', 7);
        $threshold = Carbon::now()->subDays($retentionDays);

        Story::onlyTrashed()
            ->where('deleted_at', '<=', $threshold)
            ->chunkById(50, function ($stories) {
                foreach ($stories as $story) {
                    DB::transaction(function () use ($story) {
                        $story->forceDelete();
                    });
                }
            });
    }
}
