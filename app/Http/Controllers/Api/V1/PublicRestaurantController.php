<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantSchedule;
use App\Models\Food;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicRestaurantController extends Controller
{
    /**
     * Get Restaurant by Slug
     *
     * Retrieve restaurant details using a SEO-friendly slug instead of ID.
     * This endpoint is specifically for the public restaurant website.
     *
     * @group Public Restaurant API
     * @unauthenticated
     *
     * @urlParam slug string required The restaurant slug. Example: pizza-palace
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Pizza Palace",
     *   "slug": "pizza-palace",
     *   "phone": "+972501234567",
     *   "email": "contact@pizzapalace.com",
     *   "logo": "restaurant-logo.png",
     *   "cover_photo": "restaurant-cover.jpg",
     *   "address": "123 Main St, Tel Aviv",
     *   "latitude": "32.0853",
     *   "longitude": "34.7818",
     *   "rating": "4.5",
     *   "delivery_time": "30-45",
     *   "minimum_order": 50.00,
     *   "delivery_charge": 15.00,
     *   "status": 1,
     *   "active": 1
     * }
     *
     * @response 404 {
     *   "errors": [
     *     {"code": "restaurant", "message": "Restaurant not found"}
     *   ]
     * }
     */
    public function getBySlug(string $slug): JsonResponse
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 1)
            ->where('active', 1)
            ->with(['cuisine', 'restaurant_config'])
            ->first();

        if (!$restaurant) {
            return response()->json([
                'errors' => [
                    ['code' => 'restaurant', 'message' => translate('messages.restaurant_not_found')]
                ]
            ], 404);
        }

        return response()->json($restaurant, 200);
    }

    /**
     * Get Restaurant Operating Hours
     *
     * Retrieve the weekly schedule/operating hours for a specific restaurant.
     * Returns an array of schedules for each day of the week.
     *
     * @group Public Restaurant API
     * @unauthenticated
     *
     * @urlParam id integer required The restaurant ID. Example: 1
     *
     * @response 200 {
     *   "restaurant_id": 1,
     *   "schedules": [
     *     {"day": 0, "day_name": "Sunday", "opening_time": "10:00:00", "closing_time": "22:00:00", "is_open": true},
     *     {"day": 1, "day_name": "Monday", "opening_time": "10:00:00", "closing_time": "22:00:00", "is_open": true},
     *     {"day": 2, "day_name": "Tuesday", "opening_time": "10:00:00", "closing_time": "22:00:00", "is_open": true},
     *     {"day": 3, "day_name": "Wednesday", "opening_time": "10:00:00", "closing_time": "22:00:00", "is_open": true},
     *     {"day": 4, "day_name": "Thursday", "opening_time": "10:00:00", "closing_time": "22:00:00", "is_open": true},
     *     {"day": 5, "day_name": "Friday", "opening_time": "10:00:00", "closing_time": "23:00:00", "is_open": true},
     *     {"day": 6, "day_name": "Saturday", "opening_time": "10:00:00", "closing_time": "23:00:00", "is_open": true}
     *   ],
     *   "currently_open": true
     * }
     *
     * @response 404 {
     *   "errors": [
     *     {"code": "restaurant", "message": "Restaurant not found"}
     *   ]
     * }
     */
    public function getSchedules(int $id): JsonResponse
    {
        $restaurant = Restaurant::where('id', $id)
            ->where('status', 1)
            ->where('active', 1)
            ->first();

        if (!$restaurant) {
            return response()->json([
                'errors' => [
                    ['code' => 'restaurant', 'message' => translate('messages.restaurant_not_found')]
                ]
            ], 404);
        }

        $schedules = RestaurantSchedule::where('restaurant_id', $id)
            ->orderBy('day')
            ->get()
            ->map(function ($schedule) {
                $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

                return [
                    'day' => $schedule->day,
                    'day_name' => $dayNames[$schedule->day] ?? 'Unknown',
                    'opening_time' => $schedule->opening_time,
                    'closing_time' => $schedule->closing_time,
                    'is_open' => $schedule->opening_time && $schedule->closing_time,
                ];
            });

        // Check if currently open
        $currentDay = now()->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $currentTime = now()->format('H:i:s');
        $todaySchedule = $schedules->firstWhere('day', $currentDay);

        $currentlyOpen = false;
        if ($todaySchedule && $todaySchedule['is_open']) {
            $currentlyOpen = $currentTime >= $todaySchedule['opening_time']
                && $currentTime <= $todaySchedule['closing_time'];
        }

        return response()->json([
            'restaurant_id' => $id,
            'schedules' => $schedules->values(),
            'currently_open' => $currentlyOpen,
        ], 200);
    }

    /**
     * Get Complete Restaurant Menu
     *
     * Retrieve the complete menu for a restaurant in a single optimized API call.
     * Returns all categories with their products organized hierarchically.
     *
     * @group Public Restaurant API
     * @unauthenticated
     *
     * @urlParam id integer required The restaurant ID. Example: 1
     *
     * @response 200 {
     *   "restaurant_id": 1,
     *   "restaurant_name": "Pizza Palace",
     *   "categories": [
     *     {
     *       "id": 1,
     *       "name": "Pizzas",
     *       "products_count": 12,
     *       "products": [
     *         {
     *           "id": 101,
     *           "name": "Margherita Pizza",
     *           "description": "Classic pizza with tomato and mozzarella",
     *           "price": 45.00,
     *           "image": "pizza.jpg",
     *           "available": true
     *         }
     *       ]
     *     }
     *   ],
     *   "total_products": 45
     * }
     *
     * @response 404 {
     *   "errors": [
     *     {"code": "restaurant", "message": "Restaurant not found"}
     *   ]
     * }
     */
    public function getMenu(int $id): JsonResponse
    {
        $restaurant = Restaurant::where('id', $id)
            ->where('status', 1)
            ->where('active', 1)
            ->first();

        if (!$restaurant) {
            return response()->json([
                'errors' => [
                    ['code' => 'restaurant', 'message' => translate('messages.restaurant_not_found')]
                ]
            ], 404);
        }

        // Get all categories with their products for this restaurant
        $categories = Category::where('status', 1)
            ->whereHas('products', function ($query) use ($id) {
                $query->where('restaurant_id', $id)
                    ->where('status', 1);
            })
            ->with(['products' => function ($query) use ($id) {
                $query->where('restaurant_id', $id)
                    ->where('status', 1)
                    ->select([
                        'id',
                        'name',
                        'description',
                        'image',
                        'category_id',
                        'price',
                        'discount',
                        'discount_type',
                        'available_time_starts',
                        'available_time_ends',
                        'veg',
                        'recommended',
                        'restaurant_id',
                        'avg_rating'
                    ])
                    ->orderBy('recommended', 'desc')
                    ->orderBy('name');
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                    'products_count' => $category->products->count(),
                    'products' => $category->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => (float) $product->price,
                            'discount' => (float) $product->discount,
                            'discount_type' => $product->discount_type,
                            'image' => $product->image,
                            'veg' => (bool) $product->veg,
                            'recommended' => (bool) $product->recommended,
                            'rating' => $product->avg_rating ? (float) $product->avg_rating : 0,
                            'available_time_starts' => $product->available_time_starts,
                            'available_time_ends' => $product->available_time_ends,
                        ];
                    }),
                ];
            });

        $totalProducts = $categories->sum('products_count');

        return response()->json([
            'restaurant_id' => $id,
            'restaurant_name' => $restaurant->name,
            'categories' => $categories->values(),
            'total_products' => $totalProducts,
        ], 200);
    }
}
