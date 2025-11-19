<?php
use Illuminate\Support\Facades\Route;

/**
 * SOFTWARE UPDATE ROUTES
 * =======================
 * Purpose: Handles application version updates and database migrations
 * URL: /update/* (requires admin authentication)
 * Access: Super admin only
 *
 * Update Process:
 * 1. Check for available updates from update server
 * 2. Download update package
 * 3. Backup current files and database
 * 4. Apply file updates
 * 5. Run database migrations
 * 6. Clear all caches
 * 7. Verify update success
 *
 * Features:
 * - Version checking against update server
 * - Automatic backup before updates
 * - Database migration execution
 * - Rollback capability on failure
 * - Update history logging
 * - License verification
 *
 * Safety Measures:
 * - Maintenance mode during update
 * - File permission checks
 * - Database transaction wrapping
 * - Incremental updates support
 *
 * Note: Manual updates via FTP require running /update afterwards
 * for database migrations and cache clearing
 */

Route::get('/', 'UpdateController@update_software_index')->name('index');
Route::post('update-system', 'UpdateController@update_software')->name('update-system');

Route::fallback(function () {
    return redirect('/');
});
