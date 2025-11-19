# Modules Folder

## Purpose
Contains Laravel modules for self-contained feature packages using the `nwidart/laravel-modules` package.

Each module is a mini-application with its own MVC structure, routes, views, and migrations.

## Active Modules

### TaxModule
- **Purpose:** Tax & VAT management system for orders
- **Routes:** `/admin/taxvat/*` (Admin panel)
- **Features:**
  - Tax calculation for orders
  - VAT configuration
  - Admin tax settings panel
  - API endpoints for tax data
- **Controllers:** `SystemTaxVatSetupController`, `TaxVatController`
- **Status:** Active (registered in `modules_statuses.json`)

## Module Management

```bash
# List all modules
php artisan module:list

# Enable a module
php artisan module:enable ModuleName

# Disable a module
php artisan module:disable ModuleName
```

## Adding New Modules
Modules must be:
1. Created in this directory
2. Registered in `modules_statuses.json`
3. Have proper `module.json` configuration
4. Include a Service Provider

## Documentation
Package: https://github.com/nwidart/laravel-modules
