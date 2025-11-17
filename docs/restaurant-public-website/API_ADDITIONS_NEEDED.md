# API Additions Needed for React Restaurant Website

## Overview
This document outlines the minimal API additions needed in the Laravel backend to fully support the React-based restaurant website with zone exploration feature.

## 1. Zone Endpoints (NEW - For Explore Page)

### A. Get All Zones Endpoint
**File**: `routes/api/v1/api.php`

Add this route in a new zones section:
```php
Route::group(['prefix' => 'zones'], function () {
    Route::get('/', 'Api\V1\ZoneController@index');
    Route::get('/{id}', 'Api\V1\ZoneController@show');
});
```

### Create Zone Controller
**File**: `app/Http/Controllers/Api/V1/ZoneController.php` (NEW FILE)

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $zones = Zone::where('status', 1)
            ->withCount([
                'restaurants' => function ($query) {
                    $query->where('status', 1);
                },
                'restaurants as active_restaurants' => function ($query) {
                    $query->where('status', 1)
                          ->where('active', 1);
                }
            ])
            ->get();

        $zones = $zones->map(function ($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'slug' => \Str::slug($zone->name),
                'coordinates' => json_decode($zone->coordinates),
                'restaurant_count' => $zone->restaurants_count,
                'active_restaurants' => $zone->active_restaurants,
                'delivery_available' => true,
                'minimum_delivery_charge' => $zone->minimum_delivery_charge,
                'per_km_delivery_charge' => $zone->per_km_delivery_charge,
                'maximum_cod_order_amount' => $zone->maximum_cod_order_amount
            ];
        });

        return response()->json([
            'zones' => $zones,
            'total_zones' => $zones->count()
        ]);
    }

    public function show($id)
    {
        $zone = Zone::where('id', $id)
            ->where('status', 1)
            ->withCount([
                'restaurants' => function ($query) {
                    $query->where('status', 1);
                },
                'restaurants as active_restaurants' => function ($query) {
                    $query->where('status', 1)
                          ->where('active', 1);
                }
            ])
            ->firstOrFail();

        return response()->json([
            'id' => $zone->id,
            'name' => $zone->name,
            'slug' => \Str::slug($zone->name),
            'coordinates' => json_decode($zone->coordinates),
            'restaurant_count' => $zone->restaurants_count,
            'active_restaurants' => $zone->active_restaurants,
            'delivery_available' => true,
            'minimum_delivery_charge' => $zone->minimum_delivery_charge,
            'per_km_delivery_charge' => $zone->per_km_delivery_charge,
            'maximum_cod_order_amount' => $zone->maximum_cod_order_amount,
            'increased_delivery_fee' => $zone->increased_delivery_fee,
            'increased_delivery_fee_status' => $zone->increased_delivery_fee_status
        ]);
    }
}
```

### Alternative: If Zone Controller Exists
If you already have a zone controller for other purposes, just add these methods to the existing controller.

## 2. Restaurant Schedules Endpoint

### Route Addition
**File**: `routes/api/v1/api.php`

Add this route in the restaurant routes section (around line 310-323):
```php
Route::get('restaurants/{id}/schedules', 'Api\V1\RestaurantController@getSchedules');
```

### Controller Method
**File**: `app/Http/Controllers/Api/V1/RestaurantController.php`

Add this method:
```php
public function getSchedules($id)
{
    $restaurant = Restaurant::findOrFail($id);

    $schedules = RestaurantSchedule::where('restaurant_id', $id)
        ->select(['day', 'opening_time', 'closing_time'])
        ->orderBy('day')
        ->get();

    // Add current day status
    $currentDay = now()->dayOfWeek; // 0 = Sunday, 6 = Saturday
    $currentTime = now()->format('H:i:s');

    $todaySchedule = $schedules->firstWhere('day', $currentDay);

    $isOpen = false;
    if ($todaySchedule) {
        $isOpen = $currentTime >= $todaySchedule->opening_time &&
                  $currentTime <= $todaySchedule->closing_time;
    }

    return response()->json([
        'schedules' => $schedules,
        'current_status' => [
            'is_open' => $isOpen,
            'current_day' => $currentDay,
            'current_time' => $currentTime,
            'today_schedule' => $todaySchedule
        ]
    ]);
}
```

## 3. Restaurant by Slug Endpoint (REQUIRED)

### Route Addition
**File**: `routes/api/v1/api.php`

Add this route:
```php
Route::get('restaurants/by-slug/{slug}', 'Api\V1\RestaurantController@getBySlug');
```

### Controller Method
**File**: `app/Http/Controllers/Api/V1/RestaurantController.php`

Add this method:
```php
public function getBySlug($slug, Request $request)
{
    $restaurant = Restaurant::where('slug', $slug)
        ->when($request->header('zoneId'), function($query) use ($request) {
            return $query->where('zone_id', $request->header('zoneId'));
        })
        ->active()
        ->firstOrFail();

    // Use existing restaurant details logic
    return $this->get_restaurant_details($restaurant->id, $request);
}
```

## 4. Enhanced Restaurant Details (Include Schedules)

### Modify Existing Method
**File**: `app/Http/Controllers/Api/V1/RestaurantController.php`

Update the `get_restaurant_details` method to include schedules:

```php
public function get_restaurant_details(Request $request, $id)
{
    // ... existing code ...

    $restaurant = Restaurant::withOpen($longitude, $latitude)
        ->with([
            'discount' => function ($q) {
                return $q->validate();
            },
            'cuisine',
            'schedules' // ADD THIS LINE
        ])
        ->withCount('foods')
        ->where(['id' => $id])
        ->first();

    // ... rest of existing code ...

    // ADD: Include current open status
    if ($restaurant) {
        $currentDay = now()->dayOfWeek;
        $currentTime = now()->format('H:i:s');
        $todaySchedule = $restaurant->schedules->firstWhere('day', $currentDay);

        $restaurant->current_opening_status = [
            'is_open' => $todaySchedule ?
                ($currentTime >= $todaySchedule->opening_time &&
                 $currentTime <= $todaySchedule->closing_time) : false,
            'today_schedule' => $todaySchedule
        ];
    }

    // ... rest of existing code ...
}
```

## 5. Restaurant Menu Endpoint (All Categories & Products)

### Route Addition
**File**: `routes/api/v1/api.php`

Add this convenient endpoint:
```php
Route::get('restaurants/{id}/menu', 'Api\V1\RestaurantController@getFullMenu');
```

### Controller Method
**File**: `app/Http/Controllers/Api/V1/RestaurantController.php`

Add this method to get the complete menu in one call:
```php
public function getFullMenu($id, Request $request)
{
    $restaurant = Restaurant::findOrFail($id);

    // Get all active categories used by this restaurant
    $categories = Category::whereHas('products', function($query) use ($id) {
        $query->where('restaurant_id', $id)->where('status', 1);
    })
    ->with(['products' => function($query) use ($id) {
        $query->where('restaurant_id', $id)
              ->where('status', 1)
              ->with(['restaurant', 'tags'])
              ->select([
                  'id', 'name', 'description', 'image', 'price',
                  'category_id', 'restaurant_id', 'veg', 'status',
                  'discount', 'discount_type', 'tax', 'tax_type',
                  'available_time_starts', 'available_time_ends',
                  'variations', 'add_ons', 'attributes', 'choice_options'
              ]);
    }])
    ->where('status', 1)
    ->orderBy('priority', 'desc')
    ->orderBy('name')
    ->get();

    // Transform for better structure
    $menu = $categories->map(function($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image_full_url,
            'products' => $category->products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image' => $product->image_full_url,
                    'price' => $product->price,
                    'discount_price' => $product->discount > 0 ?
                        PriceHelper::get_discounted_price($product) : null,
                    'veg' => $product->veg,
                    'variations' => $product->variations,
                    'add_ons' => $product->add_ons,
                    'available_time_starts' => $product->available_time_starts,
                    'available_time_ends' => $product->available_time_ends,
                ];
            })
        ];
    });

    return response()->json([
        'restaurant_id' => $id,
        'restaurant_name' => $restaurant->name,
        'total_categories' => $menu->count(),
        'total_products' => $menu->sum(function($cat) {
            return $cat['products']->count();
        }),
        'menu' => $menu
    ]);
}
```

## 6. CORS Configuration Update

### Update CORS Settings
**File**: `config/cors.php`

Ensure your React app domain is allowed:
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',    // React development
        'http://localhost:3001',    // Alternative port
        'https://hopa.delivery',     // Production domain
        'https://www.hopa.delivery', // WWW version
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
```

## 7. Testing the New Endpoints

### Test with cURL:

```bash
# Get all zones
curl https://admin.hopa.delivery/api/v1/zones

# Get zone details
curl https://admin.hopa.delivery/api/v1/zones/1

# Get restaurant by slug
curl https://admin.hopa.delivery/api/v1/restaurants/by-slug/pizza-palace

# Get restaurant schedules
curl https://admin.hopa.delivery/api/v1/restaurants/1/schedules

# Get full menu
curl -H "zoneId: 1" \
  https://admin.hopa.delivery/api/v1/restaurants/1/menu
```

### Test with JavaScript:

```javascript
// Get all zones
fetch('https://admin.hopa.delivery/api/v1/zones')
.then(res => res.json())
.then(data => console.log(data));

// Get zone details
fetch('https://admin.hopa.delivery/api/v1/zones/1')
.then(res => res.json())
.then(data => console.log(data));

// Get restaurant by slug (no zone header needed)
fetch('https://admin.hopa.delivery/api/v1/restaurants/by-slug/pizza-palace')
.then(res => res.json())
.then(data => console.log(data));

// Get schedules
fetch('https://admin.hopa.delivery/api/v1/restaurants/1/schedules')
.then(res => res.json())
.then(data => console.log(data));

// Get restaurants in a zone
fetch('https://admin.hopa.delivery/api/v1/restaurants/get-restaurants/all?limit=12&offset=1', {
  headers: { 'zoneId': '1' }
})
.then(res => res.json())
.then(data => console.log(data));
```

## Summary

With these additions:
1. ✅ **Zone exploration** - Browse all zones and see restaurant counts
2. ✅ **Restaurant by slug** - Direct access via clean URLs
3. ✅ **Operating hours/schedules** - Show when restaurants are open
4. ✅ **Complete menu** - Fetch all categories and items in one call
5. ✅ **CORS configured** - React app can communicate with API
6. ✅ **No auth required** - All data is publicly accessible

Total changes needed:
- Add 6-7 new routes (zones, restaurant by slug, schedules, menu)
- Create 1 new controller (ZoneController) or add to existing
- Add 4-5 new methods to RestaurantController
- Update CORS config for your domain
- No database changes required
- No authentication changes required

This backend update will fully support your React-based restaurant website with zone exploration!