# Dietary Preference Filtering Implementation Plan

## Overview
This document outlines the implementation plan for adding dietary preference filtering to the food/product system **without removing or breaking** the existing veg/non-veg classification.

**Strategy:** Additive enhancement - new filtering capabilities run alongside existing system.

---

## Current State

### Existing Systems (KEEPING)
- ✅ **Type of Food:** `veg` field (0 = Non-Veg, 1 = Veg)
- ✅ **Restaurant Types:** `veg` and `non_veg` fields
- ✅ **Halal Certification:** `is_halal` field
- ✅ **Allergen System:** `allergies` many-to-many relationship
- ✅ **Nutrition System:** `nutritions` many-to-many relationship

### New System (ADDED)
- ✅ **Dietary Preferences:** `dietary_preferences` table with 19 options
  - Diet Type (8): Vegan, Pescatarian, Egg-Free, Dairy-Free, Sugar-Free, Low-Carb, Keto-Friendly, High-Protein
  - Cultural Preferences (2): Kosher, No Alcohol
  - Allergy-Free (6): Gluten-Free, Nut-Free, Lactose-Free, Soy-Free, Shellfish-Free, Sesame-Free
  - Other (3): Organic, Healthy, Light

---

## Implementation Steps

### STEP 1: Model Layer - Add Query Scope

**File:** `app/Models/Food.php`

**Add scope method after existing relationships:**

```php
/**
 * Scope to filter foods by dietary preferences
 * Uses AND logic - food must have ALL selected preferences
 *
 * @param array $preferenceIds Array of dietary_preference IDs
 */
public function scopeWithDietaryPreferences($query, $preferenceIds)
{
    if (empty($preferenceIds) || !is_array($preferenceIds)) {
        return $query;
    }

    // Filter foods that have ALL selected dietary preferences
    foreach ($preferenceIds as $prefId) {
        $query->whereHas('dietaryPreferences', function($q) use ($prefId) {
            $q->where('dietary_preferences.id', $prefId);
        });
    }

    return $query;
}
```

**Why AND logic?**
- User selects "Vegan + Gluten-Free" → wants foods that are BOTH
- OR logic would show "Vegan OR Gluten-Free" → too broad

---

### STEP 2: Logic Classes - Add Parameter Support

#### 2.1 ProductLogic Class

**File:** `app/CentralLogics/ProductLogic.php`

**Methods to update:**

##### A. `get_latest_products()` (Line ~29)
```php
public static function get_latest_products(
    $zone_id,
    $limit = 10,
    $offset = 1,
    $type = 'all',
    $category_id = null,
    $restaurant_id = null,
    $dietary_preferences = null  // ADD THIS
) {
    return Food::active()
        ->when(isset($category_id), function ($query) use ($category_id) {
            $query->whereHas('category', function ($q) use ($category_id) {
                return $q->whereId($category_id)->orWhere('parent_id', $category_id);
            });
        })
        ->when($type != 'all', function ($query) use ($type) {
            return $query->type($type);  // Keep existing veg filter
        })
        ->when($dietary_preferences, function($query) use ($dietary_preferences){
            return $query->withDietaryPreferences($dietary_preferences);  // NEW
        })
        ->when(isset($restaurant_id), function ($query) use ($restaurant_id) {
            return $query->where('restaurant_id', $restaurant_id);
        })
        ->latest()
        ->paginate($limit, ['*'], 'page', $offset);
}
```

**Repeat for these methods:**
- `get_popular_products()` (Line ~95)
- `search_products()` (Line ~165)
- `get_recommended_products()` (Line ~261)
- `get_most_reviewed_products()` (Line ~323)
- `get_discounted_products()` (Line ~544)

**Pattern:** Add parameter → Add ->when() clause with withDietaryPreferences()

---

#### 2.2 CategoryLogic Class

**File:** `app/CentralLogics/CategoryLogic.php`

**Method to update:** `products()` (Line ~24)

```php
public static function products($id, $zone_id, $additional_data = null)
{
    // Extract dietary preferences
    $dietary_preferences = $additional_data['dietary_preferences'] ?? null;

    $products = Food::withoutGlobalScope(RestaurantScope::class)
        ->active()
        ->when($additional_data['category_id'], function ($query) use ($additional_data) {
            $query->whereHas('category', function ($q) use ($additional_data) {
                return $q->whereId($additional_data['category_id'])
                    ->orWhere('parent_id', $additional_data['category_id']);
            });
        })
        ->when($additional_data['veg'] == 1 && config('toggle_veg_non_veg'), function ($query) {
            return $query->where('veg', 1);  // Keep existing
        })
        ->when($additional_data['non_veg'] == 1 && config('toggle_veg_non_veg'), function ($query) {
            return $query->where('veg', 0);  // Keep existing
        })
        ->when($dietary_preferences, function($query) use ($dietary_preferences){
            return $query->withDietaryPreferences($dietary_preferences);  // NEW
        })
        // ... rest of query
}
```

---

#### 2.3 RestaurantLogic Class (Optional Enhancement)

**File:** `app/CentralLogics/RestaurantLogic.php`

**Methods to update:**
- `get_restaurants()` (Line ~24)
- `get_latest_restaurants()` (Line ~173)
- `get_popular_restaurants()` (Line ~252)

**Add filtering:**
```php
->when($dietary_preferences, function($query) use ($dietary_preferences){
    // Show restaurants that have at least one food with ALL selected dietary prefs
    return $query->whereHas('foods', function($foodQuery) use ($dietary_preferences){
        $foodQuery->active()->withDietaryPreferences($dietary_preferences);
    });
})
```

---

### STEP 3: API Controllers - Accept Parameters

#### 3.1 ProductController (API)

**File:** `app/Http/Controllers/Api/V1/ProductController.php`

##### A. `get_latest()` method (Line ~17)
```php
public function get_latest(Request $request)
{
    $dietary_prefs = $request->dietary_preferences
        ? (is_array($request->dietary_preferences)
            ? $request->dietary_preferences
            : explode(',', $request->dietary_preferences))
        : null;

    $products = ProductLogic::get_latest_products(
        zone_id: json_decode($request->header('zoneId'), true),
        limit: $request['limit'],
        offset: $request['offset'],
        type: $type,
        category_id: $request->category_id,
        restaurant_id: $request->restaurant_id,
        dietary_preferences: $dietary_prefs  // NEW
    );

    // ... rest of method
}
```

**Repeat pattern for:**
- `get_popular()` (Line ~45)
- `get_searched_products()` (Line ~125)
- `get_recommended()` (Line ~187)
- `get_most_reviewed()` (Line ~228)
- `get_discounted_products()` (Line ~266)

---

#### 3.2 CategoryController (API)

**File:** `app/Http/Controllers/Api/V1/CategoryController.php`

**Method:** `get_products()` (Line ~60)

```php
public function get_products($id, Request $request)
{
    $dietary_prefs = $request->dietary_preferences
        ? (is_array($request->dietary_preferences)
            ? $request->dietary_preferences
            : explode(',', $request->dietary_preferences))
        : null;

    $additional_data = [
        'zone_id' => json_decode($request->header('zoneId'), true),
        'limit' => $request['limit'] ?? 10,
        'offset' => $request['offset'] ?? 1,
        'type' => $request->query('type', 'all'),
        'category_id' => $id,
        'veg' => $request->veg ?? 0,  // Keep existing
        'non_veg' => $request->non_veg ?? 0,  // Keep existing
        'dietary_preferences' => $dietary_prefs,  // NEW
    ];

    $products = CategoryLogic::products($id, $zone_id, $additional_data);
    // ... rest
}
```

---

### STEP 4: Data Formatting - Include in API Responses

**File:** `app/CentralLogics/Formatting/DataFormatter.php`

**Update `product_data_formatting()` method (around line 75-285):**

#### For multi_data = true (line ~75):
```php
foreach ($data as $item) {
    // ... existing formatting

    // Add dietary preferences to response
    $item['dietary_preferences'] = $item->dietaryPreferences()
        ->select('dietary_preferences.id', 'dietary_preferences.name', 'dietary_preferences.type')
        ->get()
        ->groupBy('type');

    // ... rest of formatting
}
```

#### For single data (line ~230):
```php
// Add dietary preferences to response
$data['dietary_preferences'] = $data->dietaryPreferences()
    ->select('dietary_preferences.id', 'dietary_preferences.name', 'dietary_preferences.type')
    ->get()
    ->groupBy('type');
```

**Response format:**
```json
{
  "id": 123,
  "name": "Vegan Pizza",
  "veg": 1,
  "dietary_preferences": {
    "diet": [
      {"id": 1, "name": "Vegan", "type": "diet"}
    ],
    "allergy": [
      {"id": 11, "name": "Gluten-Free", "type": "allergy"},
      {"id": 12, "name": "Nut-Free", "type": "allergy"}
    ],
    "other": [
      {"id": 17, "name": "Organic", "type": "other"}
    ]
  }
}
```

---

### STEP 5: Admin/Vendor UI - Add Filter Dropdowns

#### 5.1 Admin Product List Page

**File:** `resources/views/admin-views/product/list.blade.php`

**Add filter section (after existing filters, around line 30):**

```blade
<div class="col-md-12 mt-3">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="tio-filter-list"></i> Dietary Preference Filters
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">

                <div class="row g-3">
                    @php($dietaryPrefs = \App\Models\DietaryPreference::active()->get()->groupBy('type'))

                    <div class="col-md-3">
                        <label>Diet Type</label>
                        <select name="dietary_diet[]" class="form-control multiple-select2" multiple>
                            @foreach($dietaryPrefs['diet'] ?? [] as $pref)
                                <option value="{{ $pref->id }}" {{ in_array($pref->id, request('dietary_diet', [])) ? 'selected' : '' }}>
                                    {{ $pref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Cultural Preferences</label>
                        <select name="dietary_cultural[]" class="form-control multiple-select2" multiple>
                            @foreach($dietaryPrefs['religious'] ?? [] as $pref)
                                <option value="{{ $pref->id }}" {{ in_array($pref->id, request('dietary_cultural', [])) ? 'selected' : '' }}>
                                    {{ $pref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Allergy-Free</label>
                        <select name="dietary_allergy[]" class="form-control multiple-select2" multiple>
                            @foreach($dietaryPrefs['allergy'] ?? [] as $pref)
                                <option value="{{ $pref->id }}" {{ in_array($pref->id, request('dietary_allergy', [])) ? 'selected' : '' }}>
                                    {{ $pref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Other</label>
                        <select name="dietary_other[]" class="form-control multiple-select2" multiple>
                            @foreach($dietaryPrefs['other'] ?? [] as $pref)
                                <option value="{{ $pref->id }}" {{ in_array($pref->id, request('dietary_other', [])) ? 'selected' : '' }}>
                                    {{ $pref->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="tio-filter-list"></i> Apply Filters
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary">
                            <i class="tio-clear"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
```

---

#### 5.2 Update Admin FoodController List Method

**File:** `app/Http/Controllers/Admin/FoodController.php`

**Method:** `list()` (find the method that handles product listing)

**Add parameter handling:**
```php
public function list(Request $request)
{
    // Existing filters
    $type = $request->query('type', 'all');
    $category_id = $request->query('category_id');
    $restaurant_id = $request->query('restaurant_id');

    // NEW: Collect all dietary preference filters
    $dietary_prefs = array_merge(
        $request->dietary_diet ?? [],
        $request->dietary_cultural ?? [],
        $request->dietary_allergy ?? [],
        $request->dietary_other ?? []
    );

    $foods = Food::with(['restaurant', 'category'])
        ->when($type == 'veg', fn($q) => $q->where('veg', 1))  // Keep existing
        ->when($type == 'non_veg', fn($q) => $q->where('veg', 0))  // Keep existing
        ->when($category_id, fn($q) => $q->where('category_id', $category_id))
        ->when($restaurant_id, fn($q) => $q->where('restaurant_id', $restaurant_id))
        ->when(!empty($dietary_prefs), fn($q) => $q->withDietaryPreferences($dietary_prefs))  // NEW
        ->latest()
        ->paginate(25);

    return view('admin-views.product.list', compact('foods'));
}
```

---

#### 5.3 Vendor Product List (Same Pattern)

**File:** `app/Http/Controllers/Vendor/FoodController.php`
**File:** `resources/views/vendor-views/product/list.blade.php`

Apply same changes as admin.

---

### STEP 3: API Endpoints - Accept Filter Parameters

#### 3.1 API Request Format

**New API parameter:**
```http
GET /api/v1/products/latest?dietary_preferences[]=1,2,3
GET /api/v1/products/popular?dietary_preferences=1,2,3
```

**Supports both formats:**
- Array: `dietary_preferences[]=1&dietary_preferences[]=2`
- Comma-separated: `dietary_preferences=1,2,3`

---

#### 3.2 ProductController (API) Updates

**File:** `app/Http/Controllers/Api/V1/ProductController.php`

**Helper function (add to top of class):**
```php
private function parseDietaryPreferences($request)
{
    if (!$request->has('dietary_preferences')) {
        return null;
    }

    $prefs = $request->dietary_preferences;

    // Handle array format
    if (is_array($prefs)) {
        return array_map('intval', $prefs);
    }

    // Handle comma-separated string
    if (is_string($prefs)) {
        return array_map('intval', explode(',', $prefs));
    }

    return null;
}
```

**Update methods:**

##### `get_latest()` (Line ~17)
```php
public function get_latest(Request $request)
{
    $dietary_prefs = $this->parseDietaryPreferences($request);

    $products = ProductLogic::get_latest_products(
        zone_id: json_decode($request->header('zoneId'), true),
        limit: $request['limit'],
        offset: $request['offset'],
        type: $type,
        category_id: $request->category_id,
        restaurant_id: $request->restaurant_id,
        dietary_preferences: $dietary_prefs
    );

    // ... rest
}
```

**Apply to all product list methods:**
- `get_popular()`
- `get_searched_products()`
- `get_recommended()`
- `get_most_reviewed()`
- `get_discounted_products()`

---

#### 3.3 CategoryController (API)

**File:** `app/Http/Controllers/Api/V1/CategoryController.php`

**Method:** `get_products()` (Line ~60)

```php
$dietary_prefs = $this->parseDietaryPreferences($request);

$additional_data = [
    'zone_id' => json_decode($request->header('zoneId'), true),
    'limit' => $request['limit'] ?? 10,
    'offset' => $request['offset'] ?? 1,
    'type' => $request->query('type', 'all'),
    'category_id' => $id,
    'veg' => $request->veg ?? 0,
    'non_veg' => $request->non_veg ?? 0,
    'dietary_preferences' => $dietary_prefs,  // NEW
];
```

---

### STEP 4: Response Formatting - Include Dietary Preferences

**File:** `app/CentralLogics/Formatting/DataFormatter.php`

**Update `product_data_formatting()` method:**

#### Multi-data section (around line 100-200):
```php
foreach ($data as $item) {
    // ... existing formatting ...

    // NEW: Add dietary preferences to response
    $item['dietary_preferences'] = $item->dietaryPreferences->map(function($pref) {
        return [
            'id' => $pref->id,
            'name' => $pref->name,
            'type' => $pref->type,
        ];
    })->groupBy('type')->toArray();

    // ... rest of formatting ...
    array_push($storage, $item);
}
```

#### Single-data section (around line 230-290):
```php
// NEW: Add dietary preferences to response
$data['dietary_preferences'] = $data->dietaryPreferences->map(function($pref) {
    return [
        'id' => $pref->id,
        'name' => $pref->name,
        'type' => $pref->type,
    ];
})->groupBy('type')->toArray();
```

---

### STEP 5: Testing Strategy

#### 5.1 Backend Testing

**Test cases:**
1. ✅ Food with no dietary preferences → filters work
2. ✅ Food with 1 dietary preference → matches single filter
3. ✅ Food with multiple preferences → matches AND logic
4. ✅ Combining veg filter + dietary filters → both apply
5. ✅ Empty dietary_preferences param → ignored gracefully

**Test queries:**
```php
// Test 1: Single preference
Food::withDietaryPreferences([1])->get();  // Should filter

// Test 2: Multiple preferences (AND)
Food::withDietaryPreferences([1, 11])->get();  // Must have BOTH

// Test 3: Combined with veg
Food::where('veg', 1)->withDietaryPreferences([1])->get();
```

---

#### 5.2 API Testing

**Test endpoints:**
```bash
# Test 1: Latest products with dietary filter
curl "https://api.hopa.delivery/api/v1/products/latest?dietary_preferences=1,2,3" \
  -H "zoneId: 1"

# Test 2: Combine veg + dietary
curl "https://api.hopa.delivery/api/v1/products/latest?type=veg&dietary_preferences=1" \
  -H "zoneId: 1"

# Test 3: Category products with dietary filter
curl "https://api.hopa.delivery/api/v1/categories/products/5?dietary_preferences[]=1&dietary_preferences[]=11" \
  -H "zoneId: 1"
```

**Expected response:**
```json
{
  "products": [
    {
      "id": 123,
      "name": "Vegan Pizza",
      "veg": 1,
      "dietary_preferences": {
        "diet": [{"id": 1, "name": "Vegan", "type": "diet"}],
        "allergy": [{"id": 11, "name": "Gluten-Free", "type": "allergy"}]
      }
    }
  ]
}
```

---

#### 5.3 UI Testing

**Admin panel:**
1. Go to Products → List
2. Select "Vegan" from Diet Type filter
3. Select "Gluten-Free" from Allergy-Free filter
4. Click "Apply Filters"
5. Verify only foods with BOTH tags appear

**Test combinations:**
- Veg type + Dietary preferences
- Search + Dietary preferences
- Category + Dietary preferences
- All filters together

---

## 📁 Files Summary

### Files to Modify (12 total)

**Models (1):**
- `app/Models/Food.php`

**Logic Classes (3):**
- `app/CentralLogics/ProductLogic.php`
- `app/CentralLogics/CategoryLogic.php`
- `app/CentralLogics/Formatting/DataFormatter.php`

**API Controllers (2):**
- `app/Http/Controllers/Api/V1/ProductController.php`
- `app/Http/Controllers/Api/V1/CategoryController.php`

**Admin Controllers (2):**
- `app/Http/Controllers/Admin/FoodController.php`
- `app/Http/Controllers/Vendor/FoodController.php`

**Views (4):**
- `resources/views/admin-views/product/list.blade.php`
- `resources/views/vendor-views/product/list.blade.php`
- `resources/views/vendor-views/product/out_of_stock_list.blade.php` (optional)
- Consider: Restaurant list pages if adding restaurant filtering

---

## 🎯 Implementation Order

### Phase 1: Core Functionality (Backend)
1. Add `scopeWithDietaryPreferences()` to Food model
2. Update ProductLogic methods
3. Update CategoryLogic methods
4. Test queries in tinker

### Phase 2: API Integration
5. Update API ProductController
6. Update API CategoryController
7. Update DataFormatter for responses
8. Test API endpoints

### Phase 3: Admin UI
9. Add filters to admin product list page
10. Update admin FoodController list method
11. Add filters to vendor product list page
12. Update vendor FoodController list method

### Phase 4: Validation
13. Test all combinations
14. Verify backward compatibility
15. Update API documentation (regenerate Scribe)

---

## 🔒 Safety Guarantees

**What's protected:**
- ✅ Existing veg/non-veg field **stays untouched**
- ✅ Existing API endpoints **keep working**
- ✅ Existing mobile apps **no changes needed**
- ✅ Database schema **only additions, no removals**
- ✅ Restaurant veg/non_veg **unchanged**

**What's added:**
- ✅ New filtering capability (optional to use)
- ✅ Richer product categorization
- ✅ Better customer experience

---

## 📊 Example Use Cases

### Use Case 1: Customer Looking for Vegan, Gluten-Free Options
```
API Call:
GET /api/v1/products/latest?dietary_preferences=1,11
(1 = Vegan, 11 = Gluten-Free)

Returns: Only products tagged with BOTH Vegan AND Gluten-Free
```

### Use Case 2: Admin Filtering Products
```
Admin UI:
- Type: Veg (existing filter)
- Diet Type: Vegan
- Allergy-Free: Gluten-Free, Nut-Free

Result: Veg items that are Vegan, Gluten-Free, AND Nut-Free
```

### Use Case 3: Restaurant Showing Their Menu
```
Restaurant has:
- 10 Vegan items
- 5 Gluten-Free items
- 3 items that are BOTH

Customer filters by "Vegan + Gluten-Free":
→ Shows the 3 items that match both
```

---

## 🚀 Deployment Strategy

### Local Development
1. Implement all changes locally
2. Test thoroughly
3. Verify no existing features broken

### Production Deployment
1. Push code changes
2. Wait for GitHub Actions deployment
3. No migration needed (dietary_preferences already exists)
4. Clear caches
5. Test filters in admin panel
6. Monitor API usage

---

## 📝 Future Enhancements (Optional)

### Phase 2 Features
- Add dietary preference filtering to restaurant list
- Add "Popular Dietary Tags" analytics
- Auto-suggest dietary tags based on ingredients
- Customer dietary profile (save preferences)

### Phase 3 Features
- Smart recommendations based on dietary preferences
- Dietary preference badges in product cards
- Filter presets (e.g., "Keto-Friendly Bundle")

---

## ✅ Success Metrics

**After implementation:**
- ✅ Can filter products by any combination of dietary preferences
- ✅ API supports dietary_preferences parameter
- ✅ Admin panel has rich filtering UI
- ✅ All existing veg/non-veg functionality still works
- ✅ No breaking changes
- ✅ Mobile apps continue working without updates

---

## 🎯 Ready to Implement

**Total effort:** ~4-6 hours
**Files to modify:** 12
**Breaking changes:** 0
**Risk level:** Low (additive only)

**Next step:** Implement Phase 1 (Backend core functionality)
