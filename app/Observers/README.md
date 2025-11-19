# Observers

## Purpose
Eloquent model event listeners that respond to model lifecycle events (creating, created, updating, updated, deleting, deleted).

## Files

### `BusinessSettingObserver.php`
Observes the `BusinessSetting` model for configuration changes.

**Listens To:**
- `created` - When a new setting is created
- `updated` - When a setting is modified
- `deleted` - When a setting is removed

**Action:** Refreshes business settings cache to ensure the application uses updated configuration immediately.

### `DataSettingObserver.php`
Observes the `DataSetting` model for data configuration changes.

**Listens To:**
- `created` - When new data setting is created
- `updated` - When data setting is modified
- `deleted` - When data setting is removed

**Action:** Similar cache refresh mechanism to keep data settings synchronized.

## How Observers Work

### Observer Flow
1. Model event occurs (e.g., `$setting->save()`)
2. Laravel dispatches event to registered observer
3. Observer method executes (e.g., `updated()`)
4. Side effects occur (cache refresh, logging, etc.)

### Registration
Observers are registered in `App\Providers\EventServiceProvider`:

```php
protected $observers = [
    BusinessSetting::class => [BusinessSettingObserver::class],
    DataSetting::class => [DataSettingObserver::class],
];
```

Or in a service provider's `boot()` method:
```php
public function boot()
{
    BusinessSetting::observe(BusinessSettingObserver::class);
}
```

## Observer Structure

```php
namespace App\Observers;

class BusinessSettingObserver
{
    public function created(BusinessSetting $setting)
    {
        // Called after model is created
        cache()->forget('business_settings');
    }

    public function updated(BusinessSetting $setting)
    {
        // Called after model is updated
        cache()->forget('business_settings');
    }

    public function deleted(BusinessSetting $setting)
    {
        // Called after model is deleted
        cache()->forget('business_settings');
    }

    // Other available events:
    // public function retrieved(BusinessSetting $setting) {}
    // public function creating(BusinessSetting $setting) {}
    // public function updating(BusinessSetting $setting) {}
    // public function saving(BusinessSetting $setting) {}
    // public function saved(BusinessSetting $setting) {}
    // public function deleting(BusinessSetting $setting) {}
    // public function restoring(BusinessSetting $setting) {}
    // public function restored(BusinessSetting $setting) {}
}
```

## Available Model Events

### Before Events (can prevent action)
- `creating` - Before creating
- `updating` - Before updating
- `saving` - Before saving (create or update)
- `deleting` - Before deleting
- `restoring` - Before restoring soft deleted model

### After Events
- `created` - After created
- `updated` - After updated
- `saved` - After saved (create or update)
- `deleted` - After deleted
- `restored` - After restored
- `retrieved` - After retrieved from database

## Common Use Cases

### Cache Invalidation
```php
public function updated(BusinessSetting $setting)
{
    cache()->forget('business_settings');
}
```

### Logging Changes
```php
public function updated(Order $order)
{
    ActivityLog::create([
        'model' => 'Order',
        'action' => 'updated',
        'changes' => $order->getChanges(),
    ]);
}
```

### Sending Notifications
```php
public function created(Order $order)
{
    $order->customer->notify(new OrderPlaced($order));
}
```

### Maintaining Related Models
```php
public function deleting(Restaurant $restaurant)
{
    // Delete related records
    $restaurant->foods()->delete();
    $restaurant->orders()->update(['status' => 'cancelled']);
}
```

### Auto-generating Values
```php
public function creating(Order $order)
{
    $order->order_number = 'ORD-' . time();
}
```

## Creating New Observers

```bash
# Generate observer
php artisan make:observer OrderObserver --model=Order
```

Then register in `EventServiceProvider`:
```php
protected $observers = [
    Order::class => [OrderObserver::class],
];
```

## Best Practices
- Use observers for cross-cutting concerns (logging, cache, notifications)
- Keep observer logic simple and fast
- Avoid circular dependencies (A observes B, B observes A)
- Be careful with infinite loops (updating model in `updated` event)
- Use queued jobs for heavy processing
- Consider using `retrieved` event sparingly (performance)
- Document side effects clearly
- Test observer behavior thoroughly

## Observer vs. Model Events

### Use Observers When:
- Multiple models need similar event handling
- Event logic is complex and deserves its own class
- You want to keep models clean and focused

### Use Model Events When:
- Logic is simple and model-specific
- Quick one-liners in model's `boot()` method
- Prototyping or temporary solutions

```php
// Model event example
protected static function booted()
{
    static::created(function ($order) {
        cache()->forget('recent_orders');
    });
}
```
