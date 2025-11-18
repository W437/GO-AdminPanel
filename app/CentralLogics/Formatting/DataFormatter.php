<?php

namespace App\CentralLogics\Formatting;

use App\CentralLogics\Helpers;
use App\CentralLogics\RestaurantLogic;
use App\Models\AddOn;
use App\Models\Allergy;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Food;
use App\Models\ItemCampaign;
use App\Models\Nutrition;
use App\Models\Zone;
use App\Models\Coupon;
use MatanYadaev\EloquentSpatial\Objects\Point;

class DataFormatter
{
    public static function cart_product_data_formatting($data, $selected_variation, $selected_addons, $selected_addon_quantity,$trans = false, $local = 'en')
    {

        $variations = [];
        $categories = [];
        $category_ids = gettype($data['category_ids']) == 'array' ? $data['category_ids'] : json_decode($data['category_ids'],true);
        foreach ($category_ids as $value) {
            $category_name = Category::where('id',$value['id'])->pluck('name');
            $categories[] = ['id' => (string)$value['id'], 'position' => $value['position'], 'name'=>data_get($category_name,'0','NA')];
        }
        $data['category_ids'] = $categories;

        $add_ons = gettype($data['add_ons']) == 'array' ? $data['add_ons'] : json_decode($data['add_ons'],true);
        $data_addons = self::addon_data_formatting(AddOn::whereIn('id', $add_ons)->active()->get(), true, $trans, $local);
        $selected_data = array_combine($selected_addons, $selected_addon_quantity);
        foreach ($data_addons as $addon) {
            $addon_id = $addon['id'];
            if (in_array($addon_id, $selected_addons)) {
                $addon['isChecked'] = true;
                $addon['quantity'] = $selected_data[$addon_id];
            } else {
                $addon['isChecked'] = false;
                $addon['quantity'] = 0;
            }
        }
        $data['addons'] = $data_addons;

        if ($data->title) {
            $data['name'] = $data->title;
            unset($data['title']);
        }
        if ($data->start_time) {
            $data['available_time_starts'] = $data->start_time->format('H:i');
            unset($data['start_time']);
        }
        if ($data->end_time) {
            $data['available_time_ends'] = $data->end_time->format('H:i');
            unset($data['end_time']);
        }
        if ($data->start_date) {
            $data['available_date_starts'] = $data->start_date->format('Y-m-d');
            unset($data['start_date']);
        }
        if ($data->end_date) {
            $data['available_date_ends'] = $data->end_date->format('Y-m-d');
            unset($data['end_date']);
        }
        $data_variation = $data['variations']?(gettype($data['variations']) == 'array' ? $data['variations'] : json_decode($data['variations'],true)):[];
        foreach ($selected_variation as $item1) {
            foreach ($data_variation as &$item2) {
                if ($item1["name"] === $item2["name"]) {
                    foreach ($item2["values"] as &$value) {
                        if (in_array($value["label"], $item1["values"]["label"])) {
                            $value["isSelected"] = true;
                        }else{
                            $value["isSelected"] = false;
                        }
                    }
                }
            }
        }
        $discount_data= Helpers::product_discount_calculate_data($data, $data->restaurant);

        $data['discount'] = $discount_data['discount_percentage'];
        $data['discount_type'] = $discount_data['original_discount_type'];

        $data['variations'] = $data_variation;
        $data['restaurant_name'] = $data->restaurant->name;
        $data['restaurant_status'] = (int) $data->restaurant->status;
        $data['restaurant_discount'] = Helpers::get_restaurant_discount($data->restaurant) ? $data->restaurant->discount->discount : 0;
        $data['restaurant_opening_time'] = $data->restaurant->opening_time ? $data->restaurant->opening_time->format('H:i') : null;
        $data['restaurant_closing_time'] = $data->restaurant->closeing_time ? $data->restaurant->closeing_time->format('H:i') : null;
        $data['schedule_order'] = $data->restaurant->schedule_order;
        $data['rating_count'] = (int)($data->rating ? array_sum(json_decode($data->rating, true)) : 0);
        $data['avg_rating'] = (float)($data->avg_rating ? $data->avg_rating : 0);
        $data['recommended'] =(int) $data->recommended;

        $data['halal_tag_status'] =  (int) $data->restaurant->restaurant_config?->halal_tag_status??0;
        $data['nutritions_name']= $data?->nutritions ? Nutrition::whereIn('id',$data?->nutritions->pluck('id') )->pluck('nutrition') : null;
        $data['allergies_name']= $data?->allergies ?Allergy::whereIn('id',$data?->allergies->pluck('id') )->pluck('allergy') : null;
        $data['free_delivery'] =  (int) $data->restaurant->free_delivery ?? 0;
        $data['min_delivery_time'] =  (int) explode('-',$data->restaurant->delivery_time)[0] ?? 0;
        $data['max_delivery_time'] =  (int) explode('-',$data->restaurant->delivery_time)[1] ?? 0;
        $cuisine =[];
        $cui =$data->restaurant->load('cuisine');
        if(isset($cui->cuisine)){
            foreach($cui->cuisine as $cu){
                $cuisine[]= ['id' => (int) $cu->id, 'name' => $cu->name , 'image' => $cu->image];
            }
        }

        $data['cuisines'] =   $cuisine;

        unset($data['restaurant']);
        unset($data['rating']);


        return $data;
    }

    public static function product_data_formatting($data, $multi_data = false, $trans = false, $local = 'en',$maxDiscount=true)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                if ($item->title) {
                    $item['name'] = $item->title;
                    unset($item['title']);
                }
                if ($item->start_time) {
                    $item['available_time_starts'] = $item->start_time->format('H:i');
                    unset($item['start_time']);
                }
                if ($item->end_time) {
                    $item['available_time_ends'] = $item->end_time->format('H:i');
                    unset($item['end_time']);
                }

                if ($item->start_date) {
                    $item['available_date_starts'] = $item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if ($item->end_date) {
                    $item['available_date_ends'] = $item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }
                $item['recommended'] =(int) $item->recommended;
                $categories = [];
                foreach (json_decode($item?->category_ids) as $value) {
                    $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
                }
                $item['category_ids'] = $categories;
                if($maxDiscount){
                    $discount_data= Helpers::product_discount_calculate_data($item,  $item->restaurant );
                    $item['discount'] = $discount_data['discount_percentage'];
                    $item['discount_type'] = $discount_data['original_discount_type'];
                }


                $item['add_ons'] = self::addon_data_formatting(AddOn::whereIn('id', json_decode($item['add_ons']))->active()->get(), true, $trans, $local);
                $item['tags'] = $item->tags;
                $item['variations'] = json_decode($item['variations'], true);
                $item['restaurant_name'] = $item->restaurant->name;
                $item['restaurant_status'] = (int) $item->restaurant->status;
                $item['restaurant_discount'] = Helpers::get_restaurant_discount($item->restaurant) ? $item->restaurant->discount->discount : 0;
                $item['restaurant_opening_time'] = $item->restaurant->opening_time ? $item->restaurant->opening_time->format('H:i') : null;
                $item['restaurant_closing_time'] = $item->restaurant->closeing_time ? $item->restaurant->closeing_time->format('H:i') : null;
                $item['schedule_order'] = $item->restaurant->schedule_order;
                $item['tax'] = $item->restaurant->tax;
                try {
                    $reviewsInfo = $item->rating()->first();
                } catch (\Exception $e) {
                    $reviewsInfo = null;
                }
                $item['rating_count'] = $reviewsInfo?->rating_count ?? 0;
                $item['avg_rating'] = $reviewsInfo?->average ?? 0;
                $item['min_delivery_time'] =  (int) explode('-',$item->restaurant->delivery_time)[0] ?? 0;
                $item['max_delivery_time'] =  (int) explode('-',$item->restaurant->delivery_time)[1] ?? 0;


                if( $item->restaurant->restaurant_model == 'subscription'  && isset($item->restaurant->restaurant_sub)){
                    $item->restaurant['self_delivery_system'] = (int) $item->restaurant->restaurant_sub->self_delivery;
                }

                $item['free_delivery'] =  (int) $item->restaurant->free_delivery ?? 0;
                $item['halal_tag_status'] =  (int) $item->restaurant->restaurant_config?->halal_tag_status??0;
                $item['nutritions_name']= $item?->nutritions ? Nutrition::whereIn('id',$item?->nutritions->pluck('id') )->pluck('nutrition') : null;
                $item['allergies_name']= $item?->allergies ?Allergy::whereIn('id',$item?->allergies->pluck('id') )->pluck('allergy') : null;

               if(Helpers::getDeliveryFee($item->restaurant)  ==  'free_delivery'){
                    $item['free_delivery'] =  (int)  1;
               }

                $cuisine =[];
                $cui =$item->restaurant->load('cuisine');
                if(isset($cui->cuisine)){
                    foreach($cui->cuisine as $cu){
                        $cuisine[]= ['id' => (int) $cu->id, 'name' => $cu->name , 'image' => $cu->image];
                    }
                }

                $item['cuisines'] =   $cuisine;

                $item['tax_data'] = $item?->taxVats ?$item?->taxVats()->pluck('tax_id')->toArray(): [] ;

                $item['tax_data']= \Modules\TaxModule\Entities\Tax::whereIn('id', $item['tax_data'])->get(['id', 'name', 'tax_rate']);
                unset($item['taxVats']);


                unset($item['restaurant']);
                unset($item['rating']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $categories = [];
            foreach (json_decode($data?->category_ids) as $value) {
                $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
            }
            $data['category_ids'] = $categories;

            $data['add_ons'] = self::addon_data_formatting(AddOn::whereIn('id', json_decode($data['add_ons']))->active()->get(), true, $trans, $local);
            if ($data->title) {
                $data['name'] = $data->title;
                unset($data['title']);
            }
            if ($data->start_time) {
                $data['available_time_starts'] = $data->start_time->format('H:i');
                unset($data['start_time']);
            }
            if ($data->end_time) {
                $data['available_time_ends'] = $data->end_time->format('H:i');
                unset($data['end_time']);
            }
            if ($data->start_date) {
                $data['available_date_starts'] = $data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if ($data->end_date) {
                $data['available_date_ends'] = $data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
            $data['variations'] = json_decode($data['variations'], true);
            $data['restaurant_name'] = $data->restaurant->name;
            $data['restaurant_status'] = (int) $data->restaurant->status;
            $data['restaurant_discount'] = Helpers::get_restaurant_discount($data->restaurant) ? $data->restaurant->discount->discount : 0;
            $data['restaurant_opening_time'] = $data->restaurant->opening_time ? $data->restaurant->opening_time->format('H:i') : null;
            $data['restaurant_closing_time'] = $data->restaurant->closeing_time ? $data->restaurant->closeing_time->format('H:i') : null;
            $data['schedule_order'] = $data->restaurant->schedule_order;
            if($maxDiscount){
                $discount_data= Helpers::product_discount_calculate_data($data, $data->restaurant);
                $data['discount'] = $discount_data['discount_percentage'];
                $data['discount_type'] = $discount_data['original_discount_type'];
            }
                try {
                    $reviewsInfo = $data->rating()->first();
                } catch (\Exception $e) {
                    $reviewsInfo = null;
                }
                $data['rating_count'] = $reviewsInfo?->rating_count ?? 0;
                $data['avg_rating'] = $reviewsInfo?->average ?? 0;
                $data['recommended'] =(int) $data->recommended;



            if( $data->restaurant->restaurant_model == 'subscription'  && isset($data->restaurant->restaurant_sub)){
                $data->restaurant['self_delivery_system'] = (int) $data->restaurant->restaurant_sub->self_delivery;
            }

            $data['free_delivery'] =  (int) $data->restaurant->free_delivery ?? 0;
            $data['halal_tag_status'] =  (int) $data->restaurant->restaurant_config?->halal_tag_status??0;
            $data['nutritions_name']= $data?->nutritions ? Nutrition::whereIn('id',$data?->nutritions->pluck('id') )->pluck('nutrition') : null;
            $data['allergies_name']= $data?->allergies ?Allergy::whereIn('id',$data?->allergies->pluck('id') )->pluck('allergy') : null;

            if(Helpers::getDeliveryFee($data->restaurant)  ==  'free_delivery'){
                $data['free_delivery'] =  (int)  1;
            }

            $data['min_delivery_time'] =  (int) explode('-',$data->restaurant->delivery_time)[0] ?? 0;
            $data['max_delivery_time'] =  (int) explode('-',$data->restaurant->delivery_time)[1] ?? 0;
            $cuisine =[];
            $cui =$data->restaurant->load('cuisine');
            if(isset($cui->cuisine)){
                foreach($cui->cuisine as $cu){
                    $cuisine[]= ['id' => (int) $cu->id, 'name' => $cu->name , 'image' => $cu->image];
                }
            }

            $data['cuisines'] =   $cuisine;

            $data['tax_data'] = $data?->taxVats ?$data?->taxVats()->pluck('tax_id')->toArray(): [] ;
            $data['tax_data']= \Modules\TaxModule\Entities\Tax::whereIn('id', $data['tax_data'])->get(['id', 'name', 'tax_rate']);
            unset($data['taxVats']);

            unset($data['restaurant']);
            unset($data['rating']);
        }

        return $data;
    }

    public static function product_data_formatting_translate($data, $multi_data = false, $trans = false, $local = 'en')
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                if ($item->title) {
                    $item['name'] = $item->title;
                    unset($item['title']);
                }
                if ($item->start_time) {
                    $item['available_time_starts'] = $item->start_time->format('H:i');
                    unset($item['start_time']);
                }
                if ($item->end_time) {
                    $item['available_time_ends'] = $item->end_time->format('H:i');
                    unset($item['end_time']);
                }
                if ($item->start_date) {
                    $item['available_date_starts'] = $item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if ($item->end_date) {
                    $item['available_date_ends'] = $item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }
                $item['recommended'] =(int) $item->recommended;
                $categories = [];
                foreach (json_decode($item['category_ids']) as $value) {
                    $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
                }
                $item['category_ids'] = $categories;
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);
                $item['add_ons'] = self::addon_data_formatting(AddOn::whereIn('id', json_decode($item['add_ons'], true))->active()->get(), true, $trans, $local);

                $item['variations'] = json_decode($item['variations'], true);
                $item['restaurant_name'] = $item->restaurant->name;
                $item['zone_id'] = $item->restaurant->zone_id;
                $item['restaurant_discount'] = Helpers::get_restaurant_discount($item->restaurant) ? $item->restaurant->discount->discount : 0;
                $item['schedule_order'] = $item->restaurant->schedule_order;
                $item['tax'] = $item->restaurant->tax;
                try {
                    $reviewsInfo = $item->rating()->first();
                } catch (\Exception $e) {
                    $reviewsInfo = null;
                }
                $item['rating_count'] = $reviewsInfo?->rating_count ?? 0;
                $item['avg_rating'] = $reviewsInfo?->average ?? 0;
                $item['recommended'] =(int) $item->recommended;
                $item['nutritions_name']= $item?->nutritions ? Nutrition::whereIn('id',$item?->nutritions->pluck('id') )->pluck('nutrition') : null;
                $item['allergies_name']= $item?->allergies ?Allergy::whereIn('id',$item?->allergies->pluck('id') )->pluck('allergy') : null;

                if ($trans) {
                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\Food',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'name',
                        'value' => $item->name
                    ];

                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\Food',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'description',
                        'value' => $item->description
                    ];
                }

                if (count($item['translations']) > 0) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation['locale'] == $local) {
                            if ($translation['key'] == 'name') {
                                $item['name'] = $translation['value'];
                            }

                            if ($translation['key'] == 'title') {
                                $item['name'] = $translation['value'];
                            }

                            if ($translation['key'] == 'description') {
                                $item['description'] = $translation['value'];
                            }
                        }
                    }
                }
                if (!$trans) {
                    unset($item['translations']);
                }

                $item['tax_ids']= $item?->taxVats ?$item?->taxVats()->pluck('tax_id')->toArray(): [] ;

                unset($item['taxVats']);

                unset($item['restaurant']);
                unset($item['rating']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $categories = [];
            foreach (json_decode($data['category_ids']) as $value) {
                $categories[] = ['id' => (string)$value->id, 'position' => $value->position];
            }
            $data['category_ids'] = $categories;

            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
            $data['add_ons'] = self::addon_data_formatting(AddOn::whereIn('id', json_decode($data['add_ons']))->active()->get(), true, $trans, $local);

            if ($data->title) {
                $data['name'] = $data->title;
                unset($data['title']);
            }
            if ($data->start_time) {
                $data['available_time_starts'] = $data->start_time->format('H:i');
                unset($data['start_time']);
            }
            if ($data->end_time) {
                $data['available_time_ends'] = $data->end_time->format('H:i');
                unset($data['end_time']);
            }
            if ($data->start_date) {
                $data['available_date_starts'] = $data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if ($data->end_date) {
                $data['available_date_ends'] = $data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
            $data['variations'] = json_decode($data['variations'], true);
            $data['restaurant_name'] = $data->restaurant->name;
            $data['zone_id'] = $data->restaurant->zone_id;
            $data['restaurant_discount'] = Helpers::get_restaurant_discount($data->restaurant) ? $data->restaurant->discount->discount : 0;
            $data['schedule_order'] = $data->restaurant->schedule_order;
            $data['nutritions_name']= $data?->nutritions ? Nutrition::whereIn('id',$data?->nutritions->pluck('id') )->pluck('nutrition') : null;
            $data['allergies_name']= $data?->allergies ?Allergy::whereIn('id',$data?->allergies->pluck('id') )->pluck('allergy') : null;
                try {
                    $reviewsInfo = $data->rating()->first();
                } catch (\Exception $e) {
                    $reviewsInfo = null;
                }
                $data['rating_count'] = $reviewsInfo?->rating_count ?? 0;
                $data['avg_rating'] = $reviewsInfo?->average ?? 0;

            if ($trans) {
                $data['translations'][] = [
                    'translationable_type' => 'App\Models\Foos',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'name',
                    'value' => $data->name
                ];

                $data['translations'][] = [
                    'translationable_type' => 'App\Models\Food',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'description',
                    'value' => $data->description
                ];
            }

            if (count($data['translations']) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation['locale'] == $local) {
                        if ($translation['key'] == 'name') {
                            $data['name'] = $translation['value'];
                        }

                        if ($translation['key'] == 'title') {
                            $item['name'] = $translation['value'];
                        }

                        if ($translation['key'] == 'description') {
                            $data['description'] = $translation['value'];
                        }
                    }
                }
            }
            if (!$trans) {
                unset($data['translations']);
            }

            $data['tax_ids']= $data?->taxVats ?$data?->taxVats()->pluck('tax_id')->toArray(): [] ;

            unset($data['taxVats']);

            unset($data['restaurant']);
            unset($data['rating']);
        }

        return $data;
    }
    public static function addon_data_formatting($data, $multi_data = false, $trans = false, $local = 'en')
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $item['tax_ids']= $item?->taxVats ?$item?->taxVats()->pluck('tax_id')->toArray(): [] ;
                unset($item['taxVats']);
                if ($trans) {
                    $item['translations'][] = [
                        'translationable_type' => 'App\Models\AddOn',
                        'translationable_id' => $item->id,
                        'locale' => 'en',
                        'key' => 'name',
                        'value' => $item->name
                    ];
                }
                // if (count($item->translations) > 0) {
                //     foreach ($item['translations'] as $translation) {
                //         if ($translation['locale'] == $local && $translation['key'] == 'name') {
                //             $item['name'] = $translation['value'];
                //         }
                //     }
                // }

                // if (!$trans) {
                //     unset($item['translations']);
                // }

                $storage[] = $item;
            }
            $data = $storage;
        } else if (isset($data)) {
            $item['tax_ids']= $data?->taxVats ?$data?->taxVats()->pluck('tax_id')->toArray(): [] ;
            unset($item['taxVats']);
            if ($trans) {
                $data['translations'][] = [
                    'translationable_type' => 'App\Models\AddOn',
                    'translationable_id' => $data->id,
                    'locale' => 'en',
                    'key' => 'name',
                    'value' => $data->name
                ];
            }

            // if (count($data->translations) > 0) {
            //     foreach ($data['translations'] as $translation) {
            //         if ($translation['locale'] == $local && $translation['key'] == 'name') {
            //             $data['name'] = $translation['value'];
            //         }
            //     }
            // }

            // if (!$trans) {
            //     unset($data['translations']);
            // }
        }
        return $data;
    }

    public static function category_data_formatting($data, $multi_data = false, $trans = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                // if (count($item->translations) > 0) {
                //     $item->name = $item->translations[0]['value'];
                // }

                // if (!$trans) {
                //     unset($item['translations']);
                // }

                if($item->relationLoaded('childes') && $item['childes']){
                    $item['products_count'] += $item['childes']->sum('products_count');
                    // unset($item['childes']);
                }
                $storage[] = $item;
            }
            $data = $storage;
        } else if (isset($data)) {
            // if (count($data->translations) > 0) {
            //     $data->name = $data->translations[0]['value'];
            // }

            // if (!$trans) {
            //     unset($data['translations']);
            // }
            if($data->relationLoaded('childes') && $data['childes']){
                $data['products_count'] += $data['childes']->sum('products_count');
                // unset($data['childes']);
            }
        }
        return $data;
    }

    public static function basic_campaign_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];

                if ($item->start_date) {
                    $item['available_date_starts'] = $item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if ($item->end_date) {
                    $item['available_date_ends'] = $item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }

                // if (count($item['translations']) > 0) {
                //     $translate = array_column($item['translations']->toArray(), 'value', 'key');
                //     $item['title'] = $translate['title'];
                //     $item['description'] = $translate['description'];
                // }
                if (count($item['restaurants']) > 0) {
                    $item['restaurants'] = self::restaurant_data_formatting($item['restaurants'], true);
                }

                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if ($data->start_date) {
                $data['available_date_starts'] = $data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if ($data->end_date) {
                $data['available_date_ends'] = $data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }

            // if (count($data['translations']) > 0) {
            //     $translate = array_column($data['translations']->toArray(), 'value', 'key');
            //     $data['title'] = $translate['title'];
            //     $data['description'] = $translate['description'];
            // }
            if (count($data['restaurants']) > 0) {
                $data['restaurants'] = self::restaurant_data_formatting($data['restaurants'], true);
            }
        }

        return $data;
    }
    public static function restaurant_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        $cuisines=[];
        $extra_packaging_data = \App\Models\BusinessSetting::where('key', 'extra_packaging_charge')->first()?->value ?? 0;

        if ($multi_data == true) {
            foreach ($data as $item) {
                $item['foods']  =  $item->foods()->active()->take(5)->get(['id','image' ,'name']);
                $item->load('cuisine');
                // $item['coupons'] = $item->coupon_valid;
                $restaurant_id= (string)$item->id;

                $item['coupons'] = Coupon::Where(function ($q) use ($restaurant_id) {
                    $q->Where('coupon_type', 'restaurant_wise')->whereJsonContains('data', [$restaurant_id])
                        ->where(function ($q1)  {
                            $q1->WhereJsonContains('customer_id', ['all']);
                        });
                })->orwhere('restaurant_id',$restaurant_id)
                ->active()
                ->valid()
                ->take(10)
                ->get();

                if( $item->restaurant_model == 'subscription'  && isset($item->restaurant_sub)){
                    $item['self_delivery_system'] = (int) $item->restaurant_sub->self_delivery;
                }

                $item['delivery_fee'] = Helpers::getDeliveryFee($item);

                $item['restaurant_status'] = (int) $item->status;
                $item['cuisine'] = $item->cuisine;

                if ($item->opening_time) {
                    $item['available_time_starts'] = $item->opening_time->format('H:i');
                    unset($item['opening_time']);
                }
                if ($item->closeing_time) {
                    $item['available_time_ends'] = $item->closeing_time->format('H:i');
                    unset($item['closeing_time']);
                }

                $reviewsInfo = $item->reviews()
                ->selectRaw('avg(reviews.rating) as average_rating, count(reviews.id) as total_reviews, food.restaurant_id')
                ->groupBy('food.restaurant_id')
                ->first();

                $item['ratings'] = $item?->ratings ?? [];
                $item['avg_rating'] = (float)  $reviewsInfo?->average_rating ?? 0;
                $item['rating_count'] = (int)   $reviewsInfo?->total_reviews ?? 0;

                $positive_rating = RestaurantLogic::calculate_positive_rating($item['rating']);

                $item['positive_rating'] = (int) $positive_rating['rating'];


                $item['customer_order_date'] =   (int) $item?->restaurant_config?->customer_order_date;
                $item['customer_date_order_sratus'] =   (bool) $item?->restaurant_config?->customer_date_order_sratus;
                $item['instant_order'] =   (bool) $item?->restaurant_config?->instant_order;
                $item['halal_tag_status'] =   (bool) $item?->restaurant_config?->halal_tag_status;
                $item['current_opening_time'] = Helpers::getNextOpeningTime($item['schedules']) ?? 'closed';

                $item['is_extra_packaging_active'] =   (bool) ($extra_packaging_data == 1 ? $item?->restaurant_config?->is_extra_packaging_active:false);
                $item['extra_packaging_status'] =   (bool) ($item['is_extra_packaging_active']  == 1   ? $item?->restaurant_config?->extra_packaging_status:false);
                $item['extra_packaging_amount'] =   (float)( $item['is_extra_packaging_active']  == 1 ? $item?->restaurant_config?->extra_packaging_amount:0);

                $item['is_dine_in_active'] =   (bool) $item?->restaurant_config?->dine_in;
                $item['schedule_advance_dine_in_booking_duration'] =   (int)  $item?->restaurant_config?->schedule_advance_dine_in_booking_duration;
                $item['schedule_advance_dine_in_booking_duration_time_format'] =   $item?->restaurant_config?->schedule_advance_dine_in_booking_duration_time_format ?? 'min';

                // Include description fields (will use translated values based on app locale)
                $item['description'] = $item->description;
                $item['short_description'] = $item->short_description;

                $item['characteristics'] = $item->characteristics()->pluck('characteristic')->toArray();
                // $item['tags'] = $item->tags()->pluck('tag')->toArray();

                // unset($item['coupon_valid']);
                unset($item['campaigns']);
                unset($item['pivot']);
                unset($item['rating']);
                unset($item['restaurant_config']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if( $data->restaurant_model == 'subscription'  && isset($data->restaurant_sub)){
                $data['self_delivery_system'] = (int) $data->restaurant_sub->self_delivery;
            }
            $data['restaurant_status'] = (int) $data->status;
            if ($data->opening_time) {
                $data['available_time_starts'] = $data->opening_time->format('H:i');
                unset($data['opening_time']);
            }
            if ($data->closeing_time) {
                $data['available_time_ends'] = $data->closeing_time->format('H:i');
                unset($data['closeing_time']);
            }

            $data['foods']  =  $data->foods()->active()->take(5)->get(['id','image' ,'name']);
            $restaurant_id= (string)$data->id;
            $data['coupons'] = Coupon::Where(function ($q) use ($restaurant_id) {
                $q->Where('coupon_type', 'restaurant_wise')->whereJsonContains('data', [$restaurant_id])
                    ->where(function ($q1)  {
                        $q1->WhereJsonContains('customer_id', ['all']);
                    });
            })->orwhere('restaurant_id',$restaurant_id)
            ->active()
            ->valid()
            ->take(10)
            ->get();

            $data->load(['cuisine']);
            $data['cuisine'] = $data->cuisine;

            $reviewsInfo = $data->reviews()
            ->selectRaw('avg(reviews.rating) as average_rating, count(reviews.id) as total_reviews, food.restaurant_id')
            ->groupBy('food.restaurant_id')
            ->first();
            $data['ratings'] = $data?->rating ?? [];
            $data['avg_rating'] = (float)  $reviewsInfo?->average_rating ?? 0;
            $data['rating_count'] = (int)   $reviewsInfo?->total_reviews ?? 0;

            $positive_rating = RestaurantLogic::calculate_positive_rating($data['rating']);
            $data['positive_rating'] = (int) $positive_rating['rating'];

            $data['customer_order_date'] =   (int) $data?->restaurant_config?->customer_order_date;
            $data['customer_date_order_sratus'] =   (bool) $data?->restaurant_config?->customer_date_order_sratus;
            $data['instant_order'] =   (bool) $data?->restaurant_config?->instant_order;
            $data['halal_tag_status'] =   (bool) $data?->restaurant_config?->halal_tag_status;
            $data['is_extra_packaging_active'] =   (bool) ($extra_packaging_data == 1 ? $data?->restaurant_config?->is_extra_packaging_active:false);
            $data['extra_packaging_status'] =   (bool)  ($data['is_extra_packaging_active'] == 1  ? $data?->restaurant_config?->extra_packaging_status:false);
            $data['extra_packaging_amount'] =   (float)  ($data['is_extra_packaging_active'] == 1 ? $data?->restaurant_config?->extra_packaging_amount:0);
            $data['delivery_fee'] = Helpers::getDeliveryFee($data);
            $data['current_opening_time'] = Helpers::getNextOpeningTime($data['schedules']) ?? 'closed';

            $data['is_dine_in_active'] =   (bool) $data?->restaurant_config?->dine_in;
            $data['schedule_advance_dine_in_booking_duration'] =   (int)  $data?->restaurant_config?->schedule_advance_dine_in_booking_duration;
            $data['schedule_advance_dine_in_booking_duration_time_format'] =   $data?->restaurant_config?->schedule_advance_dine_in_booking_duration_time_format ?? 'min';
            $data['tags'] = $data->tags()->pluck('tag')->toArray();


            $data['characteristics'] = $data->characteristics()->pluck('characteristic')->toArray();
            unset($data['rating']);
            unset($data['campaigns']);
            unset($data['pivot']);
            unset($data['restaurant_config']);
        }

        return $data;
    }

    public static function wishlist_data_formatting($data, $multi_data = false)
    {
        $foods = [];
        $restaurants = [];
        if ($multi_data == true) {

            foreach ($data as $item) {
                if ($item->food) {
                    $foods[] = self::product_data_formatting($item->food, false, false, app()->getLocale());
                }
                if ($item->restaurant) {
                    $restaurants[] = self::restaurant_data_formatting($item->restaurant);
                }
            }
        } else {
            if ($data->food) {
                $foods[] = self::product_data_formatting($data->food, false, false, app()->getLocale());
            }
            if ($data->restaurant) {
                $restaurants[] = self::restaurant_data_formatting($data->restaurant);
            }
        }

        return ['food' => $foods, 'restaurant' => $restaurants];
    }

    public static function order_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                if (isset($item['restaurant'])) {
                    $item['restaurant_name'] = $item['restaurant']['name'];
                    $item['restaurant_address'] = $item['restaurant']['address'];
                    $item['restaurant_phone'] = $item['restaurant']['phone'];
                    $item['restaurant_lat'] = $item['restaurant']['latitude'];
                    $item['restaurant_lng'] = $item['restaurant']['longitude'];
                    $item['restaurant_logo'] = $item['restaurant']['logo'];
                    $item['restaurant_logo_full_url'] = $item['restaurant']['logo_full_url'];
                    $item['restaurant_delivery_time'] = $item['restaurant']['delivery_time'];
                    $item['vendor_id'] = $item['restaurant']['vendor_id'];
                    $item['chat_permission'] = $item['restaurant']['restaurant_sub']['chat'] ?? 0;
                    $item['restaurant_model'] = $item['restaurant']['restaurant_model'];
                    unset($item['restaurant']);
                } else {
                    $item['restaurant_name'] = null;
                    $item['restaurant_address'] = null;
                    $item['restaurant_phone'] = null;
                    $item['restaurant_lat'] = null;
                    $item['restaurant_lng'] = null;
                    $item['restaurant_logo'] = null;
                    $item['restaurant_logo_full_url'] = null;
                    $item['restaurant_delivery_time'] = null;
                    $item['restaurant_model'] = null;
                    $item['chat_permission'] = null;
                }
                $item['food_campaign'] = 0;
                foreach ($item->details as $d) {
                    if ($d->item_campaign_id != null) {
                        $item['food_campaign'] = 1;
                    }
                }

                $item['delivery_address'] = $item->delivery_address ? json_decode($item->delivery_address, true) : null;
                $item['details_count'] = (int)$item->details->count();
                unset($item['details']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if (isset($data['restaurant'])) {
                $data['restaurant_name'] = $data['restaurant']['name'];
                $data['restaurant_address'] = $data['restaurant']['address'];
                $data['restaurant_phone'] = $data['restaurant']['phone'];
                $data['restaurant_lat'] = $data['restaurant']['latitude'];
                $data['restaurant_lng'] = $data['restaurant']['longitude'];
                $data['restaurant_logo'] = $data['restaurant']['logo'];
                $data['restaurant_logo_full_url'] = $data['restaurant']['logo_full_url'];
                $data['restaurant_delivery_time'] = $data['restaurant']['delivery_time'];
                $data['vendor_id'] = $data['restaurant']['vendor_id'];
                $data['chat_permission'] = $data['restaurant']['restaurant_sub']['chat'] ?? 0;
                $data['restaurant_model'] = $data['restaurant']['restaurant_model'];
                unset($data['restaurant']);
            } else {
                $data['restaurant_name'] = null;
                $data['restaurant_address'] = null;
                $data['restaurant_phone'] = null;
                $data['restaurant_lat'] = null;
                $data['restaurant_lng'] = null;
                $data['restaurant_logo'] = null;
                $data['restaurant_logo_full_url'] = null;
                $data['restaurant_delivery_time'] = null;
                $data['chat_permission'] = null;
                $data['restaurant_model'] = null;
            }

            $data['food_campaign'] = 0;
            foreach ($data->details as $d) {
                if ($d->item_campaign_id != null) {
                    $data['food_campaign'] = 1;
                }
            }
            $data['delivery_address'] = $data->delivery_address ? json_decode($data->delivery_address, true) : null;
            $data['details_count'] = (int)$data->details->count();
            unset($data['details']);
        }
        return $data;
    }

    public static function order_details_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $item['add_ons'] = json_decode($item['add_ons']);
            $item['variation'] = json_decode($item['variation']);
            $item['food_details'] = json_decode($item['food_details'], true);
            if ($item['item_id']){
                $product = \App\Models\Food::where(['id' => $item['food_details']['id']])->first();
                $item['image_full_url'] = $product?->image_full_url;
//                $item['images_full_url'] = $product->images_full_url;
            }else{
               $product = \App\Models\ItemCampaign::where(['id' => $item['food_details']['id']])->first();
                $item['image_full_url'] = $product?->image_full_url;
//                $item['images_full_url'] = [];
            }
            array_push($storage, $item);
        }
        $data = $storage;

        return $data;
    }

    public static function deliverymen_list_formatting($data , $restaurant_lat = null , $restaurant_lng = null , $single_data = false )
    {
        $storage = [];
        $map_api_key = BusinessSetting::where(['key' => 'map_api_key_server'])->first()?->value ?? null;

        if($single_data ==  true){
            $item=$data;
                if( $restaurant_lat &&  $restaurant_lng && $item->last_location){
//                    $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $restaurant_lat . ',' . $restaurant_lng . '&destinations=' . ($item->last_location ? $item->last_location->latitude : 0 ). ',' . ($item->last_location ? $item->last_location->longitude : 0) . '&key=' . $map_api_key . '&mode=walking');
//                    $distance=  $response->json();
//                    $distance= gettype($distance) == 'array' ? $distance: json_decode($distance,true);
//                    $distance = data_get($distance,'rows.0.elements.0.distance.text',' ');

                    $originCoordinates =[
                        $restaurant_lat,
                        $restaurant_lng
                    ];
                    $destinationCoordinates =[
                        $item->last_location->latitude,
                        $item->last_location->longitude
                    ];
                    $distance = Helpers::get_distance($originCoordinates, $destinationCoordinates);

                    $distance =  round($distance,2).' KM';
                }




                $data = [
                    'id' => $item['id'],
                    'name' => $item['f_name'] . ' ' . $item['l_name'],
                    'image' => $item['image'],
                    'image_full_url' => $item['image_full_url'],
                    'current_orders' => $item['current_orders'],
                    'lat' => $item->last_location ? $item->last_location->latitude : '0',
                    'lng' => $item->last_location ? $item->last_location->longitude : '0',
                    'location' => $item->last_location ? $item->last_location->location : '',
                    'distance' => $distance ?? '',
                    'wallet' => $item['wallet'],
                ];

                return $data;
        }

        foreach ($data as $item) {
        if( $restaurant_lat &&  $restaurant_lng && $item->last_location){
//            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $restaurant_lat . ',' . $restaurant_lng . '&destinations=' . ($item->last_location ? $item->last_location->latitude : 0 ). ',' . ($item->last_location ? $item->last_location->longitude : 0) . '&key=' . $map_api_key . '&mode=walking');
//            $distance=  $response->json();
//            $distance= gettype($distance) == 'array' ? $distance: json_decode($distance,true);
//            $distance = data_get($distance,'rows.0.elements.0.distance.text',' ');

            $originCoordinates =[
                $restaurant_lat,
                $restaurant_lng
            ];
            $destinationCoordinates =[
                $item->last_location->latitude,
                $item->last_location->longitude
            ];
            $distance = Helpers::get_distance($originCoordinates, $destinationCoordinates);
            $distance =  round($distance,2).' KM';
        }

            $storage[] = [
                'id' => $item['id'],
                'name' => $item['f_name'] . ' ' . $item['l_name'],
                'image' => $item['image'],
                'image_full_url' => $item['image_full_url'],
                'current_orders' => $item['current_orders'],
                'lat' => $item->last_location ? $item->last_location->latitude : '0',
                'lng' => $item->last_location ? $item->last_location->longitude : '0',
                'location' => $item->last_location ? $item->last_location->location : '',
                'distance' => $distance ?? '',
                'wallet' => $item['wallet'],
                // 'wallet' => data_get($item, 'wallet'),
            ];
        }

        $data = $storage;

        return $data;
    }

    public static function address_data_formatting($data)
    {
        foreach ($data as $key=>$item) {
            $data[$key]['zone_ids'] = array_column(Zone::query()->whereContains('coordinates', new Point($item->latitude, $item->longitude, POINT_SRID))->latest()->get(['id'])->toArray(), 'id');
        }
        return $data;
    }

    public static function deliverymen_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $item['avg_rating'] = (float)(count($item->rating) ? (float)$item->rating[0]->average : 0);
            $item['rating_count'] = (int)(count($item->rating) ? $item->rating[0]->rating_count : 0);
            $item['lat'] = $item->last_location ? $item->last_location->latitude : null;
            $item['lng'] = $item->last_location ? $item->last_location->longitude : null;
            $item['location'] = $item->last_location ? $item->last_location->location : null;

            if ($item['rating']) {
                unset($item['rating']);
            }
            if ($item['last_location']) {
                unset($item['last_location']);
            }
            $storage[] = $item;
        }
        $data = $storage;

        return $data;
    }

}
