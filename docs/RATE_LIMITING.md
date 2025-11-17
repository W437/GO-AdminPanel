# Rate Limiting Configuration

## 📋 Overview

Rate limiting protects your application from abuse, brute-force attacks, and DoS attacks by restricting the number of requests a user can make in a given time period.

## 🔒 Current Rate Limits

| Endpoint Type | Limit | Purpose |
|--------------|-------|---------|
| **Admin Login** | 5 requests/minute | Prevent brute-force password attacks |
| **Vendor Login** | 10 requests/minute | Balance security and UX for business users |
| **Delivery Login** | 10 requests/minute | Balance security and UX for delivery staff |
| **Customer Login** | 15 requests/minute | Allow for mobile app edge cases |
| **Admin Panel** | 60 requests/minute | Prevent DoS while allowing normal usage |
| **API (v1/v2)** | 240 requests/minute | Support mobile app requests |

## ⚙️ How to Change Rate Limits

### Step 1: Update Rate Limiter Definitions

**File:** `app/Providers/RouteServiceProvider.php`

**Location:** Lines 155-186 (inside `configureRateLimiting()` method)

```php
protected function configureRateLimiting()
{
    // Change the number after perMinute() to adjust the limit

    RateLimiter::for('admin-login', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
        //                      ↑
        //                 Change this number
    });

    // Other rate limiters follow the same pattern...
}
```

### Step 2: Common Adjustments

#### Make Admin Login More Strict (Recommended for high-security)
```php
RateLimiter::for('admin-login', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip()); // Only 3 attempts
});
```

#### Make API More Permissive (For high-traffic apps)
```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(500)->by(optional($request->user())->id ?: $request->ip());
    // Changed from 240 to 500
});
```

#### Change Time Window (Use perHour instead of perMinute)
```php
RateLimiter::for('admin-login', function (Request $request) {
    return Limit::perHour(20)->by($request->ip()); // 20 attempts per hour
});
```

### Step 3: Apply Changes

After modifying `RouteServiceProvider.php`:

```bash
# Clear route cache
php artisan route:clear

# Clear config cache
php artisan config:clear

# Cache new configuration (production only)
php artisan config:cache
```

## 🎯 Advanced Configurations

### Different Limits for Authenticated vs Guest Users

```php
RateLimiter::for('api', function (Request $request) {
    // Authenticated users get higher limit
    if ($request->user()) {
        return Limit::perMinute(500)->by($request->user()->id);
    }

    // Guest users get lower limit
    return Limit::perMinute(100)->by($request->ip());
});
```

### Progressive Rate Limiting (Stricter After Failures)

```php
RateLimiter::for('admin-login', function (Request $request) {
    // First tier: 5 attempts per minute
    $tier1 = Limit::perMinute(5)->by($request->ip());

    // Second tier: 10 attempts per 10 minutes
    $tier2 = Limit::perMinutes(10, 10)->by($request->ip());

    // Third tier: 20 attempts per hour
    $tier3 = Limit::perHour(20)->by($request->ip());

    return [$tier1, $tier2, $tier3];
});
```

### Rate Limit by User ID + IP (More Secure)

```php
RateLimiter::for('admin', function (Request $request) {
    return Limit::perMinute(60)->by(
        ($request->user()?->id ?? 'guest') . '|' . $request->ip()
    );
});
```

## 🛡️ Applying Rate Limits to Routes

### Method 1: Inline Middleware (Current Approach)

**File:** `routes/web.php`

```php
Route::post('login_submit', 'LoginController@submit')
    ->name('login_post')
    ->middleware('throttle:admin-login');
    //              ↑
    //     This matches the rate limiter name
```

### Method 2: Route Groups

```php
// Apply to multiple routes at once
Route::middleware(['throttle:admin-login'])->group(function () {
    Route::post('login_submit', 'LoginController@submit');
    Route::post('admin-reset-password', 'LoginController@reset');
});
```

### Method 3: Controller-Level

**File:** `app/Http/Controllers/YourController.php`

```php
public function __construct()
{
    $this->middleware('throttle:admin-login')->only(['login', 'authenticate']);
}
```

## 🧪 Testing Rate Limits

### Test Admin Login Limit (5 requests/minute)

```bash
# Bash script to test
for i in {1..6}; do
  echo "Attempt $i:"
  curl -X POST "https://admin.hopa.delivery/login_submit" \
    -d "email=test@test.com&password=wrong" \
    -c cookies.txt -b cookies.txt
  echo ""
done

# Expected:
# Attempts 1-5: Normal response
# Attempt 6: 429 Too Many Requests
```

### Test API Limit (240 requests/minute)

```bash
#!/bin/bash
count=0
success=0
failed=0

for i in {1..250}; do
  response=$(curl -s -o /dev/null -w "%{http_code}" "https://api.hopa.delivery/api/v1/config")

  if [ "$response" = "200" ]; then
    ((success++))
  elif [ "$response" = "429" ]; then
    ((failed++))
    echo "Rate limited at request $i"
  fi
done

echo "Success: $success, Rate Limited: $failed"
```

### Monitor Rate Limiting in Laravel Logs

```bash
# Watch for rate limit hits in real-time
tail -f storage/logs/laravel.log | grep -i "rate"
```

## 📊 Recommended Limits by Use Case

### High-Security Application (Banking, Healthcare)
```php
'admin-login'    => 3 per minute
'api'            => 100 per minute
'admin'          => 30 per minute
```

### Standard Business Application (Current Setup)
```php
'admin-login'    => 5 per minute
'api'            => 240 per minute
'admin'          => 60 per minute
```

### High-Traffic Consumer App
```php
'admin-login'    => 5 per minute   (Keep strict!)
'api'            => 500 per minute
'customer-login' => 30 per minute
```

## 🚨 Common Issues and Solutions

### Issue: Legitimate Users Getting Rate Limited

**Problem:** Users hitting "Submit" multiple times due to slow network

**Solution:** Add client-side button disable + increase limit slightly
```php
// Increase from 5 to 8
RateLimiter::for('admin-login', function (Request $request) {
    return Limit::perMinute(8)->by($request->ip());
});
```

### Issue: Shared IP (Office/VPN) Getting Limited

**Problem:** Multiple users behind same IP hit limit

**Solution:** Rate limit by user ID when possible
```php
RateLimiter::for('admin', function (Request $request) {
    // Authenticated: limit by user ID
    if ($request->user()) {
        return Limit::perMinute(60)->by($request->user()->id);
    }
    // Not authenticated: limit by IP (stricter)
    return Limit::perMinute(10)->by($request->ip());
});
```

### Issue: API Tests Failing Due to Rate Limits

**Problem:** Automated tests make too many requests

**Solution:** Disable rate limiting in test environment
```php
// In RouteServiceProvider.php
protected function configureRateLimiting()
{
    // Skip rate limiting in testing
    if (app()->environment('testing')) {
        RateLimiter::for('api', fn() => Limit::none());
        return;
    }

    // Normal rate limits...
}
```

## 🔄 Deployment Checklist

When changing rate limits in production:

- [ ] Update `RouteServiceProvider.php` with new limits
- [ ] Test locally first
- [ ] Commit changes to Git
- [ ] Deploy to production
- [ ] SSH into production server
- [ ] Run `php artisan config:clear`
- [ ] Run `php artisan config:cache`
- [ ] Monitor logs for 24 hours
- [ ] Adjust if needed based on real usage

## 📚 Additional Resources

### Laravel Official Documentation
- [Rate Limiting](https://laravel.com/docs/10.x/routing#rate-limiting)
- [Middleware](https://laravel.com/docs/10.x/middleware)

### Security Best Practices
- **Never disable rate limiting on login endpoints**
- **Monitor failed login attempts in logs**
- **Consider adding CAPTCHA after 3 failed attempts**
- **Use Redis for distributed rate limiting** (if using multiple servers)

### Response Headers

Rate-limited responses include helpful headers:
```
X-RateLimit-Limit: 5
X-RateLimit-Remaining: 2
Retry-After: 48
```

Users can see when they can retry in the `Retry-After` header (seconds).

## 🎯 Quick Reference

```php
// Common rate limit patterns

Limit::perMinute(60)        // 60 requests per minute
Limit::perMinutes(5, 100)   // 100 requests per 5 minutes
Limit::perHour(1000)        // 1000 requests per hour
Limit::perDay(10000)        // 10000 requests per day
Limit::none()               // No limit (dangerous!)

// Tracking options
->by($request->ip())                    // By IP address
->by($request->user()->id)              // By user ID
->by($request->header('X-API-Key'))     // By API key
```

---

**Last Updated:** November 2025
**Maintained By:** Development Team
