<?php

namespace App\CentralLogics\Logistics;

use App\Models\BusinessSetting;
use App\Models\Vehicle;
use App\Models\Zone;
use MatanYadaev\EloquentSpatial\Objects\Point;

class LogisticsService
{
    public static function getDeliveryFee($restaurant): string
    {
        if (!request()->header('latitude') || !request()->header('longitude')) {
            return 'out_of_range';
        }

        $zone = Zone::where('id', $restaurant->zone_id)
            ->whereContains('coordinates', new Point(request()->header('latitude'), request()->header('longitude'), POINT_SRID))
            ->first();

        if (!$zone) {
            return 'out_of_range';
        }

        if (isset($restaurant->distance) && $restaurant->distance > 0) {
            $distance = round($restaurant->distance / 1000, 5);
        } elseif ($restaurant->latitude && $restaurant->longitude) {
            $originCoordinates = [
                $restaurant->latitude,
                $restaurant->longitude,
            ];
            $destinationCoordinates = [
                request()->header('latitude'),
                request()->header('longitude'),
            ];
            $distance = round(self::getDistance($originCoordinates, $destinationCoordinates), 5);
        } else {
            return 'out_of_range';
        }

        if ($restaurant['self_delivery_system'] == 1) {
            if ($restaurant->free_delivery == 1) {
                return 'free_delivery';
            }

            if ($restaurant->free_delivery_distance_status == 1 && $distance <= $restaurant->free_delivery_distance_value) {
                return 'free_delivery';
            }

            $perKmShippingCharge = $restaurant->per_km_shipping_charge ?? 0;
            $minimumShippingCharge = $restaurant->minimum_shipping_charge ?? 0;
            $extraCharges = 0;
            $increased = 0;
        } else {
            $businessSettings = BusinessSetting::whereIn('key', ['free_delivery_over', 'free_delivery_distance', 'admin_free_delivery_status', 'admin_free_delivery_option'])
                ->pluck('value', 'key');

            $freeDeliveryDistance = (float)($businessSettings['free_delivery_distance'] ?? 0);
            $adminFreeDeliveryStatus = (int)($businessSettings['admin_free_delivery_status'] ?? 0);
            $adminFreeDeliveryOption = $businessSettings['admin_free_delivery_option'] ?? null;

            if ($adminFreeDeliveryStatus == 1) {
                $isFreeDelivery = $adminFreeDeliveryOption === 'free_delivery_to_all_store'
                    || ($adminFreeDeliveryOption === 'free_delivery_by_specific_criteria'
                        && ($freeDeliveryDistance > 0 && $distance <= $freeDeliveryDistance));

                if ($isFreeDelivery) {
                    return 'free_delivery';
                }
            }

            $perKmShippingCharge = $zone->per_km_shipping_charge ?? 0;
            $minimumShippingCharge = $zone->minimum_shipping_charge ?? 0;
            $increased = 0;

            if ($zone->increased_delivery_fee_status == 1) {
                $increased = $zone->increased_delivery_fee ?? 0;
            }

            $vehicleCharge = self::vehicleExtraCharge($distance);
            $extraCharges = (float)($vehicleCharge['extra_charge'] ?? 0);
        }

        $originalDeliveryCharge = ($distance * $perKmShippingCharge > $minimumShippingCharge)
            ? $distance * $perKmShippingCharge + $extraCharges
            : $minimumShippingCharge + $extraCharges;

        if ($increased > 0 && $originalDeliveryCharge > 0) {
            $increasedFee = ($originalDeliveryCharge * $increased) / 100;
            $originalDeliveryCharge += $increasedFee;
        }

        return (string)$originalDeliveryCharge;
    }

    public static function getDistance(array $originCoordinates, array $destinationCoordinates, string $unit = 'K'): float
    {
        $lat1 = (float)$originCoordinates[0];
        $lat2 = (float)$destinationCoordinates[0];
        $lon1 = (float)$originCoordinates[1];
        $lon2 = (float)$destinationCoordinates[1];

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == 'K') {
            return $miles * 1.609344;
        }

        if ($unit == 'N') {
            return $miles * 0.8684;
        }

        return $miles;
    }

    public static function vehicleExtraCharge(float $distance): array
    {
        $vehicle = Vehicle::active()
            ->where(function ($query) use ($distance) {
                $query->where('starting_coverage_area', '<=', $distance)
                    ->where('maximum_coverage_area', '>=', $distance)
                    ->orWhere(function ($query) use ($distance) {
                        $query->where('starting_coverage_area', '>=', $distance);
                    });
            })
            ->orderBy('starting_coverage_area')
            ->first();

        if (empty($vehicle)) {
            $vehicle = Vehicle::active()
                ->orderBy('maximum_coverage_area', 'desc')
                ->first();
        }

        return [
            'extra_charge' => $vehicle->extra_charges ?? 0,
            'vehicle_id' => $vehicle->id ?? null,
        ];
    }
}
