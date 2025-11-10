# GO-AdminPanel Cache & Deployment Guide

## Production Environment Details

### Server Information
- **IP**: 138.197.188.120
- **Path**: /var/www/go-adminpanel
- **URL**: https://admin.hopa.delivery
- **Web Server**: Apache2 (NOT Nginx/PHP-FPM)
- **PHP Version**: 8.4.11
- **Deployment**: Auto-deploys from GitHub (main branch)

## Critical Laravel Cache Knowledge

### ✅ Correct Cache Facade Methods
```php
// Cache facade - these methods EXIST
Cache::forget('key');         // Remove specific cache key
Cache::flush();              // Clear all cache
Cache::rememberForever();    // Cache permanently
Cache::remember();           // Cache with TTL
Helpers::deleteCacheData();  // Custom helper method
```

### ❌ WRONG - Config Facade Methods
```php
// Config facade - these methods DO NOT EXIST
Config::forget('key');       // ❌ DOES NOT EXIST - Will cause 500 error!
Config::flush();            // ❌ DOES NOT EXIST

// Config facade - these methods DO exist
Config::set('key', value);  // ✅ Set config value (runtime only)
Config::get('key');         // ✅ Get config value
Config::has('key');         // ✅ Check if exists
```

## BusinessSetting Model Architecture

### Dual-Layer Caching System
1. **Persistent Cache Layer**
   - Key: `business_settings_all_data`
   - Method: `Cache::rememberForever()`
   - Contains: All business settings from database

2. **Runtime Config Layer**
   - Key Pattern: `{setting_key}_conf` (e.g., `currency_conf`)
   - Method: `Config::set($key.'_conf', $data)`
   - Scope: Current request only

### Important File Locations
- **Model**: `app/Models/BusinessSetting.php`
- **Helper**: `app/CentralLogics/helpers.php` (lines 1153-1189)
- **Method**: `get_business_settings($key, $json_decode = true)`

### Model Events Execution
- `saved` event: Fires after both `created` and `updated`
- `created` event: Fires when new record created
- `updated` event: Fires when existing record updated
- `deleted` event: Fires when record deleted

## Common Issues & Solutions

### Issue 1: "Set Up Configuration First" Error
**Symptoms**: Social login credentials not recognized immediately after saving

**Root Cause**: Stale runtime Config cache not being cleared

**Solution**: Clear Config cache after BusinessSetting saves
```php
// In BusinessSetting model saved event:
if (!empty($model->key)) {
    Config::forget($model->key . '_conf'); // ❌ WRONG - doesn't exist!
}
```

**Correct approach**: Modify the helper function or clear all cache

### Issue 2: 500 Internal Server Error on Save
**Common Causes**:
1. Calling non-existent methods (e.g., `Config::forget()`)
2. Circular dependencies in model events
3. Incomplete S3/storage configuration
4. Cache corruption

**Debug Steps**:
```bash
# Check error logs
ssh root@138.197.188.120
tail -n 100 /var/www/go-adminpanel/storage/logs/laravel.log
```

### Issue 3: Production Drift from GitHub
**Symptoms**: Production has different commits than GitHub

**Fix Production Sync**:
```bash
ssh root@138.197.188.120
cd /var/www/go-adminpanel

# Check current state
git status
git log --oneline -5

# Force sync with GitHub
git fetch origin
git reset --hard origin/main

# Clear all caches
php artisan optimize:clear
php artisan config:cache

# Restart web server
systemctl restart apache2
```

## Safe Cache Management Commands

### Local Development
```bash
# Clear specific caches
php artisan cache:clear       # Application cache
php artisan config:clear      # Configuration cache
php artisan view:clear        # Compiled views
php artisan route:clear       # Route cache

# Clear everything at once
php artisan optimize:clear
```

### Production Server
```bash
# One-line command for production
ssh root@138.197.188.120 "cd /var/www/go-adminpanel && php artisan optimize:clear && php artisan config:cache && systemctl restart apache2"

# Step by step
ssh root@138.197.188.120
cd /var/www/go-adminpanel
php artisan optimize:clear
php artisan config:cache
systemctl restart apache2
```

## Storage Configuration Issues

### DigitalOcean Spaces Configuration
For DigitalOcean Spaces, ALL fields must be filled:
- **Region**: e.g., `fra1`
- **Bucket**: e.g., `hopastorage`
- **URL**: `https://{bucket}.{region}.digitaloceanspaces.com`
- **Endpoint**: `https://{region}.digitaloceanspaces.com`

### S3/Spaces Error Prevention
If `Helpers::getDisk()` is called with incomplete S3 config, it will fail. Always ensure:
1. All S3 configuration fields are complete
2. Or use try-catch around `getDisk()` calls
3. Or default to 'public' disk on error

## Red Flags & Prevention

### 🚨 Things to NEVER Do
1. **Never use `Config::forget()`** - Method doesn't exist
2. **Never clear cache inside model events** without checking for loops
3. **Never force push to main** without team coordination
4. **Never skip error logs** when debugging 500 errors

### ✅ Best Practices
1. **Before changing cache logic**: Understand the dual-layer system
2. **Before using facades**: Verify methods exist in Laravel docs
3. **Before pushing**: Test with production-like cache settings
4. **After deployment**: Verify production matches GitHub
5. **Regular maintenance**: Schedule cache clearing

## Emergency Recovery Procedures

### When Everything Breaks
```bash
# 1. SSH to production
ssh root@138.197.188.120

# 2. Check git state
cd /var/www/go-adminpanel
git status
git log --oneline -5

# 3. Save any important uncommitted changes
git stash

# 4. Force sync with GitHub
git fetch origin
git reset --hard origin/main

# 5. Nuclear option - clear everything
php artisan optimize:clear
rm -rf bootstrap/cache/*
php artisan config:cache
php artisan route:cache

# 6. Restart services
systemctl restart apache2

# 7. Test the application
curl -I https://admin.hopa.delivery/admin/dashboard
```

## Testing Checklist

After any cache-related changes:
- [ ] Test saving Business Settings
- [ ] Test saving Storage Connection
- [ ] Test saving Login Setup
- [ ] Test toggling Social Login providers
- [ ] Verify settings persist after page refresh
- [ ] Check error logs for warnings
- [ ] Test on production after deployment

## Key Learnings

1. **Config facade is read-only** after application boot
2. **Model events fire in cascade** - be careful with bulk operations
3. **Static variables persist** across the request lifecycle
4. **Production can drift** if deployments fail silently
5. **Apache != Nginx** - different restart commands

## Quick Reference

| Issue | Command | Purpose |
|-------|---------|---------|
| 500 Error | `tail -n 100 storage/logs/laravel.log` | Check error details |
| Cache Issues | `php artisan optimize:clear` | Clear all caches |
| Git Drift | `git reset --hard origin/main` | Force sync with GitHub |
| Service Restart | `systemctl restart apache2` | Restart web server |
| Config Rebuild | `php artisan config:cache` | Rebuild config cache |

---

**Last Updated**: November 2024
**Incident**: Cache clearing implementation causing 500 errors
**Resolution**: Removed invalid Config::forget() calls, synced production with GitHub