<?php

namespace App\CentralLogics\Payments;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Traits\PaymentGatewayTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentUtilityService
{
    use PaymentGatewayTrait;

    public static function offline_payment_formater($user_data)
    {
        $userInputs = [];

        $user_inputes = json_decode($user_data->payment_info, true);
        $method_name = $user_inputes['method_name'];
        $method_id = $user_inputes['method_id'];

        foreach ($user_inputes as $key => $value) {
            if (!in_array($key, ['method_name', 'method_id'])) {
                $userInput = [
                    'user_input' => $key,
                    'user_data' => $value,
                ];
                $userInputs[] = $userInput;
            }
        }

        $data = [
            'status' => $user_data->status,
            'method_id' => $method_id,
            'method_name' => $method_name,
            'customer_note' => $user_data->customer_note,
            'admin_note' => $user_data->note,
        ];

        return [
            'input' => $userInputs,
            'data' => $data,
            'method_fields' => json_decode($user_data->method_fields, true),
        ];
    }

    public static function getActivePaymentGateways()
    {
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }
        $digital_payment = Helpers::get_business_settings('digital_payment');

        if ($digital_payment && $digital_payment['status'] == 0) {
            return [];
        }

        $published_status = 0;
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        if ($published_status == 1) {
            $methods = DB::table('addon_settings')->where('is_active', 1)->where('settings_type', 'payment_config')->get();
            $env = env('APP_ENV') == 'live' ? 'live' : 'test';
            $credentials = $env . '_values';
        } else {
            $methods = DB::table('addon_settings')->where('is_active', 1)->whereIn('settings_type', ['payment_config'])->whereIn('key_name', ['ssl_commerz', 'paypal', 'stripe', 'razor_pay', 'senang_pay', 'paytabs', 'paystack', 'paymob_accept', 'paytm', 'flutterwave', 'liqpay', 'bkash', 'mercadopago'])->get();
            $env = env('APP_ENV') == 'live' ? 'live' : 'test';
            $credentials = $env . '_values';
        }

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additional_data = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additional_data?->gateway_title,
                    'gateway_image' => $additional_data?->gateway_image,
                    'gateway_image_full_url' => Helpers::get_full_url('payment_modules/gateway_image', $additional_data?->gateway_image, $additional_data?->storage ?? 'public')
                ];
            }
        }
        return $data;
    }

    public static function checkCurrency($data, $type = null)
    {
        $digital_payment = Helpers::get_business_settings('digital_payment');

        if ($digital_payment && $digital_payment['status'] == 1) {
            if ($type === null) {
                foreach (self::getActivePaymentGateways() as $payment_gateway) {
                    $supported = self::getPaymentGatewaySupportedCurrencies($payment_gateway['gateway']);

                    if (!empty($supported) && !array_key_exists($data, $supported)) {
                        return $payment_gateway['gateway'];
                    }
                }
            } elseif ($type == 'payment_gateway') {
                $currency = BusinessSetting::where('key', 'currency')->first()?->value;
                $supported = self::getPaymentGatewaySupportedCurrencies($data);

                if (!empty($supported) && !array_key_exists($currency, $supported)) {
                    return $data;
                }
            }
        }

        return true;
    }
}
