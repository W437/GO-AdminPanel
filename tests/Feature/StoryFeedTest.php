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

        $story = Story::create([
            'restaurant_id' => $restaurant->id,
            'title' => 'Happy hour',
            'status' => Story::STATUS_PUBLISHED,
            'publish_at' => Carbon::now()->subHour(),
            'expire_at' => Carbon::now()->addHour(),
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
                                'media' => [
                                    ['id', 'sequence', 'type', 'media_url'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
