<?php

namespace App\CentralLogics\Presentation;

use App\Models\Category;
use App\Models\DataSetting;
use App\Models\Restaurant;
use App\Models\Zone;
use Carbon\Carbon;

class PresentationService
{
    public static function exportGenerator($datas)
    {
        foreach ($datas as $data) {
            yield $data;
        }
    }

    public static function formatReactServices($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'Id' => $item['id'],
                'Title' => $item['title'],
                'Sub_title' => $item['sub_title'],
                'Status' => $item['status'] == 1 ? 'active' : 'inactive',
            ];
        }

        return $storage;
    }

    public static function formatReactPromotionalBanners($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[] = [
                'Id' => $item['id'],
                'Title' => $item['title'],
                'Description' => $item['description'],
                'Status' => $item['status'] == 1 ? 'active' : 'inactive',
            ];
        }

        return $storage;
    }

    public static function formatTextVariables($value, $userName = null, $restaurantName = null, $deliveryManName = null, $transactionId = null, $orderId = null, $advertisementId = null)
    {
        $data = $value;

        if ($value) {
            if ($userName) {
                $data = str_replace("{userName}", $userName, $data);
            }

            if ($restaurantName) {
                $data = str_replace("{restaurantName}", $restaurantName, $data);
            }

            if ($deliveryManName) {
                $data = str_replace("{deliveryManName}", $deliveryManName, $data);
            }

            if ($transactionId) {
                $data = str_replace("{transactionId}", $transactionId, $data);
            }

            if ($orderId) {
                $data = str_replace("{orderId}", $orderId, $data);
            }
            if ($advertisementId) {
                $data = str_replace("{advertisementId}", $advertisementId, $data);
            }
        }

        return $data;
    }

    public static function getLoginUrl($type)
    {
        $data = DataSetting::whereIn('key', ['restaurant_employee_login_url', 'restaurant_login_url', 'admin_employee_login_url', 'admin_login_url'])
            ->pluck('key', 'value')
            ->toArray();

        return array_search($type, $data);
    }

    public static function formatDateTime($data)
    {
        $time = config('timeformat') ?? 'H:i';

        return Carbon::parse($data)->locale(app()->getLocale())->translatedFormat('d M Y ' . $time);
    }

    public static function formatDate($data)
    {
        return Carbon::parse($data)->locale(app()->getLocale())->translatedFormat('d M Y');
    }

    public static function formatTime($data)
    {
        $time = config('timeformat') ?? 'H:i';

        return Carbon::parse($data)->locale(app()->getLocale())->translatedFormat($time);
    }

    public static function getZonesName($zones)
    {
        if (is_array($zones)) {
            $data = Zone::whereIn('id', $zones)->pluck('name')->toArray();
        } else {
            $data = Zone::where('id', $zones)->pluck('name')->toArray();
        }

        return implode(', ', $data);
    }

    public static function getRestaurantName($restaurant)
    {
        if (is_array($restaurant)) {
            $data = Restaurant::whereIn('id', $restaurant)->pluck('name')->toArray();
        } else {
            $data = Restaurant::where('id', $restaurant)->pluck('name')->toArray();
        }

        return implode(', ', $data);
    }

    public static function getCategoryName($id)
    {
        $id = json_decode($id, true);
        $id = data_get($id, '0.id', 'NA');

        return Category::with('translations')->where('id', $id)->first()?->name ?? translate('messages.uncategorize');
    }

    public static function getSubCategoryName($id)
    {
        $id = json_decode($id, true);
        $id = data_get($id, '1.id', 'NA');

        return Category::where('id', $id)->first()?->name;
    }

    public static function getFoodVariations($variations)
    {
        try {
            $data = [];
            $data2 = [];
            foreach ((array)json_decode($variations, true) as $key => $choice) {
                if (data_get($choice, 'values', null)) {
                    foreach (data_get($choice, 'values', []) as $k => $v) {
                        $data2[$k] = $v['label'];
                    }
                    $data[$choice['name']] = $data2;
                }
            }

            return str_ireplace(['\'', '"', '{', '}', '[', ']', '<', '>', '?'], ' ', json_encode($data));
        } catch (\Exception $ex) {
            info(["line___{$ex->getLine()}", $ex->getMessage()]);

            return 0;
        }
    }

    public static function getImageForExport($imagePath)
    {
        $temporaryImage = self::getTemporaryImageForExport($imagePath);
        $pngImage = imagecreatetruecolor(imagesx($temporaryImage), imagesy($temporaryImage));
        imagealphablending($pngImage, false);
        imagesavealpha($pngImage, true);
        imagecopy($pngImage, $temporaryImage, 0, 0, 0, 0, imagesx($temporaryImage), imagesy($temporaryImage));

        return $pngImage;
    }

    public static function getTemporaryImageForExport($imagePath)
    {
        try {
            $imageData = file_get_contents($imagePath);

            return imagecreatefromstring($imageData);
        } catch (\Throwable $th) {
            $imageData = file_get_contents(dynamicAsset('public/assets/admin/img/100x100/no-image-found.png'));

            return imagecreatefromstring($imageData);
        }
    }
}
