<?php

namespace App\CentralLogics\Pricing;

use App\CentralLogics\Helpers;

class PricingService
{
    public static function tax_calculate($food, $price)
    {
        if ($food['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $food['tax'];
        } else {
            $price_tax = $food['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['restaurant_discount']) {
            $price_discount = ($price / 100) * $product['restaurant_discount'];
        } else if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function get_product_discount($product)
    {
        $restaurant_discount = Helpers::get_restaurant_discount($product->restaurant);
        if ($restaurant_discount) {
            $discount = $restaurant_discount['discount'] . ' %';
        } else if ($product['discount_type'] == 'percent') {
            $discount = $product['discount'] . ' %';
        } else {
            $discount = Helpers::format_currency($product['discount']);
        }
        return $discount;
    }

    public static function product_discount_calculate($product, $price, $restaurant)
    {
        $restaurant_discount = Helpers::get_restaurant_discount($restaurant);
        if (isset($restaurant_discount)) {
            $price_discount = ($price / 100) * $restaurant_discount['discount'];
        } else if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function product_discount_calculate_data($product, $restaurant)
    {
        $restaurant_discount = Helpers::get_restaurant_discount($restaurant);
        if (isset($restaurant_discount)) {
            $price_discount = $restaurant_discount['discount'];
            $original_discount_type = 'percent';
        } else {
            $original_discount_type = $product['discount_type'];
            $price_discount = $product['discount'];
        }
        return [
            'discount_percentage' => $price_discount,
            'original_discount_type' => $original_discount_type,
        ];
    }

    public static function food_discount_calculate($product, $price, $restaurant, $check_restaurant_discount = true)
    {
        $restaurant_discount = null;
        $restaurant_price_discount = 0;
        $restaurant_discount_percentage = 0;

        if ($check_restaurant_discount) {
            $restaurant_discount = Helpers::get_restaurant_discount($restaurant);
            if (isset($restaurant_discount)) {
                $restaurant_price_discount = ($price / 100) * $restaurant_discount['discount'];
                $restaurant_discount_percentage = $restaurant_discount['discount'];
            }
        }

        $discount_percentage = $product['discount'];
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }

        $discount_percentage = isset($restaurant_discount) && $price_discount == $restaurant_price_discount ? $restaurant_discount_percentage : $discount_percentage ?? 0;

        $price_discount = max($restaurant_price_discount, $price_discount);
        $discount_type = isset($restaurant_discount) && $price_discount == $restaurant_price_discount ? 'admin' : 'discount_on_product';

        return [
            'discount_type' => $discount_type,
            'discount_amount' => $price_discount,
            'discount_percentage' => $discount_type == 'admin' ? $restaurant_discount['discount'] : $product['discount'],
            'original_discount_type' => $discount_type == 'admin' ? 'percent' : $product['discount_type'],
        ];
    }

    public static function get_price_range($product, $discount = false)
    {
        $lowest_price = $product->price;

        if ($discount) {
            $lowest_price -= self::product_discount_calculate($product, $lowest_price, $product->restaurant);
        }
        $lowest_price = Helpers::format_currency($lowest_price);

        return $lowest_price;
    }
}
