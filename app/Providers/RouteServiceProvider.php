<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';


    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            // Define domain constants from environment
            $adminDomain = env('ADMIN_DOMAIN', 'hq-secure-panel-1337.hopa.delivery');
            $apiDomain = env('API_DOMAIN', 'api.hopa.delivery');
            $oldAdminDomain = 'admin.hopa.delivery'; // For backward compatibility

            // API Routes (api.hopa.delivery) - Register FIRST with highest priority
            Route::domain($apiDomain)
                ->group(function () {
                    // Root path returns API info
                    Route::get('/', function() {
                        return response()->json([
                            'message' => 'GO Admin API',
                            'status' => 'active',
                            'endpoints' => [
                                'v1' => url('/api/v1'),
                                'v2' => url('/api/v2'),
                            ],
                            'documentation' => 'API endpoints are available at /api/v1 and /api/v2'
                        ], 200);
                    });

                    // API routes with /api prefix (main endpoints)
                    Route::prefix('api/v1')
                        ->middleware('api')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/api/v1/api.php'));

                    Route::prefix('api/v2')
                        ->middleware('api')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/api/v2/api.php'));

                    // Catch-all for non-API routes on API domain
                    Route::any('{any}', function() {
                        return response()->json([
                            'error' => 'Not Found',
                            'message' => 'This domain only serves API endpoints. Please use /api/v1 or /api/v2 for API access.'
                        ], 404);
                    })->where('any', '.*');
                });

            // Admin Panel Routes (hq-secure-panel-1337.hopa.delivery)
            Route::domain($adminDomain)
                ->group(function () {
                    Route::middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/web.php'));

                    Route::prefix('admin')
                        ->middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/admin.php'));

                    Route::prefix('restaurant-panel')
                        ->middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/vendor.php'));

                    // Also allow API access from admin domain for backward compatibility
                    Route::prefix('api/v1')
                        ->middleware('api')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/api/v1/api.php'));

                    Route::prefix('api/v2')
                        ->middleware('api')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/api/v2/api.php'));
                });

            // Old Admin Domain (admin.hopa.delivery) - For backward compatibility
            // Only web/admin routes, no API to avoid route name conflicts
            Route::domain($oldAdminDomain)
                ->group(function () {
                    Route::middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/web.php'));

                    Route::prefix('admin')
                        ->middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/admin.php'));

                    Route::prefix('restaurant-panel')
                        ->middleware('web')
                        ->namespace($this->namespace)
                        ->group(base_path('routes/vendor.php'));
                });

            // Fallback for localhost, IP access (development only - minimal routes to avoid conflicts)
            // For production, use proper domain names above
            // Only register web/admin routes, no API routes to avoid duplicate route name conflicts
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('admin')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/admin.php'));

            Route::prefix('restaurant-panel')
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/vendor.php'));

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // API rate limiting - 240 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(240)->by(optional($request->user())->id ?: $request->ip());
        });

        // Admin login rate limiting - 5 attempts per minute
        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Vendor login rate limiting - 10 attempts per minute
        RateLimiter::for('vendor-login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Delivery man login rate limiting - 10 attempts per minute
        RateLimiter::for('delivery-login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Customer login rate limiting - 15 attempts per minute
        RateLimiter::for('customer-login', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        // Admin panel general rate limiting - 60 requests per minute
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
