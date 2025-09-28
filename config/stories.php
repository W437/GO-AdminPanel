<?php

return [
    'enabled' => env('STORY_ENABLED', true),
    'max_media_per_story' => (int) env('STORY_MAX_MEDIA', 10),
    'default_duration' => (int) env('STORY_DEFAULT_DURATION', 5),
    'retention_days' => (int) env('STORY_RETENTION_DAYS', 7),
    'media_disk' => env('STORY_MEDIA_DISK', env('FILESYSTEM_DISK', 'public')),
    'enable_video_processing' => env('STORY_ENABLE_VIDEO_PROCESSING', true),
];
