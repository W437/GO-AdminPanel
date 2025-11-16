<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StoryFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_story_feed_returns_published_stories(): void
    {
        config(['stories.enabled' => true]);
        $this->withoutMiddleware();

        $vendor = Vendor::factory()->create();
        $restaurant = Restaurant::factory()->create([
            'vendor_id' => $vendor->id,
            'stories_enabled' => true,
            'status' => 1,
        ]);

        $overlays = [
            [
                'id' => 'overlay-1',
                'text' => 'Limited time combo',
                'position' => ['x' => 0.5, 'y' => 0.4],
                'scale' => 1.2,
                'rotation' => 0,
                'fontFamily' => 'Directional',
                'stylePreset' => 'directional',
                'color' => '#FFFFFFFF',
                'backgroundColor' => '#000000CC',
                'backgroundMode' => 'pill',
                'alignment' => 'center',
                'zIndex' => 1,
            ],
        ];

        $story = Story::create([
            'restaurant_id' => $restaurant->id,
            'title' => 'Happy hour',
            'status' => Story::STATUS_PUBLISHED,
            'publish_at' => Carbon::now()->subHour(),
            'expire_at' => Carbon::now()->addHour(),
            'type' => 'image',
            'media_url' => 'https://example.com/stories/test-image.jpg',
            'thumbnail_url' => 'https://example.com/stories/test-image-thumb.jpg',
            'duration_seconds' => 5,
            'overlays' => $overlays,
            'has_overlays' => true,
        ]);

        StoryMedia::create([
            'story_id' => $story->id,
            'sequence' => 1,
            'media_type' => 'image',
            'media_path' => 'stories/test-image.jpg',
            'duration_seconds' => 5,
        ]);

        $response = $this->getJson('/api/v1/stories');

        $response->assertOk()
            ->assertJsonFragment(['id' => $story->id])
            ->assertJsonStructure([
                'data' => [
                    [
                        'restaurant' => ['id', 'name', 'zone_id', 'logo_url'],
                        'stories' => [
                            [
                                'id',
                                'type',
                                'media_url',
                                'thumbnail_url',
                                'duration_seconds',
                                'has_overlays',
                                'overlays',
                                'media' => [
                                    ['id', 'sequence', 'type', 'media_url'],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->assertJsonPath('data.0.stories.0.has_overlays', true)
            ->assertJsonPath('data.0.stories.0.overlays.0.text', 'Limited time combo')
            ->assertJsonPath('data.0.stories.0.overlays.0.position.x', 0.5)
            ->assertJsonPath('data.0.stories.0.media.0.type', 'image');
    }
}
