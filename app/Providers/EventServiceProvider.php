<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Models\DataSetting;
use App\Models\FoodLike;
use App\Models\Zone;
use App\Observers\BusinessSettingObserver;
use App\Observers\DataSettingObserver;
use App\Observers\FoodLikeObserver;
use App\Observers\ZoneObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        BusinessSetting::observe(BusinessSettingObserver::class);
        DataSetting::observe(DataSettingObserver::class);
        FoodLike::observe(FoodLikeObserver::class);
        Zone::observe(ZoneObserver::class);
    }
}
