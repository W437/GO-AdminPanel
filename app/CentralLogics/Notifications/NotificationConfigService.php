<?php

namespace App\CentralLogics\Notifications;

use App\Models\NotificationSetting;
use App\Models\RestaurantNotificationSetting;
use App\Traits\NotificationDataSetUpTrait;

class NotificationConfigService
{
    use NotificationDataSetUpTrait;

    public static function syncAdminNotificationSettings(): bool
    {
        $data = self::getAdminNotificationSetupData();
        NotificationSetting::upsert(
            $data,
            ['key', 'type'],
            ['title', 'mail_status', 'sms_status', 'push_notification_status', 'sub_title']
        );

        return true;
    }

    public static function syncRestaurantNotificationSettings($restaurantId): bool
    {
        $data = self::getRestaurantNotificationSetupData($restaurantId);
        RestaurantNotificationSetting::upsert(
            $data,
            ['key', 'restaurant_id'],
            ['title', 'mail_status', 'sms_status', 'push_notification_status', 'sub_title']
        );

        return true;
    }

    public static function getNotificationStatus(string $userType, string $key): ?NotificationSetting
    {
        return NotificationSetting::where('type', $userType)
            ->where('key', $key)
            ->select(['mail_status', 'push_notification_status', 'sms_status'])
            ->first();
    }

    public static function getRestaurantNotificationStatus($restaurantId, string $key): ?RestaurantNotificationSetting
    {
        $data = self::getRestaurantNotificationSetting($restaurantId, $key);

        if (!$data) {
            self::addNewRestaurantNotificationSetupData($restaurantId);
            $data = self::getRestaurantNotificationSetting($restaurantId, $key);

            if (!$data) {
                self::syncRestaurantNotificationSettings($restaurantId);
                $data = self::getRestaurantNotificationSetting($restaurantId, $key);
            }
        }

        return $data;
    }

    public static function ensureAdminNotificationSeed(): bool
    {
        self::addNewAdminNotificationSetupData();

        return true;
    }

    protected static function getRestaurantNotificationSetting($restaurantId, string $key): ?RestaurantNotificationSetting
    {
        return RestaurantNotificationSetting::where('restaurant_id', $restaurantId)
            ->where('key', $key)
            ->select(['mail_status', 'push_notification_status', 'sms_status'])
            ->first();
    }
}
