<?php

namespace App\CentralLogics\Orders;

use App\CentralLogics\Helpers;
use App\Mail\OrderVerificationMail;
use App\Mail\PlaceOrder;
use App\Models\NotificationMessage;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    public static function order_status_update_message($status, $lang = 'default')
    {
        if ($status == 'pending') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_pending_message')->first();
        } elseif ($status == 'confirmed') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_confirmation_msg')->first();
        } elseif ($status == 'processing') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_processing_message')->first();
        } elseif ($status == 'picked_up') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'out_for_delivery_message')->first();
        } elseif ($status == 'handover') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_handover_message')->first();
        } elseif ($status == 'delivered') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_delivered_message')->first();
        } elseif ($status == 'delivery_boy_delivered') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'delivery_boy_delivered_message')->first();
        } elseif ($status == 'accepted') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'delivery_boy_assign_message')->first();
        } elseif ($status == 'canceled') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_cancled_message')->first();
        } elseif ($status == 'refunded') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'order_refunded_message')->first();
        } elseif ($status == 'refund_request_canceled') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'refund_request_canceled')->first();
        } elseif ($status == 'offline_verified') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'offline_order_accept_message')->first();
        } elseif ($status == 'offline_denied') {
            $data = NotificationMessage::with(['translations' => function ($query) use ($lang) {
                $query->where('locale', $lang);
            }])->where('key', 'offline_order_deny_message')->first();
        } else {
            $data = ["status" => "0", "message" => '', 'translations' => []];
        }

        if ($data) {
            if ($data['status'] == 0) {
                return 0;
            }
            return $data['message'];
        } else {
            return false;
        }
    }

    public static function send_order_notification($order)
    {
        $order = Order::where('id', $order->id)->with('zone:id,deliveryman_wise_topic', 'restaurant:id,name,restaurant_model,self_delivery_system,vendor_id', 'restaurant.restaurant_sub', 'customer:id,cm_firebase_token,email,f_name,l_name,current_language_key', 'restaurant.vendor:id,firebase_token', 'delivery_man:id,fcm_token', 'guest')->first();
        $push_notification_status = Helpers::getNotificationStatusData('restaurant', 'restaurant_order_notification');
        $restaurant_push_notification_status = Helpers::getRestaurantNotificationStatusData($order?->restaurant?->id, 'restaurant_order_notification');
        $deliveryman_push_notification_status = Helpers::getNotificationStatusData('deliveryman', 'deliveryman_order_notification');

        try {
            $status = ($order->order_status == 'delivered' && $order->delivery_man) ? 'delivery_boy_delivered' : $order->order_status;

            if ($order->checked != 1 && ($order->subscription_id == null && (in_array($order->payment_method, ['cash_on_delivery', 'offline_payment']) && $order->order_status == 'pending') || (!in_array($order->payment_method, ['cash_on_delivery', 'offline_payment']) && $order->order_status == 'confirmed'))) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => translate('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'new_order_admin',
                ];
                Helpers::send_push_notif_to_topic($data, 'admin_message', 'order_request', url('/') . '/admin/order/list/all');
            }

            if ($order->is_guest) {
                $customer_details = json_decode($order['delivery_address'], true);
                $value = self::order_status_update_message($status, 'en');
                $value = Helpers::text_variable_data_format(value: $value, restaurant_name: $order->restaurant?->name, order_id: $order->id, user_name: "{$customer_details['contact_person_name']}");
                $user_fcm = $order?->guest?->fcm_token;
            } else {
                $value = self::order_status_update_message($status, $order->customer ? $order?->customer?->current_language_key : 'en');
                $value = Helpers::text_variable_data_format(value: $value, user_name: "{$order->customer?->f_name} {$order->customer?->l_name}", restaurant_name: $order->restaurant?->name, order_id: $order->id);
                $user_fcm = $order?->customer?->cm_firebase_token;
            }

            $customer_push_notification_status = Helpers::getNotificationStatusData('customer', 'customer_order_notification');
            if ($customer_push_notification_status?->push_notification_status == 'active' && $value && $user_fcm) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => $order->order_status,
                ];
                Helpers::send_push_notif_to_device($user_fcm, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $customer_push_notification_status = null;
            $customer_push_notification_status = Helpers::getNotificationStatusData('customer', 'customer_refund_request_rejaction');

            if ($customer_push_notification_status?->push_notification_status == 'active' && $order?->customer?->cm_firebase_token && $order->order_status == 'refund_request_canceled') {
                $data = [
                    'title' => translate('messages.Refund_Rejected'),
                    'description' => translate('messages.Your_refund_request_has_been_canceled'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => $order->order_status,
                ];
                Helpers::send_push_notif_to_device($order?->customer?->cm_firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $order->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if ($push_notification_status?->push_notification_status == 'active' && $restaurant_push_notification_status?->push_notification_status == 'active' && $status == 'picked_up' && $order?->restaurant?->vendor?->firebase_token) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => $order->order_status,
                ];
                Helpers::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $order->restaurant->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if (in_array($order->order_type, ['dine_in', 'delivery']) && !$order->scheduled && $order->order_status == 'pending' && $order->payment_method == 'cash_on_delivery' && config('order_confirmation_model') == 'deliveryman') {
                if (($order->restaurant->restaurant_model == 'commission' && $order->restaurant->self_delivery_system)
                    || ($order->restaurant->restaurant_model == 'subscription' && isset($order->restaurant->restaurant_sub) && $order->restaurant->restaurant_sub->self_delivery)
                ) {
                    if ($push_notification_status?->push_notification_status == 'active' && $restaurant_push_notification_status?->push_notification_status == 'active' && $order?->restaurant?->vendor?->firebase_token) {
                        $data = [
                            'title' => translate('messages.order_push_title'),
                            'description' => translate('messages.new_order_push_description'),
                            'order_id' => $order->id,
                            'image' => '',
                            'type' => 'new_order',
                            'order_status' => $order->order_status,
                        ];
                        Helpers::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'vendor_id' => $order->restaurant->vendor_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            if (($order->restaurant->restaurant_model == 'commission' && !$order->restaurant->self_delivery_system)
                || ($order->restaurant->restaurant_model == 'subscription' && isset($order->restaurant->restaurant_sub) && !$order->restaurant->restaurant_sub->self_delivery)
            ) {
                if ($order->order_status == 'confirmed' && $deliveryman_push_notification_status?->push_notification_status == 'active' && $order->order_type != 'take_away' && $order->zone?->deliveryman_wise_topic) {
                    $data = [
                        'title' => translate('messages.order_push_title'),
                        'description' => translate('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'new_order',
                        'order_status' => $order->order_status,
                        'order_type' => $order->order_type,
                    ];
                    Helpers::send_push_notif_to_topic($data, $order?->zone?->deliveryman_wise_topic, 'order_request');
                }
            }

            if ($order->delivery_man && $deliveryman_push_notification_status?->push_notification_status == 'active' && $order->delivery_man?->fcm_token) {
                $data = [
                    'title' => translate('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                    'order_status' => $order->order_status,
                ];
                Helpers::send_push_notif_to_device($order->delivery_man->fcm_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'delivery_man_id' => $order->delivery_man_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            try {
                $notification_status = Helpers::getNotificationStatusData('customer', 'customer_order_notification');
                if ($notification_status?->mail_status == 'active' &&  $order->order_status == 'confirmed' && $order->payment_method != 'cash_on_delivery' && config('mail.status') && Helpers::get_mail_status('place_order_mail_status_user') == '1' && $order->is_guest == 0) {
                    Mail::to($order->customer->email)->send(new PlaceOrder($order->id));
                }
                $notification_status = null;
                $notification_status = Helpers::getNotificationStatusData('customer', 'customer_delivery_verification');
                if ($notification_status?->mail_status == 'active' &&  $order->order_status == 'pending' && config('mail.status')  && config('order_delivery_verification') == 1 && Helpers::get_mail_status('order_verification_mail_status_user') == '1' && $order->is_guest == 0) {
                    Mail::to($order->customer->email)->send(new OrderVerificationMail($order->otp, $order->customer->f_name));
                }
            } catch (\Exception $exception) {
                info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
            }
            return true;
        } catch (\Exception $exception) {
            info([$exception->getFile(), $exception->getLine(), $exception->getMessage()]);
        }
        return false;
    }
}
