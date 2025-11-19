<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/**
 * CONSOLE ROUTES (ARTISAN COMMANDS)
 * ==================================
 * Purpose: Define custom Artisan CLI commands for maintenance and automation
 * Usage: Run via terminal with 'php artisan command-name'
 *
 * This file is for simple, closure-based commands.
 * Complex commands should be in app/Console/Commands/ as classes.
 *
 * Common use cases:
 * - Data cleanup and maintenance tasks
 * - Scheduled job definitions
 * - Database seeding shortcuts
 * - Cache management
 * - Quick admin operations
 * - Development helpers
 *
 * Example Commands (that could be added):
 * - artisan order:cleanup - Remove old cancelled orders
 * - artisan restaurant:verify - Check restaurant data integrity
 * - artisan payment:reconcile - Match payments with orders
 * - artisan delivery:optimize - Optimize delivery routes
 * - artisan report:daily - Generate daily reports
 *
 * Note: For scheduled tasks, register them in app/Console/Kernel.php
 * For complex logic, create dedicated command classes instead
 */

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
