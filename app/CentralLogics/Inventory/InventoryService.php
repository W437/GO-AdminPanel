<?php

namespace App\CentralLogics\Inventory;

use App\Models\AddOn;
use App\Models\VariationOption;

class InventoryService
{
    public static function calculateAddonPrice($addons, $addOnQtys, bool $incrementCount = false, array $oldSelectedAddons = []): ?array
    {
        $addOnsCost = 0;
        $data = [];

        if (!$addons) {
            return null;
        }

        foreach ($addons as $key => $addon) {
            $addOnQty = $addOnQtys[$key] ?? 1;

            if ($addon->stock_type != 'unlimited') {
                $availableStock = $addon->addon_stock;

                if (data_get($oldSelectedAddons, $addon->id)) {
                    $availableStock += data_get($oldSelectedAddons, $addon->id);
                }

                if ($availableStock <= 0 || $availableStock < $addOnQty) {
                    return [
                        'out_of_stock' => $addon->name . ' ' . translate('Addon_is_out_of_stock_!!!'),
                        'id' => $addon->id,
                        'current_stock' => $availableStock > 0 ? $availableStock : 0,
                        'type' => 'addon',
                    ];
                }
            }

            if ($incrementCount) {
                $addon->increment('sell_count', $addOnQty);
            }

            $data[] = [
                'id' => $addon->id,
                'name' => $addon->name,
                'price' => $addon->price,
                'quantity' => $addOnQty,
            ];

            $addOnsCost += $addon['price'] * $addOnQty;
        }

        return ['addons' => $data, 'total_add_on_price' => $addOnsCost];
    }

    public static function addonAndVariationStockCheck(
        $product,
        int $quantity = 1,
        $addOnQtys = 1,
        $variationOptions = null,
        $addOnIds = null,
        bool $incrementCount = false,
        array $oldSelectedVariations = [],
        int $oldSelectedWithoutVariation = 0,
        array $oldSelectedAddons = []
    ) {
        if ($product?->stock_type && $product?->stock_type !== 'unlimited') {
            $availableMainStock = $product->item_stock + $oldSelectedWithoutVariation;

            if ($availableMainStock <= 0 || $availableMainStock < $quantity) {
                return [
                    'out_of_stock' => $availableMainStock > 0
                        ? translate('Only') . ' ' . $availableMainStock . ' ' . translate('Quantity_is_abailable_for') . ' ' . $product?->name
                        : $product?->name . ' ' . translate('is_out_of_stock_!!!'),
                    'id' => $product->id,
                    'current_stock' => $availableMainStock > 0 ? $availableMainStock : 0,
                ];
            }

            if ($incrementCount) {
                $product->increment('sell_count', $quantity);
            }

            if (is_array($variationOptions) && (data_get($variationOptions, 0) !== '' || data_get($variationOptions, 0) !== null)) {
                $variationModels = VariationOption::whereIn('id', $variationOptions)->get();
                foreach ($variationModels as $variationOption) {
                    if ($variationOption->stock_type !== 'unlimited') {
                        $availableStock = $variationOption->total_stock - $variationOption->sell_count;

                        if (data_get($oldSelectedVariations, $variationOption->id)) {
                            $availableStock += data_get($oldSelectedVariations, $variationOption->id);
                        }

                        if ($availableStock <= 0 || $availableStock < $quantity) {
                            return [
                                'out_of_stock' => $availableStock > 0
                                    ? translate('Only') . ' ' . $availableStock . ' ' . translate('Quantity_is_abailable_for') . ' ' . $product?->name . ' \'s ' . $variationOption->option_name . ' ' . translate('Variation_!!!')
                                    : $product?->name . ' \'s ' . $variationOption->option_name . ' ' . translate('Variation_is_out_of_stock_!!!'),
                                'id' => $variationOption->id,
                                'current_stock' => $availableStock > 0 ? $availableStock : 0,
                            ];
                        }

                        if ($incrementCount) {
                            $variationOption->increment('sell_count', $quantity);
                        }
                    }
                }
            }
        }

        if (is_array($addOnIds) && count($addOnIds) > 0) {
            $addons = AddOn::whereIn('id', $addOnIds)->get();

            return self::calculateAddonPrice(
                addons: $addons,
                addOnQtys: $addOnQtys,
                incrementCount: $incrementCount,
                oldSelectedAddons: $oldSelectedAddons
            );
        }

        return null;
    }

    public static function decreaseSellCount($orderDetails): bool
    {
        foreach ($orderDetails as $detail) {
            $optionIds = [];
            if ($detail->variation != '[]') {
                foreach (json_decode($detail->variation, true) as $value) {
                    foreach (data_get($value, 'values', []) as $item) {
                        if (data_get($item, 'option_id', null) != null) {
                            $optionIds[] = data_get($item, 'option_id', null);
                        }
                    }
                }
                VariationOption::whereIn('id', $optionIds)
                    ->where('sell_count', '>', 0)
                    ->decrement('sell_count', $detail->quantity);
            }

            $detail->food()->where('sell_count', '>', 0)->decrement('sell_count', $detail->quantity);

            foreach (json_decode($detail->add_ons, true) as $addOn) {
                if (data_get($addOn, 'id', null) != null) {
                    AddOn::where('id', data_get($addOn, 'id', null))
                        ->where('sell_count', '>', 0)
                        ->decrement('sell_count', data_get($addOn, 'quantity', 1));
                }
            }
        }

        return true;
    }
}
