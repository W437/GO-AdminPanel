# Console

## Purpose
Houses Artisan CLI commands and scheduled tasks for the GO-AdminPanel application. This directory contains custom console commands and the task scheduler configuration.

## Files

### `Kernel.php`
The console kernel that defines the application's command schedule.

**Scheduled Tasks:**
- Story expiration check - Runs every 10 minutes to mark expired stories as inactive

## Commands Directory

### Disbursement Automation
- `RestaurantDisbursementScheduler.php` - Automated restaurant payment disbursement
- `DeliveryManDisbursementScheduler.php` - Automated delivery person payment disbursement

### Package Management
- `InstallablePackage.php` - Installation commands for addons/packages
- `UpdatablePackage.php` - Update commands for addons/packages

### Route Generation
- `GenerateAdminRoute.php` - Generates routes for the admin panel
- `GenerateRestaurantRoute.php` - Generates routes for the vendor/restaurant panel

### Maintenance
- `StoryExpirationCommand.php` - Marks expired stories as inactive
- `DatabaseRefresh.php` - Database reset utility (development/testing)

## Usage

### Running Commands
```bash
# Run story expiration command
php artisan story:expire

# Run disbursement scheduler
php artisan disbursement:restaurant
php artisan disbursement:deliveryman

# Generate routes
php artisan generate:admin-routes
php artisan generate:restaurant-routes
```

### Scheduled Tasks
Tasks are automatically scheduled in `Kernel.php` and run via Laravel's scheduler:
```bash
# Run the scheduler (add to cron)
php artisan schedule:run
```

## Creating New Commands

```bash
# Generate a new command
php artisan make:command YourCommandName
```

Commands will be created in this directory and automatically registered.

## Best Practices
- Keep commands focused on a single responsibility
- Use dependency injection for services
- Add progress bars for long-running tasks
- Include proper error handling and logging
- Schedule tasks in `Kernel.php` instead of using cron directly
