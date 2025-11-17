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

            // Get the current domain
            $currentDomain = request()->getHost();

            // Define domain constants from environment
            $adminDomain = env('ADMIN_DOMAIN', 'hq-secure-panel-1337.hopa.delivery');
            $apiDomain = env('API_DOMAIN', 'api.hopa.delivery');
            $oldAdminDomain = 'admin.hopa.delivery'; // For backward compatibility

            // Admin Panel Routes (hq-secure-panel-1337.hopa.delivery OR admin.hopa.delivery for backward compat)
            if ($currentDomain === $adminDomain || $currentDomain === $oldAdminDomain) {

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
            }

            // API Routes (api.hopa.delivery)
            elseif ($currentDomain === $apiDomain) {

                // Only API routes on API subdomain
                Route::prefix('api/v1')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v1/api.php'));

                Route::prefix('api/v2')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v2/api.php'));

                // Also support routes without /api prefix for cleaner URLs
                Route::prefix('v1')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v1/api.php'));

                Route::prefix('v2')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v2/api.php'));

                // Return 404 for any non-API routes on API domain
                Route::any('/{any}', function() {
                    return response()->json(['error' => 'Not Found', 'message' => 'This domain only serves API endpoints'], 404);
                })->where('any', '^(?!api|v1|v2).*$');
            }

            // Fallback for localhost, IP access, or any other domain (development/backward compatibility)
            else {
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

                Route::prefix('api/v1')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v1/api.php'));

                Route::prefix('api/v2')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api/v2/api.php'));
            }

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(240)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
