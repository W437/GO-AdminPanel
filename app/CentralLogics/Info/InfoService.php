<?php

namespace App\CentralLogics\Info;

use App\CentralLogics\Helpers;
use App\Models\User;
use App\Models\AddOn;
use App\Models\BusinessSetting;
use App\Models\Translation;

class InfoService
{
    public static function get_customer_name($id)
    {
        $user = User::where('id', $id)->first();

        return $user?->f_name . ' ' . $user?->l_name;
    }

    public static function get_addon_data($id)
    {
        try {
            $data = [];
            $addon = AddOn::whereIn('id', json_decode($id, true))->get(['name', 'price'])->toArray();
            foreach ($addon as $key => $value) {
                $data[$key] = $value['name'] . ' - ' . Helpers::format_currency($value['price']);
            }
            return str_ireplace(['\'', '"', '{', '}', '[', ']', '<', '>', '?'], ' ', json_encode($data, JSON_UNESCAPED_UNICODE));
        } catch (\Exception $ex) {
            info(["line___{$ex->getLine()}", $ex->getMessage()]);
            return 0;
        }
    }

    public static function get_business_data($name)
    {
        return BusinessSetting::where('key', $name)->first()?->value;
    }

    public static function add_or_update_translations($request, $key_data, $name_field, $model_name, $data_id, $data_value)
    {
        try {
            $model = 'App\\Models\\' . $model_name;
            $default_lang = str_replace('_', '-', app()->getLocale());
            foreach ($request->lang as $index => $key) {
                if ($default_lang == $key && !($request->{$name_field}[$index])) {
                    if ($key != 'default') {
                        Translation::updateorcreate(
                            [
                                'translationable_type' => $model,
                                'translationable_id' => $data_id,
                                'locale' => $key,
                                'key' => $key_data
                            ],
                            ['value' => $data_value]
                        );
                    }
                } else {
                    if ($request->{$name_field}[$index] && $key != 'default') {
                        Translation::updateorcreate(
                            [
                                'translationable_type' => $model,
                                'translationable_id' => $data_id,
                                'locale' => $key,
                                'key' => $key_data
                            ],
                            ['value' => $request->{$name_field}[$index]]
                        );
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            info(["line___{$e->getLine()}", $e->getMessage()]);
            return false;
        }
    }
}
