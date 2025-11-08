<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Core Laravel web middleware
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Routing\Middleware\SubstituteBindings;

// Custom middleware
use App\Http\Middleware\ActivationCheckMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\APIGuestMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\DmTokenIsValid;
use App\Http\Middleware\InstallationMiddleware;
use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\ModulePermissionMiddleware;
use App\Http\Middleware\ReactValid;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\Subscription;
use App\Http\Middleware\VendorMiddleware;
use App\Http\Middleware\VendorTokenIsValid;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        // commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )


    ->withMiddleware(function (Middleware $middleware) {

    $middleware->use([
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);

        $middleware->group('web', [
            // EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            LocalizationMiddleware::class,
        ]);


        $middleware->group('api', [
            SubstituteBindings::class,
        ]);


        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,

            'admin' => AdminMiddleware::class,
            'vendor' => VendorMiddleware::class,
            'vendor.api' => VendorTokenIsValid::class,
            'dm.api' => DmTokenIsValid::class,
            'module' => ModulePermissionMiddleware::class,
            'installation-check' => InstallationMiddleware::class,
            'actch' => ActivationCheckMiddleware::class,
            'localization' => LocalizationMiddleware::class,
            'subscription' => Subscription::class,
            'react' => ReactValid::class,
            'apiGuestCheck' => APIGuestMiddleware::class,
            'maintenance' => MaintenanceMode::class,
        ]);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();



// $requestUri = $_SERVER['REQUEST_URI'] ?? '';
// if (!str_starts_with($requestUri, '/image-proxy')) {
//     header('Access-Control-Allow-Origin: *');
// }
// header('Access-Control-Allow-Methods: *');
// header('Access-Control-Allow-Headers: *');
