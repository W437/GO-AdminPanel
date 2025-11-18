<?php

namespace App\CentralLogics\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationUtilityService
{
    public static function addFundPushNotification($userId, $amount = '')
    {
        $status = NotificationConfigService::getNotificationStatus('customer', 'customer_add_fund_to_wallet');
        $user = User::where('id', $userId)->first();

        if ($status?->push_notification_status === 'active' && $user?->cm_firebase_token) {
            $data = [
                'title' => translate('messages.Fund_added'),
                'description' => translate('messages.Fund_added_to_your_wallet'),
                'order_id' => '',
                'image' => '',
                'type' => 'add_fund',
                'order_status' => '',
                'amount' => $amount,
            ];

            PushNotificationService::send_push_notif_to_device($user->cm_firebase_token, $data);

            DB::table('user_notifications')->insert([
                'data' => json_encode($data),
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return true;
    }
}
