<?php

namespace App\CentralLogics\Config;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\DataSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ConfigService
{
    public static function get_business_settings($key, $json_decode = true, $relations = [])
    {
        try {
            static $allSettings = null;

            $configKey = $key . '_conf';
            if (Config::has($configKey)) {
                $data = Config::get($configKey);
            } else {
                if (is_null($allSettings)) {
                    $allSettings = Cache::rememberForever('business_settings_all_data', function () {
                        return BusinessSetting::select('key', 'value')->get();
                    });
                }

                $data = $allSettings->firstWhere('key', $key);
                if ($data && !empty($relations)) {
                    $data->loadMissing($relations);
                }
                Config::set($configKey, $data);
            }

            if (!isset($data['value'])) {
                return null;
            }

            $value = $data['value'];
            if ($json_decode && is_string($value)) {
                $decoded = json_decode($value, true);
                return is_null($decoded) ? $value : $decoded;
            }

            return $value;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function currency_code()
    {
        if (!config('currency')) {
            $currency = self::get_business_settings('currency') ?? BusinessSetting::where(['key' => 'currency'])->first()?->value;
            Config::set('currency', $currency);
        } else {
            $currency = config('currency');
        }

        return $currency;
    }

    public static function currency_symbol()
    {
        if (!config('currency_symbol')) {
            $currency_symbol = Currency::where(['currency_code' => self::currency_code()])->first()?->currency_symbol;
            Config::set('currency_symbol', $currency_symbol);
        } else {
            $currency_symbol = config('currency_symbol');
        }

        return $currency_symbol;
    }

    public static function format_currency($value)
    {
        if (!config('currency_symbol_position')) {
            $currency_symbol_position = self::get_business_settings('currency_symbol_position') ?? BusinessSetting::where(['key' => 'currency_symbol_position'])->first()?->value;
            Config::set('currency_symbol_position', $currency_symbol_position);
        } else {
            $currency_symbol_position = config('currency_symbol_position');
        }

        return $currency_symbol_position == 'right'
            ? number_format($value, config('round_up_to_digit')) . ' ' . self::currency_symbol()
            : self::currency_symbol() . ' ' . number_format($value, config('round_up_to_digit'));
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function get_settings_storage($name)
    {
        $config = 'public';
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data) && count($data->storage) > 0) {
            $config = $data->storage[0]['value'];
        }
        return $config;
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $oldValue = env($envKey);
        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function insert_business_settings_key($key, $value = null)
    {
        $data = BusinessSetting::where('key', $key)->first();
        if (!$data) {
            Helpers::businessUpdateOrInsert(['key' => $key], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return true;
    }

    public static function insert_data_settings_key($key, $type, $value = null)
    {
        $data = DataSetting::where('key', $key)->where('type', $type)->first();
        if (!$data) {
            DataSetting::updateOrCreate(['key' => $key, 'type' => $type], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return true;
    }
}
