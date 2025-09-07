<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\Food;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\ItemCampaign;
use App\Scopes\RestaurantScope;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\CentralLogics\CouponLogic;
use App\Models\AddOn;

trait PlaceNewOrder
{
    private function makeOrderDetails($carts, $order, $restaurant)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $restaurant_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $variations = [];
        $discount_on_product_by = 'vendor';
        foreach ($carts as $c) {
            $isCampaign = false;
            if ($c['item_type'] === 'App\Models\ItemCampaign' || $c['item_type'] === 'AppModelsItemCampaign') {
                $product = ItemCampaign::active()->find($c['item_id']);
                $isCampaign = true;
            } else {
                $product = Food::withoutGlobalScope(RestaurantScope::class)->active()->find($c['item_id']);
            }
            if ($product) {
                if ($product->restaurant_id != $order->restaurant_id) {
                    return [
                        'status_code' => 403,
                        'code' => 'different_restaurants',
                        'message' => translate('messages.Please_select_items_from_the_same_restaurant'),
                    ];
                }

                if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                    return [
                        'status_code' => 403,
                        'code' => 'quantity',
                        'message' => translate('messages.maximum_cart_quantity_limit_over'),
                    ];
                }

                $product_variations = json_decode($product->variations, true);

                if (count($product_variations)) {
                    $variation_data = Helpers::get_varient($product_variations, gettype($c['variations']) == 'array' ? $c['variations'] : json_decode($c['variations'], true));
                    $price = $product['price'] + $variation_data['price'];
                    $variations = $variation_data['variations'];
                } else {
                    $price = $product['price'];
                }

                $product = Helpers::product_data_formatting(data:$product,multi_data: false,trans: false,local: app()->getLocale(),maxDiscount:false);
                $addon_data = Helpers::calculate_addon_price(AddOn::withoutGlobalScope(RestaurantScope::class)->whereIn('id', $c['add_on_ids'])->get(), $c['add_on_qtys']);
                $product_discount = Helpers::food_discount_calculate($product, $price, $restaurant, false);

                $or_d = [
                    'food_id' => $isCampaign ? null : $c['item_id'],
                    'item_campaign_id' => $isCampaign ? $c['item_id'] : null,
                    'food_details' => json_encode($product),
                    'quantity' => $c['quantity'],
                    'price' => round($price, config('round_up_to_digit')),

                    'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                    'tax_amount' => 0,
                    'tax_status' => null,

                    'discount_on_product_by' => $product_discount['discount_type'],
                    'discount_type' => $product_discount['discount_type'],
                    'discount_on_food' => $product_discount['discount_amount'],
                    'discount_percentage' => $product_discount['discount_percentage'],

                    // 'variant' => json_encode($c['variant']),
                    'variation' => json_encode($variations),
                    'add_ons' => json_encode($addon_data['addons']),

                    'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                    'addon_discount' => 0,

                    'created_at' => now(),
                    'updated_at' => now()
                ];


                $total_addon_price += $or_d['total_add_on_price'];
                $product_price += $price * $or_d['quantity'];
                $restaurant_discount_amount += $or_d['discount_on_food'] * $or_d['quantity'] ?? 0;
                $order_details[] = $or_d;
                $addon_data[] = $addon_data['addons'];
            } else {
                return [
                    'status_code' => 403,
                    'code' => 'not_found',
                    'message' => translate('messages.product_not_found'),
                ];
            }
        }


        $discount_on_product_by = 'vendor';
        $discount = $restaurant_discount_amount;
        $restaurantDiscount = Helpers::get_restaurant_discount($restaurant);
        if (isset($restaurantDiscount)) {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase']);
            $discount= $admin_discount;
            $discount_on_product_by = 'admin';
            foreach ($order_details as $key => $detail_data) {
                if($admin_discount>0){
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = $restaurantDiscount['discount'];
                    $order_details[$key]['discount_on_food'] =  Helpers::checkAdminDiscount(price: $product_price , discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);
                } else {
                    $order_details[$key]['discount_on_product_by'] = null;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = 0;
                    $order_details[$key]['discount_on_food'] =  0;
                }
            }
        }

        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'restaurant_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by,
            'product_data' => $product_data

        ];
    }

    private function makePosOrderDetails($carts, $restaurant)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $restaurant_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $variations = [];
        $discount_on_product_by = 'vendor';
        foreach ($carts as $c) {
            if (is_array($c)) {
                $isCampaign = false;
                if (isset($c['item_type']) && ($c['item_type'] === 'App\Models\ItemCampaign' || $c['item_type'] === 'AppModelsItemCampaign')) {
                    $product = ItemCampaign::with('module')->active()->find($c['item_id']);
                    $isCampaign = true;
                } else {
                    $product = Food::withoutGlobalScope(RestaurantScope::class)->active()->find(isset($c['item_id']) ?: $c['id']);
                }

                if ($product) {
                    if ($product->restaurant_id != $restaurant->id) {
                        return [
                            'status_code' => 403,
                            'code' => 'different_restaurants',
                            'message' => translate('messages.Please_select_food_from_the_same_restaurant'),
                        ];
                    }

                    if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                        return [
                            'status_code' => 403,
                            'code' => 'quantity',
                            'message' => translate('messages.maximum_cart_quantity_limit_over'),
                        ];
                    }

                    $product_variations = json_decode($product->variations, true);

                    if (count($product_variations)) {
                        $variation_data = Helpers::get_varient($product_variations, $c['variations']);
                        $price = $product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
                    }
                    $product = Helpers::product_data_formatting(data:$product,multi_data: false,trans: false,local: app()->getLocale(),maxDiscount:false);
                    $addon_data = Helpers::calculate_addon_price(AddOn::whereIn('id', $c['add_ons'])->get(), $c['add_on_qtys']);
                    $product_discount = Helpers::food_discount_calculate($product, $price, $restaurant, false);

                    $or_d = [
                        'food_id' => $isCampaign ? null : $c['id'],
                        'item_campaign_id' => $isCampaign ? $c['id'] : null,
                        'food_details' => json_encode($product),
                        'quantity' => $c['quantity'],
                        'price' => round($price, config('round_up_to_digit')),

                        'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                        'tax_amount' => 0,
                        'tax_status' => null,

                        'discount_on_product_by' => $product_discount['discount_type'],
                        'discount_type' => $product_discount['discount_type'],
                        'discount_on_food' => $product_discount['discount_amount'],
                        'discount_percentage' => $product_discount['discount_percentage'],

                        'variant' => json_encode($c['variant']),
                        'variation' => json_encode($variations),
                        'add_ons' => json_encode($addon_data['addons']),

                        'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                        'addon_discount' => 0,

                        'created_at' => now(),
                        'updated_at' => now()
                    ];


                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $price * $or_d['quantity'];
                    $restaurant_discount_amount += $or_d['discount_on_food'] * $or_d['quantity'] ?? 0;
                    $order_details[] = $or_d;
                    $addon_data[] = $addon_data['addons'];
                } else {
                    return [
                        'status_code' => 403,
                        'code' => 'not_found',
                        'message' => translate('messages.product_not_found'),
                    ];
                }
            }
        }





        $discount_on_product_by = 'vendor';
        $discount = $restaurant_discount_amount;
        $restaurantDiscount = Helpers::get_restaurant_discount($restaurant);
        if (isset($restaurantDiscount)) {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase']);
            $discount= $admin_discount;
            $discount_on_product_by = 'admin';
            foreach ($order_details as $key => $detail_data) {
                if($admin_discount>0){
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = $restaurantDiscount['discount'];
                    $order_details[$key]['discount_on_food'] =  Helpers::checkAdminDiscount(price: $product_price , discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);
                } else {
                    $order_details[$key]['discount_on_product_by'] = null;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = 0;
                    $order_details[$key]['discount_on_food'] =  0;
                }
            }
        }





        $orderDetails = collect($order_details ?? []);

        $originalCart = session()->get('cart', []);
        $finalCart = [];

        foreach ($originalCart as $key => $item) {
            if (!is_numeric($key)) {
                $finalCart[$key] = $item;
                continue;
            }

            if (!is_array($item)) {
                $finalCart[$key] = $item;
                continue;
            }

            $match = $orderDetails->first(function ($detail) use ($item) {
                return ($detail['food_id'] ?? null) === ($item['id'] ?? null) ||
                    ($detail['item_campaign_id'] ?? null) === ($item['id'] ?? null);
            });

            if ($match) {
                $item['discount'] = ($match['discount_on_product_by'] ?? '') === 'admin'
                    ? $match['discount_on_food']
                    : ($match['discount_on_food'] ?? 0) * ($item['quantity'] ?? 1);
            }

            $finalCart[$key] = $item;
        }

        session()->put('cart', collect($finalCart));

        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'restaurant_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by,
            'product_data' => $product_data
        ];
    }
    private function makeEditOrderDetails($carts, $restaurant)
    {
        $total_addon_price = 0;
        $product_price = 0;
        $restaurant_discount_amount = 0;
        $product_data = [];
        $order_details = [];
        $variations = [];
        $discount_on_product_by = 'vendor';

        foreach ($carts as $c) {
            if (!isset($c['status']) || $c['status'] !== false) {
                $product = Food::withoutGlobalScope(RestaurantScope::class)->find($c['food_id']);
                if ($product) {
                    if ($product->restaurant_id != $restaurant->id) {
                        return [
                            'status_code' => 403,
                            'code' => 'different_restaurants',
                            'message' => translate('messages.Please_select_items_from_the_same_restaurant'),
                        ];
                    }

                    if ($product?->maximum_cart_quantity && $c['quantity'] > $product?->maximum_cart_quantity) {
                        return [
                            'status_code' => 403,
                            'code' => 'quantity',
                            'message' => translate('messages.maximum_cart_quantity_limit_over'),
                        ];
                    }

                    $product_variations = json_decode($product->variations, true);

                    if (count($product_variations)) {
                        $variation_data = Helpers::get_edit_varient($product_variations, json_decode($c['variation'], true));
                        $price = $product['price'] + $variation_data['price'];
                        $variations = $variation_data['variations'];
                    } else {
                        $price = $product['price'];
                    }

                    $product = Helpers::product_data_formatting(data:$product,multi_data: false,trans: false, local:app()->getLocale(),maxDiscount:false);
                    $input = $c['add_ons'] ?? null;
                    $addonIds = [];
                    $addonQuantities = [];

                    if (is_string($input)) {
                        $decoded = json_decode($input, true);

                        if (is_array($decoded)) {
                            if (is_numeric(data_get($decoded,0))) {

                                $addonIds = $decoded;
                                $addonQuantities = $c['add_on_qtys'] ?? [];
                            } else {

                                $addonIds = array_column($decoded, 'id');
                                $addonQuantities = array_column($decoded, 'quantity');
                            }
                        }
                    } elseif (is_array($input)) {
                        if (is_numeric(data_get($input,0))) {

                            $addonIds = $input;
                            $addonQuantities = $c['add_on_qtys'] ?? [];
                        } else {

                            $addonIds = array_column($input, 'id');
                            $addonQuantities = array_column($input, 'quantity');
                        }
                    }

                    $addonIds = array_unique($addonIds);
                    $addon_data = Helpers::calculate_addon_price(
                        AddOn::whereIn('id', $addonIds)->get(),
                        $addonQuantities
                    );

                    $product_discount = Helpers::food_discount_calculate($product, $price, $restaurant, false);

                    $or_d = [
                        'cart_id' => $c['id'],
                        'food_id' => $c['food_id'],
                        'food_details' => json_encode($product),
                        'quantity' => $c['quantity'],
                        'price' => round($price, config('round_up_to_digit')),

                        'category_id' => collect(is_string($product->category_ids) ? json_decode($product->category_ids, true) : $product->category_ids)->firstWhere('position', 1)['id'] ?? null,
                        'tax_amount' => 0,
                        'tax_status' => null,

                        'discount_on_product_by' => 'vendor',
                        'discount_type' => $product_discount['discount_type'],
                        'discount_on_food' => $product_discount['discount_amount'],
                        'discount_percentage' => $product_discount['discount_percentage'],

                        'variant' => json_encode($c['variant']),
                        'variation' => json_encode($variations),
                        'add_ons' => json_encode($addon_data['addons']),

                        'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                        'addon_discount' => 0,

                        'created_at' => now(),
                        'updated_at' => now()
                    ];


                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $price * $or_d['quantity'];
                    $restaurant_discount_amount += $or_d['discount_on_food'] * $or_d['quantity'] ?? 0;
                    $order_details[] = $or_d;
                    $addon_data[] = $addon_data['addons'];
                } else {
                    return [
                        'status_code' => 403,
                        'code' => 'not_found',
                        'message' => translate('messages.product_not_found'),
                    ];
                }
            }
        }

        $discount = $restaurant_discount_amount;
        $restaurantDiscount = Helpers::get_restaurant_discount($restaurant);
        if (isset($restaurantDiscount)) {
            $admin_discount = Helpers::checkAdminDiscount(price: $product_price, discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase']);
            $discount= $admin_discount;
            $discount_on_product_by = 'admin';
            foreach ($order_details as $key => $detail_data) {
                if($admin_discount>0){
                    $order_details[$key]['discount_on_product_by'] = $discount_on_product_by;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = $restaurantDiscount['discount'];
                    $order_details[$key]['discount_on_food'] =  Helpers::checkAdminDiscount(price: $product_price , discount: $restaurantDiscount['discount'], max_discount: $restaurantDiscount['max_discount'], min_purchase: $restaurantDiscount['min_purchase'], item_wise_price: $detail_data['price'] * $detail_data['quantity']);
                } else {
                    $order_details[$key]['discount_on_product_by'] = null;
                    $order_details[$key]['discount_type'] = 'percentage';
                    $order_details[$key]['discount_percentage'] = 0;
                    $order_details[$key]['discount_on_food'] =  0;
                }
            }
        }

        $orderDetails = collect($order_details ?? []);
        $updatedCart = collect($carts)->map(function ($item) use ($orderDetails) {
            $match = $orderDetails->where('food_id',$item['food_id'])->where('cart_id',$item['id'])->first();
            if ($match) {
                $item['discount_on_food'] =  $match['discount_on_food'];
                $item['discount_type'] =  $match['discount_on_product_by'];
                $item['discount_percentage'] = $match['discount_on_food'] > 0 ? $match['discount_percentage']:0;
                $item['discount_on_product_by'] =  $match['discount_on_product_by'];
            }
            return $item;
        });
        request()->session()->put('order_cart', $updatedCart);

        return [
            'order_details' => $order_details,
            'total_addon_price' => $total_addon_price,
            'product_price' => $product_price,
            'restaurant_discount_amount' => $discount,
            'discount_on_product_by' => $discount_on_product_by,
            'product_data' => $product_data

        ];
    }

    public function getCalculatedTax($request)
    {
        $product_price = $request->order_amount ?? 0;
        $coupon = null;
        $ref_bonus_amount = 0;
        $total_addon_price = 0;
        $restaurant_discount_amount = 0;
        $coupon_discount_amount = 0;
        $order_details = [];


        $order = new Order();
        $order->user_id = $request->user ? $request->user->id : $request['guest_id'];
        $order->is_guest = $request->user ? 0 : 1;
        $order->restaurant_id = $request['restaurant_id'];



        $additionalCharges = [];
        $settings = BusinessSetting::whereIn('key', [
            'additional_charge_status',
            'additional_charge',
            'extra_packaging_charge',
        ])->pluck('value', 'key');


        $extra_packaging_data  = $settings['extra_packaging_charge'] ?? 0;

        $restaurant = Restaurant::with(['discount'])->where('id', $request->restaurant_id)->first();

        if ($request['coupon_code']) {
            $couponData =  $this->getCouponData($request);
            if (data_get($couponData, 'status_code') === 403) {

                return response()->json([
                    'errors' => [
                        ['code' => data_get($couponData, 'code'), 'message' => data_get($couponData, 'message')]
                    ]
                ], data_get($couponData, 'status_code'));
            } else {
                $coupon = data_get($couponData, 'coupon');
            }
        }
        $extra_packaging_amount =  ($extra_packaging_data == 1 && $restaurant?->restaurant_config?->is_extra_packaging_active == 1  && $request?->extra_packaging_amount > 0)?$restaurant?->restaurant_config?->extra_packaging_amount:0;

        if ($extra_packaging_amount > 0) {
            $additionalCharges['tax_on_packaging_charge'] =  $extra_packaging_amount;
        }

        $carts = Cart::where('user_id', $order->user_id)->where('is_guest', $order->is_guest)
            ->when(isset($request->is_buy_now) && $request->is_buy_now == 1 && $request->cart_id, function ($query) use ($request) {
                return $query->where('id', $request->cart_id);
            })
            ->get()->map(function ($data) {
                $data->add_on_ids = json_decode($data->add_on_ids, true);
                $data->add_on_qtys = json_decode($data->add_on_qtys, true);
                $data->variation = json_decode($data->variation, true);
                return $data;
            });

        if (isset($request->is_buy_now) && $request->is_buy_now == 1) {
            $carts =gettype($request['cart']) == 'array' ? $request['cart'] : json_decode($request['cart'], true);
        }

        $order_details = $this->makeOrderDetails($carts, $order, $restaurant);
        if (data_get($order_details, 'status_code') === 403) {

            return response()->json([
                'errors' => [
                    ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                ]
            ], data_get($order_details, 'status_code'));
        }

        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $restaurant_discount_amount = $order_details['restaurant_discount_amount'];
        $order_details = $order_details['order_details'];

        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $restaurant_discount_amount) : 0;

        $total_price = $product_price + $total_addon_price - $restaurant_discount_amount - $coupon_discount_amount;


        if ($order->is_guest  == 0 && $order->user_id) {
            $user = User::withcount('orders')->find($order->user_id);
            $discount_data = Helpers::getCusromerFirstOrderDiscount(order_count: $user->orders_count, user_creation_date: $user->created_at,  refby: $user->ref_by, price: $total_price);
            if (data_get($discount_data, 'is_valid') == true &&  data_get($discount_data, 'calculated_amount') > 0) {
                $total_price = $total_price - data_get($discount_data, 'calculated_amount');
                $ref_bonus_amount = data_get($discount_data, 'calculated_amount');
            }
        }

        $totalDiscount = $restaurant_discount_amount  + $coupon_discount_amount +  $ref_bonus_amount;
        $finalCalculatedTax =  Helpers::getFinalCalculatedTax($order_details, $additionalCharges, $totalDiscount, $total_price, $order->restaurant_id, false);

        $data = [
            'tax_amount' => $finalCalculatedTax['tax_amount'],
            'tax_status' => $finalCalculatedTax['tax_status'],
            'tax_included' => $finalCalculatedTax['tax_included'],
        ];

        return response()->json($data, 200);
    }
    public function setPosCalculatedTax($restaurant, $restaurantData = false)
    {

        $sessionCart = session()->get('cart');
        $carts = collect($sessionCart)->filter(fn($item, $key) => is_numeric($key))->values()->all();

        $order_details = $this->makePosOrderDetails($carts, $restaurant);
        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $restaurant_discount_amount = $order_details['restaurant_discount_amount'];
        $order_details = $order_details['order_details'];

        $totalDiscount = $restaurant_discount_amount;
        $price = $product_price + $total_addon_price - $totalDiscount ?? 0;

        $finalCalculatedTax =  Helpers::getFinalCalculatedTax(
            $order_details,
            [],
            $totalDiscount,
            $price,
            $restaurant->id,
            $restaurantData
        );

        session()->put('tax_amount', $finalCalculatedTax['tax_amount']);
        session()->put('tax_included', $finalCalculatedTax['tax_included']);

        $data = [
            'tax_amount' => $finalCalculatedTax['tax_amount'],
            'tax_status' => $finalCalculatedTax['tax_status'],
            'tax_included' => $finalCalculatedTax['tax_included'],
        ];
        return $data;
    }
    public function setOrderEditCalculatedTax($restaurant, $restaurantData = false, $order_id = null)
    {
        if ($order_id) {
            $order = Order::find($order_id);
            if ($order->extra_packaging_amount > 0) {
                $additionalCharges['tax_on_packaging_charge'] =  $order->extra_packaging_amount;
            }
        }

        $coupon = null;

        $carts = session()->get('order_cart');

        $order_details = $this->makeEditOrderDetails($carts, $restaurant);
        if (data_get($order_details, 'status_code') === 403) {

            return response()->json([
                'errors' => [
                    ['code' => data_get($order_details, 'code'), 'message' => data_get($order_details, 'message')]
                ]
            ], data_get($order_details, 'status_code'));
        }

        $total_addon_price = $order_details['total_addon_price'];
        $product_price = $order_details['product_price'];
        $restaurant_discount_amount = $order_details['restaurant_discount_amount'];
        $discount_on_product_by = $order_details['discount_on_product_by'];
        $order_details = $order_details['order_details'];

        if ($order?->coupon_code) {
            $coupon = Coupon::where(['code' => $order->coupon_code])->first();
        }

        $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $restaurant_discount_amount) : 0;
        $totalDiscount = $restaurant_discount_amount + $coupon_discount_amount;
        $price = $product_price + $total_addon_price - $totalDiscount ?? 0;

        $finalCalculatedTax =  Helpers::getFinalCalculatedTax(
            $order_details,
            $additionalCharges??[],
            $totalDiscount,
            $price,
            $restaurant->id,
            $restaurantData
        );

        session()->put('edit_tax_amount', $finalCalculatedTax['tax_amount']);
        session()->put('edit_tax_included', $finalCalculatedTax['tax_included']);

        $data = [
            'tax_amount' => $finalCalculatedTax['tax_amount'],
            'tax_status' => $finalCalculatedTax['tax_status'],
            'tax_included' => $finalCalculatedTax['tax_included'],
            'restaurant_discount_amount' => $restaurant_discount_amount,
            'discount_on_product_by'=> $discount_on_product_by,
        ];

        return response()->json($data, 200);
    }



    private function getCouponData($request)
    {

        if ($request['coupon_code']) {
            $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();

            if (!$coupon) {
                return [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_expire'),
                ];
            }

            $status = $request->is_guest
                ? CouponLogic::is_valid_for_guest($coupon, $request['restaurant_id'])
                : CouponLogic::is_valide($coupon, $request->user->id, $request['restaurant_id']);

            $validationError = match ($status) {
                407 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_expire'),
                ],
                408 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.You_are_not_eligible_for_this_coupon'),
                ],
                406 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.coupon_usage_limit_over'),
                ],
                404 => [
                    'status_code' => 403,
                    'code' => 'coupon',
                    'message' => translate('messages.not_found'),
                ],
                default => null,
            };

            if ($validationError) {
                return $validationError;
            }

            $coupon_created_by = $coupon->created_by;

            if ($coupon->coupon_type === 'free_delivery') {
                $delivery_charge = 0;
                $free_delivery_by = $coupon_created_by;
                $coupon_created_by = null;
            }
        }

        return [
            'coupon' => $coupon ?? null,
            'coupon_created_by' => $coupon_created_by ?? null,
            'delivery_charge' => $delivery_charge ?? null,
            'free_delivery_by' => $free_delivery_by ?? null,
        ];
    }
}
