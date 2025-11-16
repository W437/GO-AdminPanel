<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Services\StoryFeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StoryFeedController extends Controller
{
    public function __construct(private readonly StoryFeedService $feedService)
    {
    }

    public function index(Request $request)
    {
        if (!config('stories.enabled')) {
            return response()->json(['data' => []], 200);
        }

        $zoneId = $request->integer('zone_id');
        $limit = $request->integer('limit', 20);

        $stories = $this->feedService->fetchActiveStories($zoneId, $limit);
        $grouped = $this->groupStoriesByRestaurant($stories);

        return response()->json(['data' => $grouped], 200);
    }

    protected function groupStoriesByRestaurant(Collection $stories): Collection
    {
        return $stories->groupBy('restaurant_id')->values()->map(function (Collection $restaurantStories) {
            /** @var Story $first */
            $first = $restaurantStories->first();
            $restaurant = $first?->restaurant;

            return [
                'restaurant' => $restaurant ? [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'zone_id' => $restaurant->zone_id,
                    'logo_url' => $restaurant->logo_full_url ?? $restaurant->logo,
                ] : null,
                'stories' => $restaurantStories->map(function (Story $story) {
                    return [
                        'id' => $story->id,
                        'title' => $story->title,
                        'status' => $story->status,
                        'publish_at' => optional($story->publish_at)->toIso8601String(),
                        'expire_at' => optional($story->expire_at)->toIso8601String(),
                        'type' => $story->type,
                        'media_url' => $story->media_url,
                        'thumbnail_url' => $story->thumbnail_url,
                        'duration_seconds' => $story->duration_seconds,
                        'has_overlays' => $story->has_overlays,
                        'overlays' => $story->overlays,
                        'media' => $story->media->map(function ($media) {
                            return [
                                'id' => $media->id,
                                'sequence' => $media->sequence,
                                'type' => $media->media_type,
                                'duration_seconds' => $media->duration_seconds,
                                'caption' => $media->caption,
                                'cta_label' => $media->cta_label,
                                'cta_url' => $media->cta_url,
                        'media_url' => $media->media_url,
                        'thumbnail_url' => $media->thumbnail_url,
                        'has_overlays' => $media->has_overlays,
                        'overlays' => $media->overlays,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        });
    }
}
