
out: 📥 Pulling latest code from GitHub...
err: From github.com:W437/GO-AdminPanel
err:  * branch            main       -> FETCH_HEAD
err:    2d8b9e6..041303d  main       -> origin/main
out: Updating 2d8b9e6..041303d
out: Fast-forward
out:  public/assets/admin/css/fonts.css | 233 +++++++++-----------------------------
out:  1 file changed, 54 insertions(+), 179 deletions(-)
out: 📦 Installing Composer dependencies...
err: Installing dependencies from lock file
err: Verifying lock file contents can be installed on current platform.
err: Nothing to install, update or remove
err: Package beyondcode/laravel-websockets is abandoned, you should avoid using it. No replacement was suggested.
err: Package paypal/paypal-checkout-sdk is abandoned, you should avoid using it. Use paypal/paypal-server-sdk instead.
err: Package paypal/paypalhttp is abandoned, you should avoid using it. No replacement was suggested.
err: Generating optimized autoload files
err: Class CreateOrderTaxesTable located in ./Modules/TaxModule/Database/Migrations/2025_05_26_121656_create_order_taxes_table.php does not comply with psr-4 autoloading standard (rule: Modules\ => ./Modules). Skipping.
err: Class CreateTaxablesTable located in ./Modules/TaxModule/Database/Migrations/2025_05_26_120912_create_taxables_table.php does not comply with psr-4 autoloading standard (rule: Modules\ => ./Modules). Skipping.
err: Class CreateTaxesTable located in ./Modules/TaxModule/Database/Migrations/2025_05_26_115643_create_taxes_table.php does not comply with psr-4 autoloading standard (rule: Modules\ => ./Modules). Skipping.
err: Class CreateTaxAdditionalSetupsTable located in ./Modules/TaxModule/Database/Migrations/2025_05_26_120030_create_tax_additional_setups_table.php does not comply with psr-4 autoloading standard (rule: Modules\ => ./Modules). Skipping.
err: Class CreateSystemTaxSetupsTable located in ./Modules/TaxModule/Database/Migrations/2025_05_26_115043_create_system_tax_setups_table.php does not comply with psr-4 autoloading standard (rule: Modules\ => ./Modules). Skipping.
err: Class App\CentralLogics\FileManagerLogic located in ./app/CentralLogics/filemanager.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\Helpers located in ./app/CentralLogics/helpers.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\CampaignLogic located in ./app/CentralLogics/campaign.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\SMS_module located in ./app/CentralLogics/sms_module.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\CategoryLogic located in ./app/CentralLogics/category.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\ProductLogic located in ./app/CentralLogics/product.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\CouponLogic located in ./app/CentralLogics/coupon.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\CustomerLogic located in ./app/CentralLogics/customer.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\OrderLogic located in ./app/CentralLogics/order.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\RestaurantLogic located in ./app/CentralLogics/restaurant.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\CentralLogics\BannerLogic located in ./app/CentralLogics/banner.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: Class App\Http\Controllers\Admin\ReportController located in ./app/Http/Controllers/Admin/ReportController copy.php does not comply with psr-4 autoloading standard (rule: App\ => ./app). Skipping.
err: > Illuminate\Foundation\ComposerScripts::postAutoloadDump
err: > @php artisan package:discover --ansi
out:    INFO  Discovering packages.  
out:   barryvdh/laravel-dompdf ............................................... DONE
out:   beyondcode/laravel-websockets ......................................... DONE
out:   brian2694/laravel-toastr .............................................. DONE
out:   intervention/image .................................................... DONE
out:   knuckleswtf/scribe .................................................... DONE
out:   laravel/passport ...................................................... DONE
out:   laravel/socialite ..................................................... DONE
out:   laravel/tinker ........................................................ DONE
out:   maatwebsite/excel ..................................................... DONE
out:   madnest/madzipper ..................................................... DONE
out:   matanyadaev/laravel-eloquent-spatial .................................. DONE
out:   nesbot/carbon ......................................................... DONE
out:   nunomaduro/collision .................................................. DONE
out:   nunomaduro/termwind ................................................... DONE
out:   nwidart/laravel-modules ............................................... DONE
out:   rap2hpoutre/fast-excel ................................................ DONE
out:   simplesoftwareio/simple-qrcode ........................................ DONE
out:   staudenmeir/eloquent-json-relations ................................... DONE
out:   unicodeveloper/laravel-paystack ....................................... DONE
err: 93 packages you are using are looking for funding.
err: Use the `composer fund` command to find out more!
out: 🔧 Clearing and caching configuration...
out:    INFO  Configuration cache cleared successfully.  
out:    INFO  Application cache cleared successfully.  
out:    INFO  Compiled views cleared successfully.  
out:    INFO  Route cache cleared successfully.  
out:    INFO  Configuration cached successfully.  
out:    LogicException 
out:   Unable to prepare route [api/v1/vendor/make-collected-cash-payment] for serialization. Another route has already been assigned name [make_payment].
out:   at vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php:247
out:     243▕             $route->name($this->generateRouteName());
out:     244▕ 
out:     245▕             $this->add($route);
out:     246▕         } elseif (! is_null($symfonyRoutes->get($name))) {
out:   ➜ 247▕             throw new LogicException("Unable to prepare route [{$route->uri}] for serialization. Another route has already been assigned name [{$name}].");
out:     248▕         }
out:     249▕ 
out:     250▕         $symfonyRoutes->add($route->getName(), $route->toSymfonyRoute());
out:     251▕
out:       +18 vendor frames 
out:   19  artisan:35
out:       Illuminate\Foundation\Console\Kernel::handle()
out:    INFO  Blade templates cached successfully.  
out: 🔐 Setting permissions...
out: ♻️  Restarting queue workers...
out: go-adminpanel-worker:go-adminpanel-worker_00: stopped
out: go-adminpanel-worker:go-adminpanel-worker_01: stopped
out: go-adminpanel-worker:go-adminpanel-worker_00: started
out: go-adminpanel-worker:go-adminpanel-worker_01: started
out:    INFO  Application is now live.  
out: ✅ Deployment completed successfully!
out: ✅ Deployment completed!
==============================================
✅ Successfully executed commands to all host.