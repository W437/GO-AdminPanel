<?php

use Illuminate\Support\Facades\Route;

/**
 * API ROUTES V2 - NEXT VERSION API (MINIMAL)
 * ===========================================
 * Purpose: Version 2 API endpoints for newer features or breaking changes
 * Base URL: /api/v2/* (defined in RouteServiceProvider)
 *
 * Currently contains:
 * - Library/License update endpoints
 *
 * This version is reserved for:
 * - Breaking changes that can't be added to V1
 * - Experimental features
 * - Gradual migration from V1
 * - New authentication mechanisms
 *
 * Note: Most functionality still uses V1 API
 * Future expansions will be added here when V1 compatibility breaks
 */

Route::group(['namespace' => 'Api\V2'], function () {
    Route::post('ls-lib-update', 'LsLibController@lib_update');
});
