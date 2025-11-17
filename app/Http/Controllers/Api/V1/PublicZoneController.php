<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class PublicZoneController extends Controller
{
    /**
     * List All Zones
     *
     * Retrieve all available delivery zones with restaurant counts.
     * Useful for zone exploration page on public website.
     *
     * @group Public Zone API
     * @unauthenticated
     *
     * @response 200 {
     *   "zones": [
     *     {
     *       "id": 1,
     *       "name": "Tel Aviv Central",
     *       "display_name": "Tel Aviv - Center",
     *       "restaurant_count": 45,
     *       "minimum_shipping_charge": 10.00,
     *       "per_km_shipping_charge": 2.50,
     *       "maximum_shipping_charge": 50.00,
     *       "status": 1
     *     },
     *     {
     *       "id": 2,
     *       "name": "Haifa",
     *       "display_name": "Haifa",
     *       "restaurant_count": 23,
     *       "minimum_shipping_charge": 12.00,
     *       "per_km_shipping_charge": 3.00,
     *       "maximum_shipping_charge": 60.00,
     *       "status": 1
     *     }
     *   ],
     *   "total_zones": 2
     * }
     */
    public function index(): JsonResponse
    {
        $zones = Zone::where('status', 1)
            ->withCount(['restaurants' => function ($query) {
                $query->where('status', 1)->where('active', 1);
            }])
            ->get()
            ->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'display_name' => $zone->display_name ?? $zone->name,
                    'restaurant_count' => $zone->restaurants_count,
                    'minimum_shipping_charge' => (float) $zone->minimum_shipping_charge,
                    'per_km_shipping_charge' => (float) $zone->per_km_shipping_charge,
                    'maximum_shipping_charge' => (float) $zone->maximum_shipping_charge,
                    'increased_delivery_fee' => (float) $zone->increased_delivery_fee,
                    'increased_delivery_fee_status' => (bool) $zone->increased_delivery_fee_status,
                    'status' => $zone->status,
                ];
            });

        return response()->json([
            'zones' => $zones,
            'total_zones' => $zones->count(),
        ], 200);
    }

    /**
     * Get Zone Details
     *
     * Retrieve detailed information about a specific zone including
     * delivery charges, coverage area, and active restaurants.
     *
     * @group Public Zone API
     * @unauthenticated
     *
     * @urlParam id integer required The zone ID. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Tel Aviv Central",
     *   "display_name": "Tel Aviv - Center",
     *   "minimum_shipping_charge": 10.00,
     *   "per_km_shipping_charge": 2.50,
     *   "maximum_shipping_charge": 50.00,
     *   "max_cod_order_amount": 500.00,
     *   "increased_delivery_fee": 5.00,
     *   "increased_delivery_fee_status": true,
     *   "increase_delivery_charge_message": "Due to high demand",
     *   "status": 1,
     *   "restaurant_count": 45,
     *   "restaurants": [
     *     {
     *       "id": 1,
     *       "name": "Pizza Palace",
     *       "slug": "pizza-palace",
     *       "rating": "4.5",
     *       "delivery_time": "30-45"
     *     }
     *   ]
     * }
     *
     * @response 404 {
     *   "errors": [
     *     {"code": "zone", "message": "Zone not found"}
     *   ]
     * }
     */
    public function show(int $id): JsonResponse
    {
        $zone = Zone::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$zone) {
            return response()->json([
                'errors' => [
                    ['code' => 'zone', 'message' => translate('messages.zone_not_found')]
                ]
            ], 404);
        }

        // Get active restaurants in this zone
        $restaurants = Restaurant::where('zone_id', $id)
            ->where('status', 1)
            ->where('active', 1)
            ->select(['id', 'name', 'slug', 'logo', 'rating', 'delivery_time', 'minimum_order'])
            ->limit(20) // Limit to prevent huge responses
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'slug' => $restaurant->slug,
                    'logo' => $restaurant->logo,
                    'rating' => $restaurant->rating,
                    'delivery_time' => $restaurant->delivery_time,
                    'minimum_order' => (float) $restaurant->minimum_order,
                ];
            });

        return response()->json([
            'id' => $zone->id,
            'name' => $zone->name,
            'display_name' => $zone->display_name ?? $zone->name,
            'minimum_shipping_charge' => (float) $zone->minimum_shipping_charge,
            'per_km_shipping_charge' => (float) $zone->per_km_shipping_charge,
            'maximum_shipping_charge' => (float) $zone->maximum_shipping_charge,
            'max_cod_order_amount' => (float) $zone->max_cod_order_amount,
            'increased_delivery_fee' => (float) $zone->increased_delivery_fee,
            'increased_delivery_fee_status' => (bool) $zone->increased_delivery_fee_status,
            'increase_delivery_charge_message' => $zone->increase_delivery_charge_message,
            'status' => $zone->status,
            'restaurant_count' => $restaurants->count(),
            'restaurants' => $restaurants,
        ], 200);
    }
}
