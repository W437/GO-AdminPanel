<?php

namespace App\CentralLogics\Subscription;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Mail\SubscriptionRenewOrShift;
use App\Mail\SubscriptionSuccessful;
use App\Models\BusinessSetting;
use App\Models\Food;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantSubscription;
use App\Models\RestaurantWallet;
use App\Models\SubscriptionBillingAndRefundHistory;
use App\Models\SubscriptionPackage;
use App\Models\SubscriptionTransaction;
use App\Traits\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SubscriptionService
{
    use Payment;

    public static function subscription_check()
    {
        $business_model = Helpers::getSettingsDataFromConfig(settings: 'business_model');
        if (!$business_model) {
            Helpers::insert_business_settings_key('refund_active_status', '1');
            Helpers::insert_business_settings_key('business_model',
                json_encode([
                    'commission' => 1,
                    'subscription' => 0,
                ]));
            $business_model = [
                'commission' => 1,
                'subscription' => 0,
            ];
        } else {
            $business_model = $business_model->value ? json_decode($business_model->value, true) : [
                'commission' => 1,
                'subscription' => 0,
            ];
        }

        return $business_model['subscription'] == 1;
    }

    public static function commission_check()
    {
        $business_model = Helpers::get_business_settings('business_model');

        if (!$business_model) {
            Helpers::insert_business_settings_key('business_model',
                json_encode([
                    'commission' => 1,
                    'subscription' => 0,
                ]));
            $business_model = [
                'commission' => 1,
                'subscription' => 0,
            ];
        } else {
            $business_model = $business_model ?? [
                'commission' => 1,
                'subscription' => 0,
            ];
        }

        return $business_model['commission'] == 1;
    }

    public static function check_subscription_validity()
    {
        $current_date = date('Y-m-d');
        $check_subscription_validity_on = BusinessSetting::where('key', 'check_subscription_validity_on')->first();
        if (!$check_subscription_validity_on) {
            Helpers::insert_business_settings_key('check_subscription_validity_on', date('Y-m-d'));
        }
        if ($check_subscription_validity_on && $check_subscription_validity_on->value != $current_date) {
            $check_subscription_validity_on->value = $current_date;
            $check_subscription_validity_on->save();
            Helpers::create_subscription_order_logs();
        }
        return false;
    }

    public static function calculateSubscriptionRefundAmount($restaurant, $return_data = null)
    {
        $restaurant_subscription = $restaurant->restaurant_sub;
        if ($restaurant_subscription && $restaurant_subscription?->is_canceled === 0 && $restaurant_subscription?->is_trial === 0) {
            $day_left = $restaurant_subscription->expiry_date_parsed->format('Y-m-d');
            if (Carbon::now()->diffInDays($day_left, false) > 0) {
                $add_days = Carbon::now()->diffInDays($day_left, false);
                $validity = $restaurant_subscription?->validity;
                $subscription_usage_max_time = BusinessSetting::where('key', 'subscription_usage_max_time')->first()?->value ?? 50;
                $subscription_usage_max_time = ($validity * $subscription_usage_max_time) / 100;

                if (($validity - $add_days) < $subscription_usage_max_time) {
                    $per_day = $restaurant->restaurant_sub_trans->price / $restaurant->restaurant_sub_trans->validity;
                    $back_amount = $per_day * $add_days;

                    if ($return_data == true) {
                        return ['back_amount' => $back_amount, 'days' => $add_days];
                    }

                    $vendorWallet = RestaurantWallet::firstOrNew([
                        'vendor_id' => $restaurant->vendor_id
                    ]);
                    $vendorWallet->total_earning = $vendorWallet->total_earning + $back_amount;
                    $vendorWallet->save();

                    $refund = new SubscriptionBillingAndRefundHistory();
                    $refund->restaurant_id = $restaurant->id;
                    $refund->subscription_id = $restaurant_subscription->id;
                    $refund->package_id = $restaurant_subscription->package_id;
                    $refund->transaction_type = 'refund';
                    $refund->is_success = 1;
                    $refund->amount = $back_amount;
                    $refund->reference = 'validity_left_' . $add_days;
                    $refund->save();
                }
            }
        }

        return true;
    }

    public static function subscription_plan_chosen($restaurant_id, $package_id, $payment_method, $discount = 0, $pending_bill = 0, $reference = null, $type = null)
    {
        $restaurant = Restaurant::find($restaurant_id);
        $package = SubscriptionPackage::withoutGlobalScope('translate')->find($package_id);
        $add_days = 0;
        $add_orders = 0;

        try {
            $restaurant_subscription = $restaurant->restaurant_sub;
            if (isset($restaurant_subscription) && $type == 'renew') {
                $restaurant_subscription->total_package_renewed = $restaurant_subscription->total_package_renewed + 1;

                $day_left = $restaurant_subscription->expiry_date_parsed->format('Y-m-d');
                if (Carbon::now()->diffInDays($day_left, false) > 0 && $restaurant_subscription->is_canceled != 1) {
                    $add_days = Carbon::now()->subDays(1)->diffInDays($day_left, false);
                }
                if ($restaurant_subscription->max_order != 'unlimited' && $restaurant_subscription->max_order > 0) {
                    $add_orders = $restaurant_subscription->max_order;
                }
            } elseif ($restaurant->restaurant_sub_update_application && $restaurant->restaurant_sub_update_application->package_id == $package->id && $type == 'renew') {
                $restaurant_subscription = $restaurant->restaurant_sub_update_application;
                $restaurant_subscription->total_package_renewed = $restaurant_subscription->total_package_renewed + 1;
            } else {
                $restaurant_subscription = new RestaurantSubscription();
                $restaurant_subscription->total_package_renewed = 0;
                $restaurant_subscription->restaurant_id = $restaurant->id;
            }

            $restaurant_subscription->is_trial = 0;
            $restaurant_subscription->renewed_at = now();
            $restaurant_subscription->package_id = $package->id;
            $restaurant_subscription->package_price = $package->price;
            $restaurant_subscription->pos = $package->pos;
            $restaurant_subscription->chat = $package->chat;
            $restaurant_subscription->self_delivery = $package->self_delivery;
            $restaurant_subscription->content_upload = $package->content_upload;
            $restaurant_subscription->free_delivery = $package->free_delivery;
            $restaurant_subscription->max_zone = $package->max_zone;
            $restaurant_subscription->max_addon = $package->max_addon;
            $restaurant_subscription->max_banner = $package->max_banner;
            $restaurant_subscription->max_time = $package->max_time;
            $restaurant_subscription->max_campaign = $package->max_campaign;
            $restaurant_subscription->review_reply = $package->review_reply;
            $restaurant_subscription->react_time = $package->react_time;
            $restaurant_subscription->schedule_order = $package->schedule_order;
            $restaurant_subscription->total_package_down = $restaurant_subscription->total_package_down ?? 0;
            $restaurant_subscription->total_package_renewed = $restaurant_subscription->total_package_renewed ?? 0;
            $restaurant_subscription->commission = $package->commission;
            $restaurant_subscription->commission_type = $package->commission_type ?? 'percent';
            $restaurant_subscription->max_product = $package->max_product;
            $restaurant_subscription->status = 1;
            $restaurant_subscription->is_canceled = 0;

            if ($type == 'renew' && isset($restaurant_subscription->expiry_date_parsed)) {
                $restaurant_subscription->expiry_date = $restaurant_subscription->expiry_date_parsed->addDays($package->validity + $add_days)->format('Y-m-d');
                $restaurant_subscription->validity = $restaurant_subscription->validity + $package->validity + $add_days;
            } else {
                $restaurant_subscription->expiry_date = Carbon::now()->addDays($package->validity + $add_days)->format('Y-m-d');
                $restaurant_subscription->validity = $package->validity + $add_days;
            }

            if ($package->max_order == 'unlimited') {
                $restaurant_subscription->max_order = 'unlimited';
            } else {
                $restaurant_subscription->max_order = $package->max_order + $add_orders;
            }

            $restaurant_subscription->save();

            $subscription_transaction = new SubscriptionTransaction();
            $subscription_transaction->id = (string)Str::uuid();
            $subscription_transaction->package_id = $package->id;
            $subscription_transaction->restaurant_id = $restaurant->id;
            $subscription_transaction->price = $package->price;
            $subscription_transaction->validity = $package->validity;
            $subscription_transaction->payment_method = $payment_method;
            $subscription_transaction->reference = $reference;
            $subscription_transaction->discount = $discount;
            $subscription_transaction->paid_amount = $package->price + $pending_bill;
            $subscription_transaction->plan_type = $type ?? 'first_purchased';
            $subscription_transaction->initiator = auth('admin')->check() ? 'admin' : 'restaurant';
            $subscription_transaction->package_details = $package;

            DB::beginTransaction();
            $restaurant->save();
            $subscription_transaction->save();
            $restaurant_subscription->save();
            DB::commit();
            $subscription_transaction->restaurant_subscription_id = $restaurant_subscription->id;
            $subscription_transaction->save();

            SubscriptionBillingAndRefundHistory::where([
                'restaurant_id' => $restaurant->id,
                'transaction_type' => 'pending_bill',
                'is_success' => 0
            ])->update([
                'is_success' => 1,
                'reference' => 'payment_via_' . $payment_method . ' _transaction_id_' . $subscription_transaction->id
            ]);

            if ($reference == 'plan_shift_by_admin') {
                $billing = new SubscriptionBillingAndRefundHistory();
                $billing->restaurant_id = $restaurant->id;
                $billing->subscription_id = $restaurant_subscription->id;
                $billing->package_id = $restaurant_subscription->package_id;
                $billing->transaction_type = 'pending_bill';
                $billing->is_success = 0;
                $billing->amount = $package->price;
                $billing->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            info(["line___{$e->getLine()}", $e->getMessage()]);
            return false;
        }

        if (data_get(self::subscriptionConditionsCheck(restaurant_id: $restaurant->id, package_id: $package->id), 'disable_item_count') > 0) {
            $disable_item_count = data_get(Helpers::subscriptionConditionsCheck(restaurant_id: $restaurant->id, package_id: $package->id), 'disable_item_count');
            $restaurant->food_section = 0;
            $restaurant->save();

            Food::where('restaurant_id', $restaurant->id)->oldest()->take($disable_item_count)->update([
                'status' => 0
            ]);
        }

        try {
            if ($type == 'renew') {
                $notification_status = Helpers::getNotificationStatusData('restaurant', 'restaurant_subscription_renew');
                $restaurant_push_notification_status = Helpers::getRestaurantNotificationStatusData($restaurant->id, 'restaurant_subscription_renew');
                $title = translate('subscription_renewed');
                $des = translate('Your_subscription_successfully_renewed');
            } elseif ($type != 'renew' && $subscription_transaction->plan_type != 'first_purchased') {
                $des = translate('Your_subscription_successfully_shifted');
                $title = translate('subscription_shifted');
                $notification_status = Helpers::getNotificationStatusData('restaurant', 'restaurant_subscription_shift');
                $restaurant_push_notification_status = Helpers::getRestaurantNotificationStatusData($restaurant->id, 'restaurant_subscription_shift');
            }

            if ($notification_status?->push_notification_status == 'active' && $restaurant_push_notification_status?->push_notification_status == 'active' && $restaurant?->vendor?->firebase_token) {
                $data = [
                    'title' => $title ?? '',
                    'description' => $des ?? '',
                    'order_id' => '',
                    'image' => '',
                    'type' => 'subscription',
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($restaurant?->vendor?->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $restaurant?->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            if (config('mail.status') && Helpers::get_mail_status('subscription_renew_mail_status_restaurant') == '1' && $type == 'renew' && $notification_status?->mail_status == 'active' && $restaurant_push_notification_status?->mail_status == 'active') {
                Mail::to($restaurant->email)->send(new SubscriptionRenewOrShift($type, $restaurant->name));
            }
            if (config('mail.status') && Helpers::get_mail_status('subscription_shift_mail_status_restaurant') == '1' && $type != 'renew' && $subscription_transaction->plan_type != 'first_purchased' && $notification_status?->mail_status == 'active' && $restaurant_push_notification_status?->mail_status == 'active') {
                Mail::to($restaurant->email)->send(new SubscriptionRenewOrShift($type, $restaurant->name));
            }

            $notification_status = Helpers::getNotificationStatusData('restaurant', 'restaurant_subscription_success');
            $restaurant_push_notification_status = Helpers::getRestaurantNotificationStatusData($restaurant->id, 'restaurant_subscription_success');

            if (config('mail.status') && Helpers::get_mail_status('subscription_successful_mail_status_restaurant') == '1' && $notification_status?->mail_status == 'active' && $restaurant_push_notification_status?->mail_status == 'active' && $subscription_transaction->plan_type == 'first_purchased') {
                $url = route('subscription_invoice', ['id' => base64_encode($subscription_transaction->id)]);
                Mail::to($restaurant->email)->send(new SubscriptionSuccessful($restaurant->name, $url));
            }

            if ($notification_status?->push_notification_status == 'active' && $restaurant_push_notification_status?->push_notification_status == 'active' && $restaurant?->vendor?->firebase_token && $subscription_transaction->plan_type == 'first_purchased') {
                $data = [
                    'title' => translate('subscription_successful'),
                    'description' => translate('You_are_successfully_subscribed'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'subscription',
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($restaurant?->vendor?->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $restaurant?->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }

        return $subscription_transaction->id;
    }

    public static function subscriptionConditionsCheck($restaurant_id, $package_id)
    {
        $restaurant = Restaurant::findOrFail($restaurant_id);
        $package = SubscriptionPackage::withoutGlobalScope('translate')->find($package_id);

        $total_food = $restaurant->foods()->withoutGlobalScope(\App\Scopes\RestaurantScope::class)->count();
        if ($package->max_product != 'unlimited' && $total_food >= $package->max_product) {
            return ['disable_item_count' => $total_food - $package->max_product];
        }
        return null;
    }

    public static function create_subscription_order_logs()
    {
        $order_schedule_day = now()->dayOfWeek;
        $orders = Order::HasSubscriptionTodayGet()
            ->with(['restaurant.schedule_today', 'subscription.schedule_today'])
            ->whereHas('restaurant.schedules', function ($q) use ($order_schedule_day) {
                $q->where('day', $order_schedule_day);
            })
            ->get();

        foreach ($orders as $order) {
            foreach ($order->restaurant->schedule_today as $rest_sh) {
                if (Carbon::parse($rest_sh->opening_time) <= Carbon::parse($order->subscription->schedule_today->time) && Carbon::parse($rest_sh->closing_time) >= Carbon::parse($order->subscription->schedule_today->time)) {
                    OrderLogic::create_subscription_log($order->id);
                }
            }
        }
        return true;
    }

    public static function checkOldSubscriptionSettings()
    {
        if (BusinessSetting::where(['key' => 'free_trial_period'])->exists()) {
            $old_trial_data = BusinessSetting::where(['key' => 'free_trial_period'])->first();
            $data = json_decode($old_trial_data?->value, true);
            if (isset($data['status']) && $data['status'] == 1) {
                $type = data_get($data, 'type');

                if ($type == 'year') {
                    $free_trial_period = data_get($data, 'data') * 365;
                } elseif ($type == 'month') {
                    $free_trial_period = data_get($data, 'data') * 30;
                } else {
                    $free_trial_period = data_get($data, 'data', 1);
                }

                $keys = ['subscription_free_trial_days', 'subscription_free_trial_type', 'subscription_free_trial_status'];
                foreach ($keys as $value) {
                    $status = BusinessSetting::firstOrNew([
                        'key' => $value
                    ]);
                    if ($value == 'subscription_free_trial_days') {
                        $status->value = $free_trial_period;
                    } elseif ($value == 'subscription_free_trial_type') {
                        $status->value = $type ?? 'day';
                    } elseif ($value == 'subscription_free_trial_status') {
                        $status->value = $data['status'];
                    }
                    $status->save();
                }
            }

            $old_trial_data?->delete();
        }

        return true;
    }

    public static function subscriptionPayment($restaurant_id, $package_id, $payment_gateway, $url, $pending_bill = 0, $type = 'payment', $payment_platform = 'web')
    {
        $restaurant = Restaurant::where('id', $restaurant_id)->first();
        $package = SubscriptionPackage::where('id', $package_id)->first();
        $type = $type ?? 'payment';

        $payer = new Payer(
            $restaurant->name,
            $restaurant->email,
            $restaurant->phone,
            ''
        );
        $restaurant_logo = BusinessSetting::where(['key' => 'logo'])->first();
        $additional_data = [
            'business_name' => BusinessSetting::where(['key' => 'business_name'])->first()?->value,
            'business_logo' => Helpers::get_full_url('business', $restaurant_logo?->value, $restaurant_logo?->storage[0]?->value ?? 'public')
        ];
        $payment_info = new PaymentInfo(
            success_hook: 'sub_success',
            failure_hook: 'sub_fail',
            currency_code: Helpers::currency_code(),
            payment_method: $payment_gateway,
            payment_platform: $payment_platform,
            payer_id: $restaurant->id,
            receiver_id: $package->id,
            additional_data: $additional_data,
            payment_amount: $package->price + $pending_bill,
            external_redirect_link: $url,
            attribute: 'restaurant_subscription_' . $type,
            attribute_id: $package->id,
        );
        $receiver_info = new Receiver('Admin', 'example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }
}
