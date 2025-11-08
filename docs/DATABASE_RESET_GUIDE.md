# Database Reset & Reinstallation Guide

## ⚠️ CRITICAL: Why `migrate:fresh` Doesn't Work

### The Problem

This Laravel application **does NOT use traditional seeders** for configuration data. Instead, it relies on:

1. **Installation Wizard** - A web-based setup process
2. **SQL Dump Import** - Pre-configured data in `installation/backup/database.sql`

Running `php artisan migrate:fresh --seed` will:
- ✅ Create all database tables
- ✅ Run basic user seeders
- ❌ **NOT import configuration data**
- ❌ **Result in broken application** (500 errors, empty settings)

---

## 🎯 The Correct Way to Reset Database

### Option A: Using Installation Wizard (Recommended)

This is how the application is **designed** to be set up:

```bash
# Step 1: Drop and recreate database
mysql -u root -e "DROP DATABASE IF EXISTS \`go-server\`"
mysql -u root -e "CREATE DATABASE \`go-server\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Step 2: Run migrations only (no seed)
php artisan migrate --force

# Step 3: Set installation mode to false
# Edit .env and change:
APP_INSTALL=false

# Step 4: Configure RouteServiceProvider to use install routes
# Copy contents from installation/activate_install_routes.txt
# to app/Providers/RouteServiceProvider.php

# Step 5: Clear all caches
php artisan optimize:clear

# Step 6: Visit the installation wizard
# Open browser: http://localhost:8000
# Follow the wizard - it will:
# - Import database.sql (274KB of configuration data)
# - Set up admin user
# - Configure business settings
# - Set APP_INSTALL=true

# Step 7: Restore normal routes
# Copy contents from installation/activate_update_routes.txt
# to app/Providers/RouteServiceProvider.php
```

---

### Option B: Manual SQL Import (Faster for Development)

If you want to skip the wizard:

```bash
# Step 1: Fresh migration
php artisan migrate:fresh --force

# Step 2: Import pre-configured data
mysql -u root go-server < installation/backup/database.sql

# Step 3: Create admin user
php artisan db:seed --class=AdminSeeder

# Step 4: Clear caches
php artisan optimize:clear

# Step 5: Ensure APP_INSTALL=true in .env
```

---

## 📋 What Gets Imported from database.sql

The SQL dump contains **ALL essential configuration data**:

### Critical Tables Populated:
- **business_settings** (~50 settings)
  - Business name, currency, phone, email
  - Payment methods, delivery settings
  - App configurations

- **data_settings** (4 login URLs)
  - admin_login_url
  - admin_employee_login_url
  - restaurant_login_url
  - restaurant_employee_login_url

- **addon_settings** (Payment gateways)
  - PayPal, Stripe, Razorpay configs
  - SSL Commerz, Paytm, Bkash
  - All payment gateway structures

- **zones** (Optional but recommended)
  - Default delivery zones

- **currencies**
  - Currency configurations

- **mail_configs**
  - Email service settings

---

## 🔧 What Happened During Our Database Reset

### The Mistake We Made:

```bash
# ❌ WRONG - This is what we did
php artisan migrate:fresh --seed

# Result:
# - Created tables ✅
# - Seeded only users ✅
# - Left config tables EMPTY ❌
# - Application crashed ❌
```

### Why It Failed:

1. **Payment Controllers Crashed**
   - Tried to access NULL payment configs
   - Prevented routes from loading
   - Everything returned 404

2. **Login System Broken**
   - `data_settings` table empty
   - LoginController aborted with 404
   - Couldn't access `/login/admin`

3. **Dashboard Failed**
   - `business_settings` table empty
   - Views tried to access NULL settings
   - 500 Internal Server Error

### The Manual Fix We Applied:

We manually inserted ~60 critical database records:
- 4 login URLs into `data_settings`
- 52 business settings into `business_settings`
- Fixed 13 payment controller null-safety issues

**This worked, but was NOT the intended setup method!**

---

## 🎓 Key Learnings

### Why This Design Pattern Exists:

**Commercial Laravel Products** (CodeCanyon, etc.) often use this pattern because:

✅ **Pros:**
- Easy to package complete, working configurations
- Non-technical users can install via web wizard
- Ensures consistent setup across installations
- Can include sample data easily

❌ **Cons:**
- Makes `migrate:fresh` unusable without wizard
- Violates Laravel best practices (should use seeders)
- Harder for developers to reset/test database
- SQL dumps become outdated over time

### The Laravel Best Practice:

Proper Laravel apps should have:
- Comprehensive database seeders for ALL config data
- `migrate:fresh --seed` should result in working app
- No dependency on SQL dump imports

---

## 🚀 Quick Reference Commands

### Full Reset via Wizard:
```bash
mysql -u root -e "DROP DATABASE IF EXISTS \`go-server\`; CREATE DATABASE \`go-server\`"
php artisan migrate --force
# Set APP_INSTALL=false in .env
# Visit http://localhost:8000 and complete wizard
```

### Full Reset via SQL Import:
```bash
php artisan migrate:fresh --force
mysql -u root go-server < installation/backup/database.sql
php artisan db:seed --class=AdminSeeder
php artisan optimize:clear
```

### Verify Installation:
```bash
# Check critical tables
mysql -u root go-server -e "SELECT COUNT(*) FROM business_settings"  # Should be 50+
mysql -u root go-server -e "SELECT COUNT(*) FROM data_settings"      # Should be 4+
mysql -u root go-server -e "SELECT COUNT(*) FROM addon_settings"     # Should be 10+

# Test routes
php artisan route:list | grep login
curl -I http://localhost:8000/login/admin  # Should be 200
```

---

## 📝 Default Credentials After Setup

**Admin Login:**
- URL: http://localhost:8000/login/admin
- Email: `admin@admin.com`
- Password: `12345678`

**Note:** The installation wizard may create different credentials - use what you set during wizard!

---

## 🔍 Troubleshooting

### If routes return 404:
```bash
# Check if payment controllers are causing issues
php artisan route:list 2>&1 | grep -i error

# Clear all caches
php artisan optimize:clear
```

### If dashboard shows 500 error:
```bash
# Check business_settings
mysql -u root go-server -e "SELECT COUNT(*) FROM business_settings"

# If count is < 10, re-import SQL
mysql -u root go-server < installation/backup/database.sql
```

### If login page shows 404:
```bash
# Check data_settings
mysql -u root go-server -e "SELECT * FROM data_settings WHERE \`key\` LIKE '%login_url%'"

# If empty, re-import SQL or manually insert
```

---

**Created:** November 8, 2025
**Last Updated:** November 8, 2025
**Version:** 8.5
