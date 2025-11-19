# Config Folder

## Purpose
Contains all application configuration files for Laravel. These define how your application behaves across different environments.

## How It Works
```
.env (environment variables)
    ↓
config/*.php (configuration arrays with defaults)
    ↓
Code uses: config('file.key')
```

## File Categories

### Core Laravel (Essential)
- **app.php** - App name, timezone, locale, service providers
- **auth.php** - Authentication guards, user providers
- **cache.php** - Cache drivers (redis, database, file)
- **database.php** - Database connections (MySQL, Redis)
- **filesystems.php** - Storage disks (local, S3, public)
- **mail.php** - Email configuration (SMTP, Mailgun)
- **queue.php** - Background job configuration
- **session.php** - Session storage, cookies, lifetime
- **logging.php** - Log channels, error reporting
- **view.php** - Blade template settings

### Payment Gateways (Multi-region)
- **flutterwave.php** - Africa
- **paypal.php** - Global
- **paytm.php** - India
- **razor.php** - Razorpay (India)
- **sslcommerz.php** - Bangladesh

### Feature Modules
- **broadcasting.php** - WebSocket/Pusher for real-time updates
- **cors.php** - Cross-Origin Resource Sharing
- **dompdf.php** - PDF generation
- **firebase.php** - Push notifications (FCM)
- **modules.php** - Laravel Modules package
- **scribe.php** - API documentation
- **stories.php** - Stories feature settings
- **websockets.php** - Laravel WebSockets server

## Usage Examples

### Reading Config
```php
// Access config values in code
config('app.name')                    // "GO Admin"
config('database.default')            // "mysql"
config('stories.enabled')             // true/false
config('filesystems.default')         // "s3" or "public"

// With default fallback
config('stories.max_media_per_story', 10)  // Returns 10 if not set
```

### Environment Variables Pattern
```php
// config/stories.php
return [
    'enabled' => env('STORY_ENABLED', true),  // Uses .env or defaults to true
    'max_media' => env('STORY_MAX_MEDIA', 10),
];
```

### Caching (Production)
```bash
# Cache all configs for performance (production only)
php artisan config:cache

# Clear config cache (development)
php artisan config:clear
```

## Best Practices

✅ **DO:**
- Use `config('key')` instead of `env()` in application code
- Keep sensitive data in `.env`, not config files
- Cache configs in production with `config:cache`
- Add new configs to appropriate files

❌ **DON'T:**
- Delete config files (all are used by Laravel or packages)
- Call `env()` directly in code (won't work with caching)
- Hardcode credentials in config files
- Modify vendor package configs directly

## Adding New Configuration

1. Choose appropriate file or create new one
2. Use `env()` for environment-specific values
3. Provide sensible defaults
4. Document with comments

Example:
```php
// config/myfeature.php
return [
    'enabled' => env('MYFEATURE_ENABLED', false),
    'api_key' => env('MYFEATURE_API_KEY'),
    'timeout' => env('MYFEATURE_TIMEOUT', 30),
];
```

## Important Notes

- All config files are auto-loaded by Laravel
- Changes require `config:clear` in development
- In production, run `config:cache` after deployment
- Payment configs support 14+ countries/regions
- Never commit `.env` - only `.env.example`

## Related Files
- `.env` - Environment-specific variables
- `.env.example` - Template for environment variables
- `bootstrap/cache/config.php` - Cached config (generated)
