<?php

use Illuminate\Support\Facades\Route;

/**
 * INSTALLATION ROUTES
 * ====================
 * Purpose: Handles initial application setup and installation wizard
 * URL: /install/* (only accessible before installation is complete)
 * Middleware: 'installation-check' prevents access after setup
 *
 * Installation Flow:
 * Step 0: Welcome screen and requirements check
 * Step 1: Server requirements validation (PHP version, extensions)
 * Step 2: Folder permissions check (storage, bootstrap/cache)
 * Step 3: Database configuration and testing
 * Step 4: Import database schema and seed data
 * Step 5: Admin account creation and finalization
 *
 * Features:
 * - Environment file (.env) creation
 * - Database connection testing
 * - SQL schema import with rollback on failure
 * - Purchase code validation (licensing)
 * - Admin user setup
 * - Installation lock file creation
 *
 * Security:
 * - These routes are disabled after successful installation
 * - Installation lock file prevents re-running
 * - Database credentials are validated before saving
 *
 * Note: After installation, these routes return 404
 */

Route::get('/', 'InstallController@step0')->name('step0');
Route::get('/step1', 'InstallController@step1')->name('step1');
Route::get('/step2', 'InstallController@step2')->name('step2');
Route::get('/step3/{error?}', 'InstallController@step3')->name('step3')->middleware('installation-check');
Route::get('/step4', 'InstallController@step4')->name('step4')->middleware('installation-check');
Route::get('/step5', 'InstallController@step5')->name('step5')->middleware('installation-check');

Route::post('/database_installation', 'InstallController@database_installation')->name('install.db')->middleware('installation-check');
Route::get('import_sql', 'InstallController@import_sql')->name('import_sql')->middleware('installation-check');
Route::get('force-import-sql', 'InstallController@force_import_sql')->name('force-import-sql')->middleware('installation-check');
Route::post('system_settings', 'InstallController@system_settings')->name('system_settings');
Route::post('purchase_code', 'InstallController@purchase_code')->name('purchase.code');

Route::fallback(function () {
    return redirect('/');
});
