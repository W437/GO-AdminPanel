<?php

namespace App\Providers;

use Exception;
use App\Traits\AddonHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Sheet;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;

// ============================================================================
// PHP Runtime Configuration - Applied from .env via config/upload.php
// ============================================================================
// These settings override php.ini values at runtime, allowing you to control
// upload limits via .env file without needing to modify server configuration

// Memory limit - set from .env (PHP_MEMORY_LIMIT) or default to unlimited
$memoryLimit = config('upload.memory_limit', '-1');
ini_set('memory_limit', $memoryLimit);

// File upload limits - set from .env
$uploadMaxFilesize = config('upload.max_filesize', '20M');
$postMaxSize = config('upload.post_max_size', '25M');
ini_set('upload_max_filesize', $uploadMaxFilesize);
ini_set('post_max_size', $postMaxSize);

// Script execution time limits - set from .env
$maxExecutionTime = config('upload.max_execution_time', 300);
$maxInputTime = config('upload.max_input_time', 300);
ini_set('max_execution_time', $maxExecutionTime);
ini_set('max_input_time', $maxInputTime);

class AppServiceProvider extends ServiceProvider
{
    use AddonHelper;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * Bootstrap any application services.
     *
     */
    public function boot(Request $request)
    {
        if(env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        if (!App::runningInConsole()) {
            Config::set('addon_admin_routes',$this->get_addon_admin_routes());
            Config::set('get_payment_publish_status',$this->get_payment_publish_status());
            Config::set('default_pagination', 25);
            Paginator::useBootstrap();
            try {
                foreach(Helpers::get_view_keys() as $key=>$value)
                {
                    view()->share($key, $value);
                }
            } catch (\Exception $e){

            }
        }
    }
}
