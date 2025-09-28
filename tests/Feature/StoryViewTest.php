<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\Story;
use App\Models\StoryMedia;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StoryViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_endpoint_records_guest_view(): void
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
            'title' => 'Evening specials',
            'status' => Story::STATUS_PUBLISHED,
            'publish_at' => Carbon::now()->subMinutes(10),
            'expire_at' => Carbon::now()->addDay(),
        ]);

        StoryMedia::create([
            'story_id' => $story->id,
            'sequence' => 1,
            'media_type' => 'image',
            'media_path' => 'stories/story.jpg',
            'duration_seconds' => 5,
        ]);

        $response = $this->postJson("/api/v1/stories/{$story->id}/view", [
            'session_key' => 'guest-session',
            'completed' => true,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Story view recorded.']);

        $this->assertDatabaseHas('story_views', [
            'story_id' => $story->id,
            'completed' => true,
        ]);
    }
}
