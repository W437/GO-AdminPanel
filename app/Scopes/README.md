# Scopes

## Purpose
Eloquent query scopes that automatically filter database queries. Scopes provide reusable query constraints applied globally or locally to model queries.

## Files

### `RestaurantScope.php`
Global scope that automatically filters queries by the current restaurant context.

**Purpose:** Implements multi-tenancy by restricting data access to the authenticated restaurant.

**Behavior:**
- Automatically adds `where('restaurant_id', '=', $currentRestaurantId)` to all queries
- Ensures vendors only see their own data
- Critical security feature for multi-tenant architecture

### `ZoneScope.php`
Global scope for filtering data by service zone.

**Purpose:** Restricts queries to specific geographical zones.

**Behavior:**
- Filters data based on active zone context
- Useful for location-based services
- Ensures zone-specific operations

## How Scopes Work

### Global Scopes
Applied automatically to ALL queries for a model:

```php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RestaurantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Get current restaurant from auth or session
        $restaurantId = auth()->user()?->restaurant_id;

        if ($restaurantId) {
            $builder->where('restaurant_id', $restaurantId);
        }
    }
}
```

### Applying Global Scopes

In your model:
```php
namespace App\Models;

use App\Scopes\RestaurantScope;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new RestaurantScope);
    }
}
```

Now all queries automatically filter by restaurant:
```php
// Automatically includes: WHERE restaurant_id = [current_restaurant_id]
$foods = Food::all();
$food = Food::find(1);
$popularFoods = Food::where('popular', true)->get();
```

### Removing Global Scopes

When you need to bypass the scope:
```php
// Remove specific scope
$allFoods = Food::withoutGlobalScope(RestaurantScope::class)->get();

// Remove all global scopes
$allFoods = Food::withoutGlobalScopes()->get();

// Remove specific scopes by array
$foods = Food::withoutGlobalScopes([
    RestaurantScope::class,
    ZoneScope::class
])->get();
```

## Local Scopes

Defined in models, called manually:

```php
// In Model
public function scopeActive($query)
{
    return $query->where('status', 'active');
}

public function scopePopular($query)
{
    return $query->where('popular', true);
}

public function scopeInPriceRange($query, $min, $max)
{
    return $query->whereBetween('price', [$min, $max]);
}

// Usage
$activeFoods = Food::active()->get();
$popularActive = Food::active()->popular()->get();
$affordableFoods = Food::inPriceRange(10, 50)->get();
```

## Multi-Tenancy with Scopes

The `RestaurantScope` implements the **tenant isolation** pattern:

### Security Benefits
```php
// Vendor A (restaurant_id = 1) is logged in

// This query ONLY returns foods from restaurant 1
$foods = Food::all();

// Even direct ID access is protected
$food = Food::find(999); // Returns null if food belongs to different restaurant

// Relationships are also protected
$restaurant = Restaurant::find(auth()->user()->restaurant_id);
$orders = $restaurant->orders; // Only this restaurant's orders
```

### Preventing Data Leakage
Without scopes, you'd need manual filtering everywhere:
```php
// Without scope - DANGEROUS (can forget)
$foods = Food::where('restaurant_id', auth()->user()->restaurant_id)->get();

// With scope - SAFE (automatic)
$foods = Food::all();
```

## Creating Custom Global Scopes

```bash
# No artisan command, create manually
```

**Anonymous Global Scope:**
```php
protected static function booted()
{
    static::addGlobalScope('active', function (Builder $builder) {
        $builder->where('active', true);
    });
}
```

**Class-Based Global Scope:**
```php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('active', true);
    }
}

// In model
protected static function booted()
{
    static::addGlobalScope(new ActiveScope);
}
```

## Advanced Scope Patterns

### Conditional Global Scope
```php
public function apply(Builder $builder, Model $model)
{
    // Only apply if user is not admin
    if (!auth()->user()?->isAdmin()) {
        $builder->where('restaurant_id', auth()->user()->restaurant_id);
    }
}
```

### Soft Delete Override
```php
public function apply(Builder $builder, Model $model)
{
    // Show only non-deleted for regular users
    if (!auth()->user()?->isAdmin()) {
        $builder->whereNull('deleted_at');
    }
}
```

### Relationship Scopes
```php
// In Restaurant model
public function foods()
{
    return $this->hasMany(Food::class)
                ->where('available', true)
                ->orderBy('popularity', 'desc');
}
```

## Debugging Scopes

### View Generated SQL
```php
// See the SQL with scope applied
$query = Food::active()->toSql();
dd($query);

// Check bindings
$query = Food::active();
dd($query->toSql(), $query->getBindings());
```

### Logging Queries
```php
\DB::enableQueryLog();
$foods = Food::all();
dd(\DB::getQueryLog());
```

## Best Practices
- **Security First:** Use global scopes for tenant isolation
- **Be Explicit:** Document which models have global scopes
- **Test Thoroughly:** Ensure scopes don't break existing queries
- **Performance:** Global scopes run on EVERY query - keep them fast
- **Removal Access:** Provide admin bypass when needed
- **Relationships:** Consider scope impact on eager loading
- **Naming:** Use clear names (RestaurantScope, not TenantScope)
- **Local Scopes:** Use for reusable query patterns
- **Chain Scopes:** Local scopes should return $query for chaining

## Common Pitfalls

### Forgetting to Remove Scopes for Admin
```php
// Bad - Admin can't see all restaurants
$restaurants = Restaurant::all();

// Good - Admin sees everything
$restaurants = Restaurant::withoutGlobalScope(RestaurantScope::class)->get();
```

### N+1 Queries with Scopes
```php
// Can cause issues if relationship models also have scopes
$restaurants = Restaurant::with('foods')->get();

// Be aware of scope interaction
```

### Creating/Updating Models
```php
// Global scopes don't affect insert/update
// You still need to set restaurant_id manually
Food::create([
    'name' => 'Pizza',
    'restaurant_id' => auth()->user()->restaurant_id, // Still required!
]);
```

## Testing Scopes

```php
use Tests\TestCase;
use App\Models\Food;
use App\Scopes\RestaurantScope;

class RestaurantScopeTest extends TestCase
{
    public function test_filters_by_restaurant()
    {
        $restaurant1 = Restaurant::factory()->create();
        $restaurant2 = Restaurant::factory()->create();

        Food::factory()->create(['restaurant_id' => $restaurant1->id]);
        Food::factory()->create(['restaurant_id' => $restaurant2->id]);

        $this->actingAs($restaurant1->owner);

        $foods = Food::all();

        $this->assertCount(1, $foods);
        $this->assertEquals($restaurant1->id, $foods->first()->restaurant_id);
    }

    public function test_can_bypass_scope()
    {
        Food::factory()->count(5)->create();

        $this->assertCount(5, Food::withoutGlobalScopes()->get());
    }
}
```
