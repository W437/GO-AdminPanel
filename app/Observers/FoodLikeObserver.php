<?php

namespace App\Observers;

use App\Models\FoodLike;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class FoodLikeObserver
{
    /**
     * Handle the FoodLike "created" event.
     */
    public function created(FoodLike $foodLike): void
    {
        $this->updateLikeCounts($foodLike, 'increment');
    }

    /**
     * Handle the FoodLike "deleted" event.
     */
    public function deleted(FoodLike $foodLike): void
    {
        $this->updateLikeCounts($foodLike, 'decrement');
    }

    /**
     * Update like counts for food and restaurant
     *
     * @param FoodLike $foodLike
     * @param string $action 'increment' or 'decrement'
     */
    private function updateLikeCounts(FoodLike $foodLike, string $action)
    {
        DB::transaction(function () use ($foodLike, $action) {
            // Update food like_count
            $food = Food::withoutGlobalScopes()->find($foodLike->food_id);

            if ($food) {
                if ($action === 'increment') {
                    $food->increment('like_count');
                } else {
                    // Don't go below 0
                    $food->decrement('like_count', 1, ['like_count' => DB::raw('GREATEST(like_count - 1, 0)')]);
                }

                // Update restaurant total_food_likes
                $restaurant = Restaurant::find($food->restaurant_id);

                if ($restaurant) {
                    if ($action === 'increment') {
                        $restaurant->increment('total_food_likes');
                    } else {
                        // Don't go below 0
                        $restaurant->decrement('total_food_likes', 1, ['total_food_likes' => DB::raw('GREATEST(total_food_likes - 1, 0)')]);
                    }
                }
            }
        });
    }
}
