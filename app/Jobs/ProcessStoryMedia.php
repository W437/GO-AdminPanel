<?php

namespace App\Jobs;

use App\Models\StoryMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStoryMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(private readonly int $storyMediaId)
    {
    }

    public function handle(): void
    {
        $media = StoryMedia::find($this->storyMediaId);

        if (!$media) {
            return;
        }

        if ($media->media_type === 'image') {
            $this->ensureDuration($media);
            return;
        }

        if ($media->media_type === 'video') {
            $this->ensureDuration($media);

            if (!config('stories.enable_video_processing', true)) {
                return;
            }

            Log::debug('Story video media queued for processing', ['story_media_id' => $media->id]);
        }
    }

    protected function ensureDuration(StoryMedia $media): void
    {
        if ($media->duration_seconds > 0) {
            return;
        }

        $media->update([
            'duration_seconds' => (int) config('stories.default_duration', 5),
        ]);
    }
}
