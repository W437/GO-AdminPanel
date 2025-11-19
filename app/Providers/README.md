# Providers

## Purpose
Service providers bootstrap and register services, bind dependencies, and configure the application during the boot process.

## Files

### `AppServiceProvider.php`
Main application bootstrap and service registration.

**Responsibilities:**
- Memory limit configuration
- HTTPS enforcement (production)
- Pagination view settings
- View composer registration (sharing data with views)
- Addon route loading
- Payment gateway status publishing

**Boot Process:**
- Sets PHP memory limits
- Forces HTTPS in production
- Registers global view data
- Loads dynamic addon routes

### `AuthServiceProvider.php`
Authentication and authorization configuration.

**Responsibilities:**
- Policy registration (e.g., `StoryPolicy`)
- Gate definitions
- Custom authentication logic
- Passport/Sanctum setup (if used)

### `RouteServiceProvider.php`
Route registration and configuration.

**Responsibilities:**
- Loading web routes
- Loading API routes
- Route model binding
- Route pattern constraints
- Namespace configuration

### `EventServiceProvider.php`
Event listeners and broadcasting setup.

**Responsibilities:**
- Event-to-listener mapping
- Model observer registration
- Broadcasting channel authorization
- Queue configuration for listeners

**Example:**
```php
protected $listen = [
    OrderPlaced::class => [
        SendOrderNotification::class,
        UpdateInventory::class,
    ],
];
```

### `BroadcastServiceProvider.php`
WebSocket and real-time broadcasting configuration.

**Responsibilities:**
- Broadcasting channel registration
- Pusher/Socket.io configuration
- Private channel authorization

### `FirebaseServiceProvider.php`
Firebase Cloud Messaging (FCM) integration for push notifications.

**Responsibilities:**
- Firebase credentials initialization
- FCM service registration
- Push notification configuration

### `ConfigServiceProvider.php`
Configuration service registration and caching.

**Responsibilities:**
- Registering configuration services
- Config caching optimization
- Dynamic configuration loading

## Provider Lifecycle

### Registration Phase
Runs first - bind services to container:
```php
public function register()
{
    $this->app->singleton(ConfigService::class, function ($app) {
        return new ConfigService();
    });
}
```

### Boot Phase
Runs after all providers registered - perform actions:
```php
public function boot()
{
    // Register policies
    Gate::policy(Story::class, StoryPolicy::class);

    // Share data with views
    View::composer('*', function ($view) {
        $view->with('settings', getWebConfig());
    });
}
```

## Service Container Binding

### Singleton (Shared Instance)
```php
$this->app->singleton(PaymentGateway::class, function ($app) {
    return new PaymentGateway(config('payment.key'));
});
```

### Bind (New Instance Each Time)
```php
$this->app->bind(ReportGenerator::class, function ($app) {
    return new ReportGenerator();
});
```

### Instance (Specific Object)
```php
$config = new Configuration();
$this->app->instance(Configuration::class, $config);
```

## Common Provider Patterns

### View Composers
Share data with all views:
```php
public function boot()
{
    View::composer('*', function ($view) {
        $view->with('user', auth()->user());
    });
}
```

### Route Model Binding
Custom model resolution:
```php
public function boot()
{
    Route::bind('restaurant', function ($value) {
        return Restaurant::where('slug', $value)->firstOrFail();
    });
}
```

### Validation Rules
Custom validation rules:
```php
public function boot()
{
    Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
        return preg_match('/^[0-9]{10}$/', $value);
    });
}
```

### Macros
Extend existing classes:
```php
public function boot()
{
    Collection::macro('toUpper', function () {
        return $this->map(fn($value) => strtoupper($value));
    });
}
```

## Creating New Providers

```bash
# Generate provider
php artisan make:provider PaymentServiceProvider
```

Register in `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\PaymentServiceProvider::class,
],
```

## Provider Registration Order

Providers load in the order listed in `config/app.php`. Laravel's core providers load first, then your custom providers.

**Important:** The `AppServiceProvider` typically loads early and is a good place for general bootstrapping.

## Deferred Providers

Optimize performance by deferring providers:
```php
class PaymentServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [PaymentGateway::class];
    }

    public function register()
    {
        $this->app->singleton(PaymentGateway::class, function () {
            return new PaymentGateway();
        });
    }
}
```

Provider only loads when `PaymentGateway::class` is requested.

## Best Practices
- Keep providers focused on specific concerns
- Use `register()` for bindings, `boot()` for actions
- Defer providers when possible for performance
- Document complex bootstrapping logic
- Use dependency injection in boot method
- Consider environment-specific logic
- Organize related bindings in dedicated providers
- Test provider functionality
- Cache routes and config in production

## Provider Use Cases

### Multi-Tenancy Setup
```php
public function boot()
{
    // Set database connection based on subdomain
    $restaurant = Restaurant::where('subdomain', request()->subdomain)->first();
    if ($restaurant) {
        config(['database.default' => $restaurant->database]);
    }
}
```

### Third-Party Integration
```php
public function register()
{
    $this->app->singleton(StripeClient::class, function () {
        return new StripeClient(config('services.stripe.secret'));
    });
}
```

### Dynamic Configuration
```php
public function boot()
{
    // Load email settings from database
    $mailConfig = MailConfig::first();
    if ($mailConfig) {
        config(['mail.mailers.smtp' => $mailConfig->toArray()]);
    }
}
```
