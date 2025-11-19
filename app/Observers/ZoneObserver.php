<?php

namespace App\Observers;

use App\Models\Zone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ZoneObserver
{
    /**
     * Handle the Zone "created" event.
     */
    public function created(Zone $zone): void
    {
        $this->refreshZoneCache();
    }

    /**
     * Handle the Zone "updated" event.
     */
    public function updated(Zone $zone): void
    {
        $this->refreshZoneCache();
    }

    /**
     * Handle the Zone "deleted" event.
     */
    public function deleted(Zone $zone): void
    {
        $this->refreshZoneCache();
    }

    /**
     * Handle the Zone "restored" event.
     */
    public function restored(Zone $zone): void
    {
        $this->refreshZoneCache();
    }

    /**
     * Handle the Zone "force deleted" event.
     */
    public function forceDeleted(Zone $zone): void
    {
        $this->refreshZoneCache();
    }

    /**
     * Refresh all zone-related cache entries
     */
    private function refreshZoneCache()
    {
        // Clear zone-related cache keys
        $prefix = 'zone_';
        $cacheKeys = DB::table('cache')
            ->where('key', 'like', "%" . $prefix . "%")
            ->pluck('key');

        $appName = env('APP_NAME').'_cache';
        $remove_prefix = strtolower(str_replace('=', '', $appName));

        $sanitizedKeys = $cacheKeys->map(function ($key) use ($remove_prefix) {
            $key = str_replace($remove_prefix, '', $key);
            return $key;
        });

        foreach ($sanitizedKeys as $key) {
            Cache::forget($key);
        }

        // Also clear config cache to ensure fresh data
        try {
            \Artisan::call('config:clear');
        } catch (\Exception $e) {
            // Config cache clearing failed, but continue
        }
    }
}
