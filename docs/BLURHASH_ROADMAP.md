# Blurhash Implementation Roadmap

## Overview
Blurhash provides instant loading placeholders for images - tiny (~45 character) strings that decode into beautiful, blurred previews before the actual image loads. This creates a smoother, more professional user experience.

**Configuration**: All blurhash implementations use **5x4 components** consistently across the platform.

---

## ✅ Already Implemented

### 1. Banners
- **Migration**: `2025_11_17_183801_add_blurhash_fields_to_banners_table.php`
- **Migration**: `2025_11_17_191200_increase_blurhash_column_length_in_banners_table.php`
- **Fields Added**:
  - `image_blurhash` (varchar 100)
  - `video_thumbnail_blurhash` (varchar 100)
- **Controller**: `app/Http/Controllers/Admin/BannerController.php`
- **Implementation**: Lines with blurhash generation in store() and update() methods

### 2. Categories
- **Migration**: `2025_11_17_192558_add_image_blurhash_to_categories_table.php`
- **Fields Added**:
  - `image_blurhash` (varchar 100)
- **Controller**: `app/Http/Controllers/Admin/CategoryController.php`
- **Implementation**: Lines 87-90 (store), 199-202 (update)

### 3. Cuisines
- **Migration**: `2025_11_17_192558_add_image_blurhash_to_cuisines_table.php`
- **Fields Added**:
  - `image_blurhash` (varchar 100)
- **Controller**: `app/Http/Controllers/Admin/CuisineController.php`
- **Implementation**: Lines 63-65 (store), 123-126 (update)

### 4. Restaurants
- **Migration**: `2025_11_17_194723_add_image_blurhash_to_restaurants_table.php`
- **Fields Added**:
  - `logo_blurhash` (varchar 100)
  - `cover_photo_blurhash` (varchar 100)
- **Controller**: `app/Http/Controllers/Admin/VendorController.php`
- **Implementation**: Lines 168-174 (store), 375-381 (update)

---

## 🔥 High Priority (Recommended Next)

### 1. Food/Products ⭐⭐⭐⭐⭐
- **Model**: `app/Models/Food.php`
- **Table**: `food`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `product/`
- **Controller**: `app/Http/Controllers/Admin/FoodController.php`
- **Why High Priority**: Food images are shown constantly in menus, search results, and product details - one of the most frequently loaded images
- **User Impact**: Very High - Affects every user browsing the menu

**Implementation Steps**:
```bash
# 1. Create migration
php artisan make:migration add_image_blurhash_to_food_table

# 2. Add to migration up() method:
Schema::table('food', function (Blueprint $table) {
    $table->string('image_blurhash', 100)->nullable()->after('image');
});

# 3. Add to migration down() method:
Schema::table('food', function (Blueprint $table) {
    $table->dropColumn('image_blurhash');
});

# 4. Update FoodController store() method after image upload:
if ($food->image) {
    $food->image_blurhash = Helpers::generate_blurhash('product/', $food->image);
}

# 5. Update FoodController update() method after image update:
if ($request->has('image') && $food->image) {
    $food->image_blurhash = Helpers::generate_blurhash('product/', $food->image);
}
```

---

### 2. Story Media ⭐⭐⭐⭐
- **Model**: `app/Models/StoryMedia.php`
- **Table**: `story_media`
- **Image Field**: `thumbnail_path`
- **Blurhash Field Needed**: `thumbnail_blurhash` (varchar 100, nullable, after `thumbnail_path`)
- **Storage Directory**: Determined by `config('stories.media_disk')`
- **Controller**: Find controller handling story uploads
- **Why High Priority**: Stories need instant loading for smooth browsing experience
- **User Impact**: High - Affects story feature engagement

**Implementation Steps**:
```bash
# 1. Create migration
php artisan make:migration add_thumbnail_blurhash_to_story_media_table

# 2. Add to migration
Schema::table('story_media', function (Blueprint $table) {
    $table->string('thumbnail_blurhash', 100)->nullable()->after('thumbnail_path');
});

# 3. Find story upload controller and add blurhash generation
# Note: Story system may use different storage pattern - verify before implementing
```

---

## 📢 Medium Priority (Marketing Content)

### 3. Campaigns ⭐⭐⭐
- **Model**: `app/Models/Campaign.php`
- **Table**: `campaigns`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `campaign/`
- **Controller**: `app/Http/Controllers/Admin/CampaignController.php`
- **Why Medium Priority**: Time-limited promotional campaigns with banner images
- **User Impact**: Medium-High - Improves promotional content appearance

### 4. Item Campaigns (Flash Sales) ⭐⭐⭐
- **Model**: `app/Models/ItemCampaign.php`
- **Table**: `item_campaigns`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `campaign/`
- **Controller**: Find ItemCampaign controller
- **Why Medium Priority**: Special promotional items with featured images
- **User Impact**: Medium-High - Flash sale browsing experience

### 5. React Promotional Banners (Website) ⭐⭐⭐
- **Model**: `app/Models/ReactPromotionalBanner.php`
- **Table**: `react_promotional_banners`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `react_promotional_banner/`
- **Controller**: Find React promotional banner controller
- **Why Medium Priority**: Landing page hero banners on website
- **User Impact**: Medium - First impression on website

---

## 👤 Lower Priority (Profile Images)

### 6. Users (Customers) ⭐⭐
- **Model**: `app/Models/User.php`
- **Table**: `users`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `profile/`
- **Controller**: User profile controller
- **Why Lower Priority**: Customer profile photos are usually smaller, less critical
- **User Impact**: Low-Medium

### 7. Vendors (Restaurant Owners) ⭐⭐
- **Model**: `app/Models/Vendor.php`
- **Table**: `vendors`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `vendor/`
- **Controller**: Vendor profile controller
- **Why Lower Priority**: Vendor profile photos, not frequently shown
- **User Impact**: Low-Medium

### 8. Delivery Personnel ⭐⭐
- **Model**: `app/Models/DeliveryMan.php`
- **Table**: `delivery_men`
- **Image Fields**: `image`, `identity_image`
- **Blurhash Fields Needed**:
  - `image_blurhash` (varchar 100, nullable, after `image`)
  - `identity_image_blurhash` (varchar 100, nullable, after `identity_image`)
- **Storage Directory**: Verify in controller
- **Controller**: DeliveryMan controller
- **Why Lower Priority**: Delivery person photos and ID verification images
- **User Impact**: Low - Only shown during delivery

---

## 🌐 Lowest Priority (Website Content)

### 9. React Opportunities ⭐
- **Model**: `app/Models/ReactOpportunity.php`
- **Table**: `react_opportunities`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `opportunity_image/`
- **Why Lowest Priority**: Website marketing section images
- **User Impact**: Low - Static website content

### 10. React Services ⭐
- **Model**: `app/Models/ReactService.php`
- **Table**: `react_services`
- **Image Field**: `image`
- **Blurhash Field Needed**: `image_blurhash` (varchar 100, nullable, after `image`)
- **Storage Directory**: `react_service_image/`
- **Why Lowest Priority**: Website service showcase images
- **User Impact**: Low - Static website content

---

## Implementation Pattern (Copy-Paste Template)

### Standard Implementation Steps

**1. Create Migration**
```bash
php artisan make:migration add_image_blurhash_to_{table_name}_table
```

**2. Migration Up Method**
```php
public function up(): void
{
    Schema::table('{table_name}', function (Blueprint $table) {
        $table->string('image_blurhash', 100)->nullable()->after('image');
        // Add more fields if needed (e.g., cover_photo_blurhash)
    });
}
```

**3. Migration Down Method**
```php
public function down(): void
{
    Schema::table('{table_name}', function (Blueprint $table) {
        $table->dropColumn('image_blurhash');
        // Drop all blurhash columns
    });
}
```

**4. Controller Store Method** (Add after image upload)
```php
// After: $model->image = Helpers::upload(...)
if ($model->image && $model->image !== 'def.png') {
    $model->image_blurhash = Helpers::generate_blurhash('{storage_dir}/', $model->image);
}
```

**5. Controller Update Method** (Add after image update)
```php
// After: $model->image = Helpers::update(...)
if ($request->has('image') && $model->image && $model->image !== 'def.png') {
    $model->image_blurhash = Helpers::generate_blurhash('{storage_dir}/', $model->image);
}
```

**6. Run Migration**
```bash
# Locally
php artisan migrate
php artisan schema:dump

# Production (via SSH)
cd /var/www/go-adminpanel
php artisan migrate --force
```

---

## Technical Details

### Blurhash Helper Function
- **Location**: `app/CentralLogics/Helpers.php`
- **Function**: `generate_blurhash(string $dir, string $image_filename, int $components_x = 5, int $components_y = 4)`
- **Features**:
  - Supports both S3 and local storage
  - Uses Intervention/Image for accurate color processing
  - 5x4 component grid (consistent across platform)
  - Returns ~45 character hash string
  - Handles missing files gracefully

### Database Column Specs
- **Type**: `varchar(100)`
- **Nullable**: Yes (for old images and default images)
- **Position**: After the corresponding image column
- **Default**: `NULL`

### Frontend Integration
Frontend should use libraries like:
- React: `react-blurhash`
- React Native: `react-native-blurhash` or `expo-blurhash`
- Vue: `vue-blurhash`

See full frontend implementation guide in deployment notes.

---

## Migration Checklist

When implementing blurhash for a new entity:

- [ ] Create migration with proper column specs
- [ ] Update model if needed (no changes required for standard columns)
- [ ] Find the controller handling image uploads
- [ ] Add blurhash generation to store() method
- [ ] Add blurhash generation to update() method
- [ ] Test locally with image upload
- [ ] Run migration validation script
- [ ] Update schema dump (`php artisan schema:dump`)
- [ ] Commit and push to GitHub
- [ ] Run migration on production
- [ ] Verify blurhash appears in API responses
- [ ] Update frontend implementation documentation

---

## Notes

- **Column Length**: Originally used varchar(50) for banners, but increased to varchar(100) to accommodate 7x6 components. Settled on 5x4 components (~45 chars) for better performance while maintaining quality.
- **Null Values**: Blurhash will be null for:
  - Images uploaded before blurhash was implemented
  - Default images (like `def.png`)
  - Failed uploads
- **S3 Compatibility**: The `generate_blurhash()` helper is fully S3-compatible using `Storage::disk('s3')->get()`
- **Performance**: Blurhash generation adds ~100-200ms to image upload time but improves user experience significantly
- **Color Accuracy**: Switched from GD to Intervention/Image library for accurate color representation

---

## Recommended Implementation Order

1. **Food** - Highest user impact, most frequently loaded ⭐⭐⭐⭐⭐
2. **StoryMedia** - Essential for stories feature ⭐⭐⭐⭐
3. **Campaign** - Improves promotional content ⭐⭐⭐
4. **ItemCampaign** - Flash sale experience ⭐⭐⭐
5. **ReactPromotionalBanner** - Website first impression ⭐⭐⭐
6. Others - Lower priority, implement as needed

---

**Last Updated**: 2025-11-17
**Current Status**: 4 entities implemented (Banners, Categories, Cuisines, Restaurants)
**Next Target**: Food images
