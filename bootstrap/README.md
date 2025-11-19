# Bootstrap Folder

## ⚠️ CRITICAL - DO NOT DELETE THIS FOLDER

## Purpose
Bootstraps (starts) the Laravel framework on every request. This folder is **essential** for your application to run.

## Files

### app.php (REQUIRED)
- **Purpose:** Creates and configures the Laravel application instance
- **Used by:** `public/index.php` on every request
- **Contains:** App initialization, service binding, error handling setup
- **Delete?** ❌ NEVER - App cannot start without this

### cache/ (AUTO-GENERATED)
- **Purpose:** Stores compiled framework optimizations for faster startup
- **Contents:**
  - `services.php` - Compiled service providers list
  - `packages.php` - Cached composer package manifest
  - `config.php` - Cached configuration (when running `config:cache`)
  - Module cache files
- **Delete?** ⚠️ Can clear, will regenerate (slight performance hit)

## How It Works

```
Every Request Flow:
1. Browser → public/index.php
2. index.php → require bootstrap/app.php
3. app.php → Creates Laravel app instance
4. Loads services from cache/ for speed
5. Application ready to handle request
```

## Cache Management

### Clear Bootstrap Cache
```bash
php artisan optimize:clear    # Clears all framework caches
php artisan cache:clear       # Clears application cache
```

### Rebuild Optimizations (Production)
```bash
php artisan optimize          # Rebuilds all caches
php artisan config:cache      # Caches configuration
php artisan route:cache       # Caches routes
php artisan view:cache        # Caches views
```

## When Cache Files Regenerate

The `cache/` folder automatically rebuilds when:
- First request after clearing
- After `composer install/update`
- After running optimization commands
- When Laravel detects changes

## Important Notes

- **Size:** Only ~44KB total (tiny but critical)
- **Git:** Cache files are gitignored (regenerate per environment)
- **Permissions:** Must be writable by web server
- **Production:** Always run `php artisan optimize` after deployment

## What NOT to Do

❌ Delete the entire bootstrap folder
❌ Delete app.php
❌ Modify app.php unless you know what you're doing
❌ Commit cache files to git (already gitignored)

## What You CAN Do

✅ Clear cache/ contents (regenerates automatically)
✅ Run optimize commands for production
✅ Check cache file timestamps for debugging

## Troubleshooting

**Issue:** "Class not found" errors
**Fix:** `php artisan optimize:clear`

**Issue:** Config changes not reflecting
**Fix:** `php artisan config:clear`

**Issue:** Slow first request after deployment
**Fix:** `php artisan optimize` (rebuilds caches)

---

**Summary:** This folder is Laravel's engine starter. Don't touch `app.php`, but you can safely clear and regenerate the cache folder contents.
