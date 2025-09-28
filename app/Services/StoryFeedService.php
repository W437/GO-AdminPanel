<?php

namespace App\Services;

use App\Models\Story;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;

class StoryFeedService
{
    public function __construct(private readonly CacheRepository $cache)
    {
    }

    public function fetchActiveStories(?int $zoneId = null, int $limit = 20): Collection
    {
        $limit = max(1, min($limit, 100));
        $cacheKey = sprintf('stories:feed:%s:%s', $zoneId ?? 'all', $limit);

        return $this->cache->remember($cacheKey, now()->addSeconds(30), function () use ($zoneId, $limit) {
            $query = Story::query()
                ->with([
                    'media',
                    'restaurant:id,name,zone_id,logo,stories_enabled',
                ])
                ->active()
                ->whereHas('restaurant', function ($restaurantQuery) use ($zoneId) {
                    $restaurantQuery->where('stories_enabled', true)
                        ->where('status', 1);

                    if ($zoneId) {
                        $restaurantQuery->where('zone_id', $zoneId);
                    }
                })
                ->orderByDesc('publish_at')
                ->limit($limit);

            return $query->get();
        });
    }
}
