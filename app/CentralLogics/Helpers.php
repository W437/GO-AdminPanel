<?php
namespace App\CentralLogics;

use App\Models\Allergy;
use App\Models\Nutrition;
use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use App\Models\Log;
use App\Models\Food;
use App\Models\User;
use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Order;
use App\Library\Payer;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Expense;
use App\Models\TimeLog;
use App\Models\Vehicle;
use App\Traits\Payment;
use App\Mail\PlaceOrder;
use App\Models\CashBack;
use App\Models\Category;
use App\Models\Currency;
use App\Models\DMReview;
use App\Library\Receiver;
use App\Models\Restaurant;
use App\Models\VisitorLog;
use App\Models\DataSetting;
use App\Models\DeliveryMan;
use App\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\Models\VariationOption;
use App\Models\RestaurantWallet;
use App\CentralLogics\OrderLogic;
use App\CentralLogics\Formatting\DataFormatter;
use App\Models\DeliveryManWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderVerificationMail;
use App\Models\NotificationMessage;
use App\Models\NotificationSetting;
use App\Models\SubscriptionPackage;
use App\Traits\PaymentGatewayTrait;
use Illuminate\Support\Facades\App;
use App\Mail\SubscriptionSuccessful;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\CentralLogics\RestaurantLogic;
use App\Mail\SubscriptionRenewOrShift;
use App\Models\RestaurantSubscription;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Library\Payment as PaymentInfo;
use App\Models\SubscriptionTransaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\RestaurantNotificationSetting;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Models\SubscriptionBillingAndRefundHistory;
use App\CentralLogics\Pricing\PricingService;
use App\CentralLogics\Config\ConfigService;
use App\CentralLogics\Notifications\PushNotificationService;
use App\CentralLogics\Media\MediaService;
use App\CentralLogics\Orders\OrderNotificationService;
use App\CentralLogics\Subscription\SubscriptionService;
use App\CentralLogics\Access\AccessService;
use App\Traits\NotificationDataSetUpTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;

class Helpers
{
    use PaymentGatewayTrait, NotificationDataSetUpTrait;
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function error_formater($key, $mesage, $errors = [])
    {
        $errors[] = ['code' => $key, 'message' => $mesage];

        return $errors;
    }

    /**
     * Normalize phone number format for Israeli numbers
     * Ensures consistent format with leading 0 after country code
     * Example: +972522988206 -> +9720522988206
     */
    public static function normalizeIsraeliPhone($phone)
    {
        if (empty($phone)) {
            return $phone;
        }

        // Remove any spaces or special characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Check if it's an Israeli number (+972)
        if (strpos($phone, '+972') === 0) {
            // Get the part after +972
            $number = substr($phone, 4);

            // If the number doesn't start with 0, add it
            if (!empty($number) && $number[0] !== '0') {
                $phone = '+9720' . $number;
            }
        }

        return $phone;
    }

    public static function schedule_order()
    {
        return (bool)Helpers::getSettingsDataFromConfig(settings: 'schedule_order')?->value;
    }



    public static function variation_price($product, $variations)
    {
        $match = $variations;
        $result = 0;
            foreach($product as $product_variation){
                foreach($product_variation['values'] as $option){
                    foreach($match as $variation){
                        if($product_variation['name'] == $variation['name'] && isset($variation['values']) && in_array($option['label'], $variation['values']['label'])){
                            $result += $option['optionPrice'];
                        }
                    }
                }
            }

        return $result;
    }

    public static function cart_product_data_formatting($data, $selected_variation, $selected_addons, $selected_addon_quantity, $trans = false, $local = 'en')
    {
        return DataFormatter::cart_product_data_formatting($data, $selected_variation, $selected_addons, $selected_addon_quantity, $trans, $local);
    }

    public static function product_data_formatting($data, $multi_data = false, $trans = false, $local = 'en', $maxDiscount = true)
    {
        return DataFormatter::product_data_formatting($data, $multi_data, $trans, $local, $maxDiscount);
    }

    public static function product_data_formatting_translate($data, $multi_data = false, $trans = false, $local = 'en')
    {
        return DataFormatter::product_data_formatting_translate($data, $multi_data, $trans, $local);
    }

    public static function addon_data_formatting($data, $multi_data = false, $trans = false, $local = 'en')
    {
        return DataFormatter::addon_data_formatting($data, $multi_data, $trans, $local);
    }

    public static function category_data_formatting($data, $multi_data = false, $trans = false)
    {
        return DataFormatter::category_data_formatting($data, $multi_data, $trans);
    }

    public static function basic_campaign_data_formatting($data, $multi_data = false)
    {
        return DataFormatter::basic_campaign_data_formatting($data, $multi_data);
    }

    public static function restaurant_data_formatting($data, $multi_data = false)
    {
        return DataFormatter::restaurant_data_formatting($data, $multi_data);
    }

    public static function wishlist_data_formatting($data, $multi_data = false)
    {
        return DataFormatter::wishlist_data_formatting($data, $multi_data);
    }

    public static function order_data_formatting($data, $multi_data = false)
    {
        return DataFormatter::order_data_formatting($data, $multi_data);
    }

    public static function order_details_data_formatting($data)
    {
        return DataFormatter::order_details_data_formatting($data);
    }

    public static function deliverymen_list_formatting($data, $restaurant_lat = null, $restaurant_lng = null, $single_data = false)
    {
        return DataFormatter::deliverymen_list_formatting($data, $restaurant_lat, $restaurant_lng, $single_data);
    }

    public static function address_data_formatting($data)
    {
        return DataFormatter::address_data_formatting($data);
    }

    public static function deliverymen_data_formatting($data)
    {
        return DataFormatter::deliverymen_data_formatting($data);
    }
    // public static function get_business_settings($name, $json_decode = true)
    // {
    //     $config = null;
    //     $settings = Cache::rememberForever('business_settings_all_data', function () {
    //         return BusinessSetting::all();
    //     });

    //     $data = $settings?->firstWhere('key', $name);
    //     if (isset($data)) {
    //         $config = $json_decode? json_decode($data['value'], true) : $data['value'];
    //         if (is_null($config)) {
    //             $config = $data['value'];
    //         }
    //     }
    //     return $config;
    // }
    public static function get_business_settings($key, $json_decode = true,$relations = [])
    {
        return ConfigService::get_business_settings($key, $json_decode, $relations);
    }

    public static function currency_code()
    {
        return ConfigService::currency_code();
    }

    public static function currency_symbol()
    {
        return ConfigService::currency_symbol();
    }

    public static function format_currency($value)
    {
        return ConfigService::format_currency($value);
    }

    public static function sendNotificationToHttp(array|null $data)
    {
        return PushNotificationService::sendNotificationToHttp($data);
    }

    public static function getAccessToken($key)
    {
        return PushNotificationService::getAccessToken($key);
    }

    public static function send_push_notif_to_device($fcm_token, $data, $web_push_link = null)
    {
        return PushNotificationService::send_push_notif_to_device($fcm_token, $data, $web_push_link);
    }

    public static function send_push_notif_to_topic($data, $topic, $type, $web_push_link = null)
    {
        return PushNotificationService::send_push_notif_to_topic($data, $topic, $type, $web_push_link);
    }
    public static function send_push_notif_for_demo_reset($data, $topic, $type)
    {
        return PushNotificationService::send_push_notif_for_demo_reset($data, $topic, $type);
    }

    public static function send_push_notif_for_maintenance_mode($data, $topic, $type)
    {
        return PushNotificationService::send_push_notif_for_maintenance_mode($data, $topic, $type);
    }

    public static function rating_count($food_id, $rating)
    {
        return Review::where(['food_id' => $food_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($food, $price)
    {
        return PricingService::tax_calculate($food, $price);
    }

    public static function discount_calculate($product, $price)
    {
        return PricingService::discount_calculate($product, $price);
    }

    public static function get_product_discount($product)
    {
        return PricingService::get_product_discount($product);
    }

    public static function product_discount_calculate($product, $price, $restaurant)
    {
        return PricingService::product_discount_calculate($product, $price, $restaurant);
    }

    public static function product_discount_calculate_data($product, $restaurant)
    {
        return PricingService::product_discount_calculate_data($product, $restaurant);
    }

    public static function food_discount_calculate($product, $price, $restaurant, $check_restaurant_discount = true)
    {
        return PricingService::food_discount_calculate($product, $price, $restaurant, $check_restaurant_discount);
    }

    public static function get_price_range($product, $discount = false)
    {
        return PricingService::get_price_range($product, $discount);
    }

    public static function get_restaurant_discount($restaurant)
    {
        //dd($restaurant);
        if ($restaurant->discount) {
            if (date('Y-m-d', strtotime($restaurant->discount->start_date)) <= now()->format('Y-m-d') && date('Y-m-d', strtotime($restaurant->discount->end_date)) >= now()->format('Y-m-d') && date('H:i', strtotime($restaurant->discount->start_time)) <= now()->format('H:i') && date('H:i', strtotime($restaurant->discount->end_time)) >= now()->format('H:i')) {
                return [
                    'discount' => $restaurant->discount->discount,
                    'min_purchase' => $restaurant->discount->min_purchase,
                    'max_discount' => $restaurant->discount->max_discount
                ];
            }
        }
        return null;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }






    public static function order_status_update_message($status, $lang='default')
    {
        return OrderNotificationService::order_status_update_message($status, $lang);
    }

    public static function send_order_notification($order)
    {
        return OrderNotificationService::send_order_notification($order);
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function env_update($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key . '=' . env($key),
                $key . '=' . $value,
                file_get_contents($path)
            ));
        }
    }

    public static function env_key_replace($key_from, $key_to, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key_from . '=' . env($key_from),
                $key_to . '=' . $value,
                file_get_contents($path)
            ));
        }
    }

    public static  function remove_dir($dir)
    {
//        if (DOMAIN_POINTED_DIRECTORY == 'public') {
//            $dir = '../'.$dir;
//        }
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_restaurant_id()
    {
        return AccessService::get_restaurant_id();
    }

    public static function get_vendor_id()
    {
        return AccessService::get_vendor_id();
    }

    public static function get_vendor_data()
    {
        return AccessService::get_vendor_data();
    }

    public static function get_loggedin_user()
    {
        return AccessService::get_loggedin_user();
    }

    public static function get_restaurant_data()
    {
        return AccessService::get_restaurant_data();
    }

    public static function getDisk()
    {
        return MediaService::getDisk();
    }

    public static function upload(string $dir, string $format, $image = null)
    {
        return MediaService::upload($dir, $format, $image);
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        return MediaService::update($dir, $old_image, $format, $image);
    }

    public static function check_and_delete(string $dir, $old_image)
    {
        return MediaService::check_and_delete($dir, $old_image);
    }

    public static function generate_video_thumbnail(string $dir, string $video_filename)
    {
        return MediaService::generate_video_thumbnail($dir, $video_filename);
    }

    public static function generate_blurhash(string $dir, string $image_filename, int $components_x = 5, int $components_y = 4)
    {
        return MediaService::generate_blurhash($dir, $image_filename, $components_x, $components_y);
    }

    public static function get_full_url($path,$data,$type,$placeholder = null){
        return MediaService::get_full_url($path,$data,$type,$placeholder);
    }

    public static function format_coordiantes($coordinates)
    {
        $data = [];
        foreach ($coordinates as $coord) {
            $data[] = (object)['lat' => $coord[1], 'lng' => $coord[0]];
        }
        return $data;
    }

    public static function module_permission_check($mod_name)
    {
        return AccessService::module_permission_check($mod_name);
    }

    public static function employee_module_permission_check($mod_name)
    {
        return AccessService::employee_module_permission_check($mod_name);
    }


    public static function calculate_addon_price($addons, $add_on_qtys , $incrementCount = false ,$old_selected_addons =[])
    {
        $add_ons_cost = 0;
        $data = [];
        if ($addons) {
            foreach ($addons as $key2 => $addon) {
                if ($add_on_qtys == null) {
                    $add_on_qty = 1;
                } else {
                    $add_on_qty = $add_on_qtys[$key2];
                }
                // if($add_on_qty > 0 ){
                    if($addon->stock_type != 'unlimited'){

                        $availableStock=$addon->addon_stock;

                        if(data_get($old_selected_addons, $addon->id)){
                            $availableStock= $availableStock + data_get($old_selected_addons, $addon->id);
                        }

                        if(  $availableStock <= 0 || $availableStock < $add_on_qty  ){
                            return ['out_of_stock' => $addon->name .' ' . translate('Addon_is_out_of_stock_!!!'),
                            'id'=>$addon->id,
                            'current_stock' =>   $availableStock > 0 ?  $availableStock : 0 ,
                            'type'=>'addon'
                        ];
                        }
                    }
                    if($incrementCount == true){
                        $addon->increment('sell_count' ,$add_on_qty);
                    }
                // }

                $data[] = ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price, 'quantity' => $add_on_qty];
                $add_ons_cost += $addon['price'] * $add_on_qty;
            }
            return ['addons' => $data, 'total_add_on_price' => $add_ons_cost];
        }
        return null;
    }

    public static function get_settings($name)
    {
        return ConfigService::get_settings($name);
    }
    public static function get_settings_storage($name)
    {
        return ConfigService::get_settings_storage($name);
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        return ConfigService::setEnvironmentValue($envKey, $envValue);
    }



    public static function insert_business_settings_key($key, $value = null)
    {
        return ConfigService::insert_business_settings_key($key, $value);
    }
    public static function insert_data_settings_key($key,$type, $value = null)
    {
        return ConfigService::insert_data_settings_key($key, $type, $value);
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function get_view_keys()
    {
        $keys = BusinessSetting::whereIn('key', ['toggle_veg_non_veg', 'toggle_dm_registration', 'toggle_restaurant_registration'])->get();
        $data = [];
        foreach ($keys as $key) {
            $data[$key->key] = (bool)$key->value ?? 0;
        }
        return $data;
    }


    public static function default_lang()
    {
        if (strpos(url()->current(), '/api')) {
            $lang = App::getLocale();
        } elseif ( request()->is('admin*') && auth('admin')?->check() && session()->has('local')) {
            $lang = session('local');
        }elseif (request()->is('restaurant-panel/*') && (auth('vendor_employee')?->check() || auth('vendor')?->check()) && session()->has('vendor_local')) {
            $lang = session('vendor_local');
        }
        elseif (session()->has('landing_local')) {
            $lang = session('landing_local');
        }
        elseif (session()->has('local')) {
            $lang = session('local');
        } else {
            $data = Helpers::get_business_settings('language');
            $code = 'en';
            $direction = 'ltr';
            foreach ($data as $ln) {
                if (is_array($ln) && array_key_exists('default', $ln) && $ln['default']) {
                    $code = $ln['code'];
                    if (array_key_exists('direction', $ln)) {
                        $direction = $ln['direction'];
                    }
                }
            }
            session()->put('local', $code);
            $lang = $code;
        }
        return $lang;
    }

    public static function system_default_language()
    {
        $languages = json_decode(\App\Models\BusinessSetting::where('key', 'system_language')->first()?->value);
        $lang = 'en';

        foreach ($languages as $key => $language) {
            if($language->default){
                $lang = $language->code;
            }
        }
        return $lang;
    }
    public static function system_default_direction()
    {
        $languages = json_decode(\App\Models\BusinessSetting::where('key', 'system_language')->first()?->value);
        $lang = 'en';

        foreach ($languages as $key => $language) {
            if($language->default){
                $lang = $language->direction;
            }
        }
        return $lang;
    }

    public static function generate_referer_code() {
        $ref_code = strtoupper(Str::random(10));
        if (self::referer_code_exists($ref_code)) {
            return self::generate_referer_code();
        }
        return $ref_code;
    }

    public static function referer_code_exists($ref_code) {
        return User::where('ref_code', '=', $ref_code)->exists();
    }


    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"',';', '<', '>'], ' ', $str);
    }

    public static function set_time_log($user_id , $date, $online = null, $offline = null,$shift_id = null)
    {
        try {
            $time_log = TimeLog::where(['user_id'=>$user_id, 'date'=>$date ,'shift_id'  => $shift_id])->first();

            if($time_log && $time_log->online && $online) return true;

            if($time_log && $offline) {
                $time_log->offline = $offline;

                if($time_log->online){
                    $time_log->working_hour = (strtotime($offline) - strtotime($time_log->online))/60;
                }
                else{
                    $time_log->online =$offline;
                    $time_log->working_hour =  0;
                }

                $time_log->shift_id = $shift_id;
                $time_log->save();
                return true;
            }

            if(!$time_log){
                $time_log = new TimeLog;
                $time_log->date = $date;
                $time_log->user_id = $user_id;
                $time_log->offline = $offline;
                $time_log->online = $online ?? $offline ;
                $time_log->working_hour =0;
                $time_log->shift_id = $shift_id;
                $time_log->save();
            }
            return true;
        } catch(\Exception $e) {
            info(["line___{$e->getLine()}",$e->getMessage()]);
        }
        return false;
    }

    public static function push_notification_export_data($data){
        $format = [];
        foreach($data as $key=>$item){
            $format[] =[
                '#'=>$key+1,
                translate('title')=>$item['title'],
                translate('description')=>$item['description'],
                translate('zone')=>$item->zone ? $item->zone->name : translate('messages.all_zones'),
                translate('tergat')=>$item['tergat'],
                translate('status')=>$item['status']
            ];
        }
        return $format;
    }


    public static function export_restaurants($collection){
        $data = [];

        foreach($collection as $key=>$item){

            $data[] = [
                'id'=>$item->id,
                'ownerID'=>$item->vendor->id,
                'ownerFirstName'=>$item->vendor->f_name,
                'ownerLastName'=>$item->vendor->l_name,
                'restaurantName'=>$item->name,
                'CoverPhoto'=>$item->cover_photo,
                'logo'=>$item->logo,
                'phone'=>$item->vendor->phone,
                'email'=>$item->vendor->email,
                'latitude'=>$item->latitude,
                'longitude'=>$item->longitude,
                'zone_id'=>$item->zone_id,
                'Address'=>$item->address ?? null,
                'Slug'=> $item->slug  ?? null,
                'MinimumOrderAmount'=>$item->minimum_order,
                'Comission'=>$item->comission ?? 0,
                'Tax'=>$item->tax ?? 0,

                'DeliveryTime'=>$item->delivery_time ?? '20-30',
                'MinimumDeliveryFee'=>$item->minimum_shipping_charge ?? 0,
                'PerKmDeliveryFee'=>$item->per_km_shipping_charge ?? 0,
                'MaximumDeliveryFee'=>$item->maximum_shipping_charge ?? 0,
                // 'order_count'=>$item->order_count,
                // 'total_order'=>$item->total_order,
                'RestaurantModel'=>$item->restaurant_model,
                'ScheduleOrder'=> $item->schedule_order == 1 ? 'yes' : 'no',
                'FreeDelivery'=> $item->free_delivery == 1 ? 'yes' : 'no',
                'TakeAway'=> $item->take_away == 1 ? 'yes' : 'no',
                'Delivery'=> $item->delivery == 1 ? 'yes' : 'no',
                'Veg'=> $item->veg == 1 ? 'yes' : 'no',
                'NonVeg'=> $item->non_veg == 1 ? 'yes' : 'no',
                'OrderSubscription'=> $item->order_subscription_active == 1 ? 'yes' : 'no',
                'Status'=> $item->status == 1 ? 'active' : 'inactive',
                'FoodSection'=> $item->food_section == 1 ? 'active' : 'inactive',
                'ReviewsSection'=> $item->reviews_section == 1 ? 'active' : 'inactive',
                'SelfDeliverySystem'=> $item->self_delivery_system == 1 ? 'active' : 'inactive',
                'PosSystem'=> $item->pos_system == 1 ? 'active' : 'inactive',
                'RestaurantOpen'=> $item->active == 1 ? 'yes' : 'no',
                // 'gst'=>$item->restaurants[0]->gst ?? null,
            ];
        }

        return $data;
    }


    public static function export_attributes($collection){
        $data = [];
        foreach($collection as $key=>$item){
            $data[] = [
                'SL'=>$key+1,
                 translate('messages.id') => $item['id'],
                 translate('messages.name') => $item['name'],
            ];
        }

        return $data;
    }

    public static function get_varient(array $product_variations, array $variations)
    {
        $result = [];
        $variation_price = 0;

        foreach($variations as $k=> $variation){
            foreach($product_variations as  $product_variation){
                if( isset($variation['values']) && isset($product_variation['values']) && $product_variation['name'] == $variation['name']  ){
                    $result[$k] = $product_variation;
                    $result[$k]['values'] = [];
                    foreach($product_variation['values'] as $key=> $option){
                        if(in_array($option['label'], $variation['values']['label'])){
                            $result[$k]['values'][] = $option;
                            $variation_price += $option['optionPrice'];
                        }
                    }
                }
            }
        }

        return ['price'=>$variation_price,'variations'=>array_values($result)];
      }

    public static function get_edit_varient(array $product_variations, $variations)
    {
        $result = [];
        $variation_price = 0;

        foreach ($variations as $k => $variation) {
            foreach ($product_variations as $product_variation) {
                if (
                    isset($variation['values']) &&
                    isset($product_variation['values']) &&
                    $product_variation['name'] == $variation['name']
                ) {
                    $result[$k] = $product_variation;
                    $result[$k]['values'] = [];

                    foreach ($product_variation['values'] as $option) {
                        foreach ($variation['values'] as $selected) {
                            if (isset($selected['label']) && $option['label'] === $selected['label']) {
                                $result[$k]['values'][] = $option;
                                $variation_price += $option['optionPrice'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        return ['price' => $variation_price, 'variations' => $result];
    }


    public Static function subscription_check()
    {
        return SubscriptionService::subscription_check();
    }

    public Static function commission_check()
    {
        return SubscriptionService::commission_check();
    }

    public static function check_subscription_validity()
    {
        return SubscriptionService::check_subscription_validity();
    }

    public static function subscription_plan_chosen($restaurant_id ,$package_id, $payment_method  ,$discount = 0,$pending_bill =0,$reference=null ,$type=null){
        return SubscriptionService::subscription_plan_chosen($restaurant_id ,$package_id, $payment_method  ,$discount,$pending_bill,$reference,$type);
    }

    public static function expenseCreate($amount,$type,$datetime,$created_by,$order_id=null,$restaurant_id=null,$user_id=null,$description='',$delivery_man_id=null)
    {
        $expense = new Expense();
        $expense->amount = $amount;
        $expense->type = $type;
        $expense->order_id = $order_id;
        $expense->created_by = $created_by;
        $expense->restaurant_id = $restaurant_id;
        $expense->delivery_man_id = $delivery_man_id;
        $expense->user_id = $user_id;
        $expense->description = $description;
        $expense->created_at = $datetime;
        $expense->updated_at = $datetime;
        return $expense->save();
    }
    public static function hex_to_rbg($color){
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $output = "$r, $g, $b";
        return $output;
    }

    public static function increment_order_count($data){
        $restaurant=$data;
        $rest_sub=$restaurant->restaurant_sub;
        if ( $restaurant->restaurant_model == 'subscription' && isset($rest_sub) && $rest_sub->max_order != "unlimited") {
            $rest_sub->increment('max_order', 1);
        }
        return true;
    }

    public static function react_activation_check($react_domain, $react_license_code){
        // License check disabled for custom deployment
        return true;
    }

    public static function activation_submit($purchase_key)
    {
        // License validation removed - always return true
        return true;
    }

    public static function react_domain_status_check(){
        $data = self::get_business_settings('react_setup');
        if($data && isset($data['react_domain']) && isset($data['react_license_code'])){
            if(isset($data['react_platform']) && $data['react_platform'] == 'codecanyon'){
                $data['status'] = (int)self::activation_submit($data['react_license_code']);
            }elseif(!self::react_activation_check($data['react_domain'], $data['react_license_code'])){
                $data['status']=0;
            }elseif($data['status'] != 1){
                $data['status']=1;
            }
            Helpers::businessUpdateOrInsert(['key' => 'react_setup'], [
                'value' => json_encode($data)
            ]);
        }
    }

    public static function number_format_short( $n ) {
        if ($n < 900) {
            // 0 - 900
            $n = $n;
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n = $n / 1000;
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n = $n / 1000000;
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n = $n / 1000000000;
            $suffix = 'B';
        } else {
            // 0.9t+
            $n = $n / 1000000000000;
            $suffix = 'T';
        }

        if(!session()->has('currency_symbol_position')){
            $currency_symbol_position = BusinessSetting::where(['key' => 'currency_symbol_position'])->first()->value;
            session()->put('currency_symbol_position',$currency_symbol_position);
        }
        $currency_symbol_position = session()->get('currency_symbol_position');

        return $currency_symbol_position == 'right' ? number_format($n, config('round_up_to_digit')).$suffix . ' ' . self::currency_symbol() : self::currency_symbol() . ' ' . number_format($n, config('round_up_to_digit')).$suffix;
    }


    public static function gen_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/../../storage/tmp','default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        /* $mpdf->AddPage('XL', '', '', '', '', 10, 10, 10, '10', '270', '');*/
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view;
        $mpdf_view = $mpdf_view->render();
        $mpdf->WriteHTML($mpdf_view);
        $mpdf->Output($file_prefix . $file_postfix . '.pdf', 'D');
    }

    public static function down_mpdf($view, $file_prefix, $file_postfix)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/../../storage/tmp', 'default_font' => 'FreeSerif', 'mode' => 'utf-8', 'format' => [190, 250]]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $mpdf_view = $view->render();
        $mpdf->WriteHTML($mpdf_view);

        $file_name = $file_prefix . $file_postfix . '.pdf';
        $file_path = storage_path('app/public/pdfs/' . $file_name);

        if (!file_exists(storage_path('app/public/pdfs'))) {
            mkdir(storage_path('app/public/pdfs'), 0777, true);
        }

        $mpdf->Output($file_path, 'F');

        return $file_name;
    }


    public static function product_tax($price , $tax, $is_include=false){
        $price_tax = ($price * $tax) / (100 + ($is_include?$tax:0)) ;
        return $price_tax;
    }

    public static function dm_wallet_transaction($delivery_man_id, $amount, $referance = null, $type = 'dm_admin_bonus')
    {
        if (!$dmwallet = DeliveryManWallet::firstOrNew(['delivery_man_id' => $delivery_man_id])) return false;
        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->transaction_id = Str::uuid();
        $wallet_transaction->reference = $referance;
        $wallet_transaction->transaction_type = $type;
        $wallet_transaction->admin_bonus = $amount;
        $wallet_transaction->credit = $amount;
        $wallet_transaction->debit = 0;
        $wallet_transaction->balance = $dmwallet->total_earning + $amount;
        $wallet_transaction->created_at = now();
        $wallet_transaction->updated_at = now();
        $wallet_transaction->delivery_man_id = $delivery_man_id;
        try {
            DB::beginTransaction();
            $wallet_transaction->save();
            $dmwallet->total_earning = $dmwallet->total_earning + $amount;
            $dmwallet->save();
            Helpers::expenseCreate(amount:$amount,type:$type,datetime:now(), created_by:'admin',delivery_man_id:$delivery_man_id);
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            info(['dm_wallet_transaction_error', 'code' => $ex->getLine(), 'message' => $ex->getMessage()]);
            return false;
        }
    }

    public static function get_subscription_schedules($type, $startDate, $endDate, $days)
    {
        $arrayOfDate = [];
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $days = $type != 'daily' ? array_column($days, 'time', 'day') : $days;
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            if($type == 'weekly'){
                if(isset($days[$date->weekday()])){
                    $arrayOfDate[] = $date->format('Y-m-d ').$days[$date->weekday()];
                }
            }elseif($type == 'monthly'){
                if(isset($days[$date->day])){
                    $arrayOfDate[] = $date->format('Y-m-d ').$days[$date->day];
                }
            }else{
                $arrayOfDate[] = $date->format('Y-m-d ').$days[0]['itme'];
            }
        }
        return $arrayOfDate;
    }



    public static function visitor_log($model,$user_id,$visitor_log_id,$order_count=false){
            if( $model == 'restaurant' ){
                $visitor_log_type = 'App\Models\Restaurant';
            }
            else {
                $visitor_log_type = 'App\Models\Category';
            }
        VisitorLog::updateOrInsert(
            ['visitor_log_type' => $visitor_log_type,
                'user_id' => $user_id,
                'visitor_log_id' => $visitor_log_id,
            ],
            [
                'visit_count' => $order_count == false ? DB::raw('visit_count + 1') : DB::raw('visit_count'),
                'order_count' =>  $order_count == true ? DB::raw('order_count + 1') : DB::raw('order_count'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return;
    }

    public static function getLanguageCode(string $country_code): string
    {
        $locales = array(
            'en-English(default)',
            'af-Afrikaans',
            'sq-Albanian - shqip',
            'am-Amharic - አማርኛ',
            'ar-Arabic - العربية',
            'an-Aragonese - aragonés',
            'hy-Armenian - հայերեն',
            'ast-Asturian - asturianu',
            'az-Azerbaijani - azərbaycan dili',
            'eu-Basque - euskara',
            'be-Belarusian - беларуская',
            'bn-Bengali - বাংলা',
            'bs-Bosnian - bosanski',
            'br-Breton - brezhoneg',
            'bg-Bulgarian - български',
            'ca-Catalan - català',
            'ckb-Central Kurdish - کوردی (دەستنوسی عەرەبی)',
            'zh-Chinese - 中文',
            'zh-HK-Chinese (Hong Kong) - 中文（香港）',
            'zh-CN-Chinese (Simplified) - 中文（简体）',
            'zh-TW-Chinese (Traditional) - 中文（繁體）',
            'co-Corsican',
            'hr-Croatian - hrvatski',
            'cs-Czech - čeština',
            'da-Danish - dansk',
            'nl-Dutch - Nederlands',
            'en-AU-English (Australia)',
            'en-CA-English (Canada)',
            'en-IN-English (India)',
            'en-NZ-English (New Zealand)',
            'en-ZA-English (South Africa)',
            'en-GB-English (United Kingdom)',
            'en-US-English (United States)',
            'eo-Esperanto - esperanto',
            'et-Estonian - eesti',
            'fo-Faroese - føroyskt',
            'fil-Filipino',
            'fi-Finnish - suomi',
            'fr-French - français',
            'fr-CA-French (Canada) - français (Canada)',
            'fr-FR-French (France) - français (France)',
            'fr-CH-French (Switzerland) - français (Suisse)',
            'gl-Galician - galego',
            'ka-Georgian - ქართული',
            'de-German - Deutsch',
            'de-AT-German (Austria) - Deutsch (Österreich)',
            'de-DE-German (Germany) - Deutsch (Deutschland)',
            'de-LI-German (Liechtenstein) - Deutsch (Liechtenstein)
            ',
            'de-CH-German (Switzerland) - Deutsch (Schweiz)',
            'el-Greek - Ελληνικά',
            'gn-Guarani',
            'gu-Gujarati - ગુજરાતી',
            'ha-Hausa',
            'haw-Hawaiian - ʻŌlelo Hawaiʻi',
            'he-Hebrew - עברית',
            'hi-Hindi - हिन्दी',
            'hu-Hungarian - magyar',
            'is-Icelandic - íslenska',
            'id-Indonesian - Indonesia',
            'ia-Interlingua',
            'ga-Irish - Gaeilge',
            'it-Italian - italiano',
            'it-IT-Italian (Italy) - italiano (Italia)',
            'it-CH-Italian (Switzerland) - italiano (Svizzera)',
            'ja-Japanese - 日本語',
            'kn-Kannada - ಕನ್ನಡ',
            'kk-Kazakh - қазақ тілі',
            'km-Khmer - ខ្មែរ',
            'ko-Korean - 한국어',
            'ku-Kurdish - Kurdî',
            'ky-Kyrgyz - кыргызча',
            'lo-Lao - ລາວ',
            'la-Latin',
            'lv-Latvian - latviešu',
            'ln-Lingala - lingála',
            'lt-Lithuanian - lietuvių',
            'mk-Macedonian - македонски',
            'ms-Malay - Bahasa Melayu',
            'ml-Malayalam - മലയാളം',
            'mt-Maltese - Malti',
            'mr-Marathi - मराठी',
            'mn-Mongolian - монгол',
            'ne-Nepali - नेपाली',
            'no-Norwegian - norsk',
            'nb-Norwegian Bokmål - norsk bokmål',
            'nn-Norwegian Nynorsk - nynorsk',
            'oc-Occitan',
            'or-Oriya - ଓଡ଼ିଆ',
            'om-Oromo - Oromoo',
            'ps-Pashto - پښتو',
            'fa-Persian - فارسی',
            'pl-Polish - polski',
            'pt-Portuguese - português',
            'pt-BR-Portuguese (Brazil) - português (Brasil)',
            'pt-PT-Portuguese (Portugal) - português (Portugal)',
            'pa-Punjabi - ਪੰਜਾਬੀ',
            'qu-Quechua',
            'ro-Romanian - română',
            'mo-Romanian (Moldova) - română (Moldova)',
            'rm-Romansh - rumantsch',
            'ru-Russian - русский',
            'gd-Scottish Gaelic',
            'sr-Serbian - српски',
            'sh-Serbo-Croatian - Srpskohrvatski',
            'sn-Shona - chiShona',
            'sd-Sindhi',
            'si-Sinhala - සිංහල',
            'sk-Slovak - slovenčina',
            'sl-Slovenian - slovenščina',
            'so-Somali - Soomaali',
            'st-Southern Sotho',
            'es-Spanish - español',
            'es-AR-Spanish (Argentina) - español (Argentina)',
            'es-419-Spanish (Latin America) - español (Latinoamérica)
            ',
            'es-MX-Spanish (Mexico) - español (México)',
            'es-ES-Spanish (Spain) - español (España)',
            'es-US-Spanish (United States) - español (Estados Unidos)
            ',
            'su-Sundanese',
            'sw-Swahili - Kiswahili',
            'sv-Swedish - svenska',
            'tg-Tajik - тоҷикӣ',
            'ta-Tamil - தமிழ்',
            'tt-Tatar',
            'te-Telugu - తెలుగు',
            'th-Thai - ไทย',
            'ti-Tigrinya - ትግርኛ',
            'to-Tongan - lea fakatonga',
            'tr-Turkish - Türkçe',
            'tk-Turkmen',
            'tw-Twi',
            'uk-Ukrainian - українська',
            'ur-Urdu - اردو',
            'ug-Uyghur',
            'uz-Uzbek - o‘zbek',
            'vi-Vietnamese - Tiếng Việt',
            'wa-Walloon - wa',
            'cy-Welsh - Cymraeg',
            'fy-Western Frisian',
            'xh-Xhosa',
            'yi-Yiddish',
            'yo-Yoruba - Èdè Yorùbá',
            'zu-Zulu - isiZulu',
        );

        foreach ($locales as $locale) {
            $locale_region = explode('-',$locale);
            if ($country_code == $locale_region[0]) {
                return $locale_region[0];
            }
        }

        return "en";
    }

    public static function auto_translator($q, $sl, $tl)
    {
        // Skip translation if text doesn't need it (numbers, symbols only, etc.)
        if (!self::needsTranslation($q)) {
            return $q;
        }

        // Get translation provider setting (default to google)
        $provider = BusinessSetting::where('key', 'translation_provider')->first();
        $provider = $provider?->value ?? 'google';

        if ($provider === 'openai') {
            $key = config('services.openai.key');
            if (!$key) {
                \Log::warning('OpenAI provider selected but OPENAI_API_KEY is missing.');
                return $q;
            }

            try {
                return self::translate_with_openai($q, $sl, $tl);
            } catch (\Throwable $e) {
                \Log::warning('OpenAI Translation Error (single): ' . $e->getMessage());
                return $q;
            }
        }

        // Google (or any other provider) is selected explicitly
        return self::translate_with_google($q, $sl, $tl);
    }

    public static function needsTranslation($text)
    {
        // Remove whitespace for checking
        $trimmed = trim($text);

        // Empty or very short strings
        if (empty($trimmed) || strlen($trimmed) <= 1) {
            return false;
        }

        // Check if string contains ONLY numbers, spaces, and common symbols
        // This will match: "$00", "123", "10%", "$5.99", etc.
        if (preg_match('/^[\d\s\$\€\£\¥\₪\%\.\,\-\+\=\(\)\[\]\{\}\/\\\:\;]+$/u', $trimmed)) {
            return false;
        }

        // Check if string is ONLY symbols/punctuation (no letters or numbers)
        if (preg_match('/^[\p{P}\p{S}\s]+$/u', $trimmed)) {
            return false;
        }

        // Check for strings that are just variable placeholders
        // e.g., ":name", "{count}", "[total]", etc.
        if (preg_match('/^[\:\{\[\<][a-zA-Z0-9_]+[\:\}\]\>]$/u', $trimmed)) {
            return false;
        }

        // String needs translation
        return true;
    }

    public static function translate_with_google($q, $sl, $tl)
    {
        try {
            $res = file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=" . $sl . "&tl=" . $tl . "&hl=hl&q=" . urlencode($q), $_SERVER['DOCUMENT_ROOT'] . "/transes.html");
            $res = json_decode($res);
            return str_replace('_',' ',$res[0][0][0]);
        } catch (\Exception $e) {
            \Log::error('Google Translate Error: ' . $e->getMessage());
            return $q; // Return original text if translation fails
        }
    }

    public static function translate_with_openai($q, $sl, $tl)
    {
        try {
            $client = \OpenAI::client(config('services.openai.key'));

            // Get language names for better context
            $targetLanguage = self::getLanguageName($tl);
            $sourceLanguage = self::getLanguageName($sl);

            // Build context-aware prompt with specific instructions
            $prompt = self::buildTranslationPrompt($q, $sourceLanguage, $targetLanguage, $tl);

            $response = $client->chat()->create([
                'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => self::getTranslationSystemMessage($tl)],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3, // Lower temperature for more consistent translations
                'max_tokens' => 500,
            ]);

            $translated = $response->choices[0]->message->content;
            return str_replace('_',' ', trim($translated));

        } catch (\Exception $e) {
            \Log::error('OpenAI Translation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function translate_batch_parallel($items, $sl, $tl)
    {
        try {
            if (empty($items)) {
                return [];
            }

            $apiKey = config('services.openai.key');
            if (empty($apiKey)) {
                \Log::warning('OpenAI API key missing. Unable to run OpenAI batch translation.');
                return [];
            }

            $batchSize = max(1, (int) config('services.openai.batch_size', 100));
            $parallelWorkers = max(1, (int) config('services.openai.parallel_workers', 8));
            $timeout = max(10, (int) config('services.openai.timeout', 60));
            $maxRetries = max(0, (int) config('services.openai.max_retries', 2));
            $retryDelayMs = max(100, (int) config('services.openai.retry_delay_ms', 500));
            $model = config('services.openai.model', 'gpt-4o-mini');

            $targetLanguage = self::getLanguageName($tl);
            $sourceLanguage = self::getLanguageName($sl);
            $systemMessage = self::getTranslationSystemMessage($tl);

            $handlerStack = HandlerStack::create();
            if ($maxRetries > 0) {
                $handlerStack->push(Middleware::retry(
                    function ($retries, $request, $response, $exception) use ($maxRetries) {
                        if ($retries >= $maxRetries) {
                            return false;
                        }

                        if ($exception instanceof ConnectException) {
                            return true;
                        }

                        if ($exception instanceof RequestException) {
                            $context = $exception->getHandlerContext();
                            if (($context['errno'] ?? null) === 28) {
                                return true; // cURL timeout
                            }
                        }

                        if ($response) {
                            $status = $response->getStatusCode();
                            return $status >= 500 || $status === 429;
                        }

                        return false;
                    },
                    function ($retries) use ($retryDelayMs) {
                        return ($retryDelayMs / 1000) * pow(2, $retries);
                    }
                ));
            }

            $client = new Client([
                'base_uri' => 'https://api.openai.com/v1/',
                'timeout' => $timeout,
                'connect_timeout' => 10,
                'read_timeout' => $timeout,
                'http_errors' => false,
                'handler' => $handlerStack,
            ]);

            $batches = array_chunk($items, $batchSize, true);
            $batchMap = [];
            $allTranslations = [];

            $requests = function () use ($client, $batches, $apiKey, $systemMessage, $model, $sourceLanguage, $targetLanguage, $tl, &$batchMap) {
                foreach ($batches as $index => $batch) {
                    $batchMap[$index] = $batch;

                    yield $index => function () use ($client, $apiKey, $systemMessage, $model, $sourceLanguage, $targetLanguage, $tl, $batch) {
                        return $client->postAsync('chat/completions', [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $apiKey,
                                'Content-Type' => 'application/json',
                            ],
                            'json' => [
                                'model' => $model,
                                'messages' => [
                                    ['role' => 'system', 'content' => $systemMessage],
                                    ['role' => 'user', 'content' => self::buildBatchTranslationPrompt($batch, $sourceLanguage, $targetLanguage, $tl)],
                                ],
                                'temperature' => 0.2,
                                'max_tokens' => 8000,
                            ],
                        ]);
                    };
                }
            };

            $pool = new Pool($client, $requests(), [
                'concurrency' => $parallelWorkers,
                'fulfilled' => function ($response, $index) use (&$allTranslations, &$batchMap) {
                    $batch = $batchMap[$index] ?? [];
                    if (empty($batch)) {
                        return;
                    }

                    $status = $response->getStatusCode();
                    if ($status >= 300) {
                        $bodySnippet = substr((string) $response->getBody(), 0, 500);
                        \Log::error('OpenAI Batch Translation HTTP ' . $status . ': ' . $bodySnippet);
                        return;
                    }

                    $bodyString = (string) $response->getBody();
                    $payload = json_decode($bodyString, true);
                    $content = $payload['choices'][0]['message']['content'] ?? null;
                    $parsedTranslations = self::parseBatchTranslationResponse($content, $batch);

                    if (!empty($parsedTranslations)) {
                        $allTranslations = array_replace($allTranslations, $parsedTranslations);
                        \Log::info('OpenAI Batch Translation Success: Translated ' . count($parsedTranslations) . ' items');
                        return;
                    }
                    \Log::warning('OpenAI Batch Translation Warning: Unable to parse response. Payload snippet: ' . \Illuminate\Support\Str::limit($bodyString, 500));
                },
                'rejected' => function ($reason, $index) use (&$batchMap) {
                    $batch = $batchMap[$index] ?? [];
                    $message = $reason instanceof TransferException ? $reason->getMessage() : (string) $reason;
                    \Log::error('OpenAI Translation Request Failed: ' . $message);
                },
            ]);

            $pool->promise()->wait();

            return $allTranslations;

        } catch (\Throwable $e) {
            \Log::error('Batch OpenAI Translation Error: ' . $e->getMessage());
            return [];
        }
    }

    protected static function buildBatchTranslationPrompt(array $batch, string $sourceLanguage, string $targetLanguage, string $targetLanguageCode): string
    {
        $lines = [
            "Translate the following user interface texts from {$sourceLanguage} to {$targetLanguage}.",
            '',
            'Context: Food delivery and restaurant management application used in Israel/Palestine.',
            'Rules:',
            '1. Improve grammar if the source text is unclear while keeping the meaning.',
            '2. Preserve placeholders (e.g., :name, {count}, %s) exactly as they appear.',
            '3. Keep terminology consistent and professional yet friendly.',
            '4. Return ONLY valid JSON. Do not wrap the response in Markdown.',
            '5. Preserve any leading or trailing symbols (e.g., *, #, •) exactly as provided.',
        ];

        if ($targetLanguageCode === 'ar') {
            $lines[] = '6. Use Palestinian dialect spoken by Israeli Arabs; avoid overly formal MSA unless legally required.';
        } elseif ($targetLanguageCode === 'he') {
            $lines[] = '6. Use modern Israeli Hebrew without niqqud (no vowel dots) and keep the tone natural.';
        }

        $payload = [];
        foreach ($batch as $key => $text) {
            $payload[] = [
                'key' => $key,
                'text' => str_replace('_', ' ', (string) $text),
            ];
        }

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $lines[] = '';
        $lines[] = 'Return format: [{"key": "translation_key", "translation": "Translated text"}]';
        $lines[] = '';
        $lines[] = 'Input JSON:';
        $lines[] = $jsonPayload;

        return implode(PHP_EOL, $lines);
    }

    protected static function parseBatchTranslationResponse(?string $content, array $batch): array
    {
        if (empty($content)) {
            return [];
        }

        $cleanContent = trim($content);
        if (Str::startsWith($cleanContent, '```')) {
            $cleanContent = preg_replace('/^```(?:json)?/i', '', $cleanContent);
            $cleanContent = preg_replace('/```$/', '', $cleanContent);
            $cleanContent = trim($cleanContent);
        }

        $translations = [];

        // Attempt strict JSON decode first (entire payload)
        $translations = self::decodeTranslationJson($cleanContent, $batch);
        if (!empty($translations)) {
            return $translations;
        }

        // Try to extract embedded JSON snippets (many models prepend text)
        foreach (self::extractJsonBlocks($cleanContent) as $jsonBlock) {
            $translations = self::decodeTranslationJson($jsonBlock, $batch);
            if (!empty($translations)) {
                return $translations;
            }
        }

        // Fallback parser for KEY|VALUE or KEY:VALUE responses
        $lines = preg_split("/\r\n|\r|\n/", $cleanContent);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $delimiter = null;
            if (strpos($line, '|') !== false) {
                $delimiter = '|';
            } elseif (strpos($line, ':') !== false) {
                $delimiter = ':';
            } elseif (stripos($line, '->') !== false) {
                $delimiter = '->';
            }

            if ($delimiter === null) {
                continue;
            }

            [$key, $value] = array_map('trim', explode($delimiter, $line, 2));
            if ($key !== '' && array_key_exists($key, $batch)) {
                $translations[$key] = $value;
            }
        }

        if (!empty($translations)) {
            return $translations;
        }

        \Log::warning('OpenAI Batch Translation Warning: Response unparseable. Returning original strings. Snippet: ' . Str::limit($cleanContent, 200));

        // Final fallback: keep originals so progress can continue
        $fallback = [];
        foreach ($batch as $key => $value) {
            $fallback[$key] = $value;
        }

        return $fallback;
    }

    protected static function decodeTranslationJson(string $json, array $batch): array
    {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        $rows = $decoded;
        if (isset($decoded['translations']) && is_array($decoded['translations'])) {
            $rows = $decoded['translations'];
        }

        if (!is_array($rows)) {
            return [];
        }

        $translations = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = $row['key'] ?? null;
            $translation = $row['translation'] ?? null;
            if ($key !== null && array_key_exists($key, $batch) && $translation !== null) {
                $translations[$key] = trim((string) $translation);
            }
        }

        return $translations;
    }

    protected static function extractJsonBlocks(string $text): array
    {
        $blocks = [];

        $arrayStart = strpos($text, '[');
        $arrayEnd = strrpos($text, ']');
        if ($arrayStart !== false && $arrayEnd !== false && $arrayEnd > $arrayStart) {
            $blocks[] = substr($text, $arrayStart, $arrayEnd - $arrayStart + 1);
        }

        $objectStart = strpos($text, '{');
        $objectEnd = strrpos($text, '}');
        if ($objectStart !== false && $objectEnd !== false && $objectEnd > $objectStart) {
            $blocks[] = substr($text, $objectStart, $objectEnd - $objectStart + 1);
        }

        return $blocks;
    }

    
    public static function getTranslationSystemMessage($targetLanguageCode)
    {
        // Base system message
        $baseMessage = 'You are a professional translator specializing in food delivery and restaurant management applications. Your translations must be clear, consistent, and culturally appropriate for the target audience.';

        // Add language-specific instructions
        if ($targetLanguageCode === 'ar') {
            $baseMessage .= "\n\nIMPORTANT: For Arabic translations, use the Palestinian dialect of Israeli Arabs. This dialect should be natural, conversational, and familiar to Palestinians living in Israel. Avoid overly formal Modern Standard Arabic unless the text requires it (e.g., legal terms, official notifications).";
        } elseif ($targetLanguageCode === 'he') {
            $baseMessage .= "\n\nIMPORTANT: For Hebrew translations, write in modern Israeli Hebrew without niqqud (vowel dots) and keep the tone friendly yet professional.";
        }

        return $baseMessage;
    }

    public static function buildTranslationPrompt($text, $sourceLanguage, $targetLanguage, $targetLanguageCode)
    {
        $prompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}.\n\n";

        $prompt .= "CONTEXT: This is for a food delivery and restaurant management application used in Israel/Palestine.\n\n";

        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. If the source text is poorly written, grammatically incorrect, or unclear, improve it while translating\n";
        $prompt .= "2. Maintain clarity and consistency in terminology throughout\n";
        $prompt .= "3. Use language appropriate for the restaurant/food delivery industry\n";
        $prompt .= "4. Keep the tone professional yet friendly (suitable for customers and restaurant staff)\n";
        $prompt .= "5. Preserve any leading or trailing symbols (e.g., *, #, •) exactly as provided.\n";

        // Add language-specific instructions
        if ($targetLanguageCode === 'ar') {
            $prompt .= "6. Use Palestinian dialect spoken by Israeli Arabs - natural, everyday language\n";
            $prompt .= "7. Avoid formal Modern Standard Arabic unless the text is official/legal\n";
            $prompt .= "8. Use terms familiar to Palestinians in Israel (e.g., for 'delivery' use 'توصيل' not 'إيصال')\n";
            $prompt .= "9. Numbers and times should be clear and match local usage\n";
        } elseif ($targetLanguageCode === 'he') {
            $prompt .= "6. Use modern Israeli Hebrew without niqqud (no vowel dots) and keep the tone natural and friendly\n";
        }

        $prompt .= "\nONLY return the translated text, nothing else. Do NOT include explanations, notes, or the original text.\n\n";
        $prompt .= "Text to translate: {$text}";

        return $prompt;
    }

    public static function getLanguageName($code)
    {
        $languages = [
            'en' => 'English',
            'ar' => 'Arabic',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'hi' => 'Hindi',
            'bn' => 'Bengali',
            'ur' => 'Urdu',
            'tr' => 'Turkish',
            'nl' => 'Dutch',
            'pl' => 'Polish',
            'sv' => 'Swedish',
            'he' => 'Hebrew',
        ];
        return $languages[$code] ?? ucfirst($code);
    }
    public static function language_load()
    {
        if (\session()->has('language_settings')) {
            $language = \session('language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('language_settings', $language);
        }
        return $language;
    }
    public static function vendor_language_load()
    {
        if (\session()->has('vendor_language_settings')) {
            $language = \session('vendor_language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('vendor_language_settings', $language);
        }
        return $language;
    }



    public static function create_subscription_order_logs()
    {
        return SubscriptionService::create_subscription_order_logs();
    }

    // public static function create_all_logs($object , $action_type, $model){
    //     $restaurant_id = null;
    //     if ((auth('vendor_employee')->check() || auth('vendor')->check() || request('vendor') || auth('admin')->check()) || (request()->token && DeliveryMan::where('auth_token' , request()->token)->exists()) ) {
    //         if (auth('vendor_employee')->check()) {
    //             $loable_type = 'App\Models\VendorEmployee';
    //             $logable_id = auth('vendor_employee')->id();
    //             $restaurant_id=auth('vendor_employee')->user() != null && isset(auth('vendor_employee')->user()->restaurant) ? auth('vendor_employee')->user()->restaurant->id : null;
    //         } elseif (auth('vendor')->check() || request('vendor')) {
    //             $restaurant_id=auth('vendor')->user() != null && isset(auth('vendor')->user()->restaurants[0]) ? auth('vendor')->user()->restaurants[0]->id : null;
    //             $loable_type = 'App\Models\Vendor';
    //             $logable_id = auth('vendor')->id();

    //             if(request('vendor')){
    //                 $logable_id =request('vendor')->id;
    //                 $restaurant_id= isset(request('vendor')->restaurants[0]) ? request('vendor')->restaurants[0]->id : null;
    //             }
    //         //    dd(request('vendor')->restaurants[0]->id);
    //         } elseif (auth('admin')->check()) {
    //             $loable_type = 'App\Models\Admin';
    //             $logable_id = auth('admin')->id();
    //         }elseif (request()->token && DeliveryMan::where('auth_token' , request()->token)->exists()) {
    //             $loable_type = 'App\Models\DeliveryMan';
    //             $dm =DeliveryMan::where('auth_token' , request()->token)->with('restaurant')->first();
    //             $logable_id = $dm->id;
    //             if($dm->type == 'restaurant_wise' && $dm->restaurant){
    //                 $restaurant_id= $dm->restaurant->id;
    //             }
    //         }

    //         $log = new Log();
    //         $log->logable_type = $loable_type;
    //         $log->logable_id = $logable_id;
    //         $log->action_type = $action_type;
    //         $log->model = $model;
    //         $log->restaurant_id = $restaurant_id;
    //         $log->model_id = $object->id;
    //         $log->ip_address = request()->ip();
    //         $log->before_state = json_encode($object->getOriginal());
    //         $log->after_state = json_encode($object->getDirty());
    //         $log->save();
    //     }
    //     return true;
    // }

    public static function landing_language_load()
    {
        if (\session()->has('landing_language_settings')) {
            $language = \session('landing_language_settings');
        } else {
            $language = BusinessSetting::where('key', 'system_language')->first();
            \session()->put('landing_language_settings', $language);
        }
        return $language;
    }

    public static function generate_reset_password_code() {
        $code = strtoupper(Str::random(15));
        if (self::reset_password_code_exists($code)) {
            return self::generate_reset_password_code();
        }
        return $code;
    }

    public static function reset_password_code_exists($code) {
        return DB::table('password_resets')->where('token', '=', $code)->exists();
    }

    public static function Export_generator($datas) {
        foreach ($datas as $data) {
            yield $data;
        }
        return true;
    }

    public static function vehicle_extra_charge(float $distance_data) {
        $data =[];
        $vehicle = Vehicle::active()
        ->where(function ($query) use ($distance_data) {
            $query->where('starting_coverage_area', '<=', $distance_data)->where('maximum_coverage_area', '>=', $distance_data)
            ->orWhere(function ($query) use ($distance_data) {
                $query->where('starting_coverage_area', '>=', $distance_data);
            });
        })->orderBy('starting_coverage_area')->first();
        if(empty($vehicle)){
            $vehicle = Vehicle::active()->orderBy('maximum_coverage_area', 'desc')->first();
        }
        $data['extra_charge'] = $vehicle->extra_charges  ?? 0;
        $data['vehicle_id'] =  $vehicle->id  ?? null;
        return $data;
    }

    public static function react_services_formater($data)
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
        $data = $storage;
        return $data;
    }
    public static function react_react_promotional_banner_formater($data)
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
        $data = $storage;
        return $data;
    }

    public static function get_mail_status($name)
    {
        return BusinessSetting::where('key', $name)->first()?->value ?? 0;
    }

    public static function text_variable_data_format($value,$user_name=null,$restaurant_name=null,$delivery_man_name=null,$transaction_id=null,$order_id=null,$add_id= null)
    {
        $data = $value;
        if ($value) {
            if($user_name){
                $data =  str_replace("{userName}", $user_name, $data);
            }

            if($restaurant_name){
                $data =  str_replace("{restaurantName}", $restaurant_name, $data);
            }

            if($delivery_man_name){
                $data =  str_replace("{deliveryManName}", $delivery_man_name, $data);
            }

            if($transaction_id){
                $data =  str_replace("{transactionId}", $transaction_id, $data);
            }

            if($order_id){
                $data =  str_replace("{orderId}", $order_id, $data);
            }
            if($add_id){
                $data =  str_replace("{advertisementId}", $add_id, $data);
            }
        }

        return $data;
    }

    public static function get_login_url($type){
        $data=DataSetting::whereIn('key',['restaurant_employee_login_url','restaurant_login_url','admin_employee_login_url','admin_login_url'
        ])->pluck('key','value')->toArray();

        return array_search($type,$data);
    }

    public static function time_date_format($data){
            $time=config('timeformat') ?? 'H:i';
        return  Carbon::parse($data)->locale(app()->getLocale())->translatedFormat('d M Y ' . $time);
    }
    public static function date_format($data){
        return  Carbon::parse($data)->locale(app()->getLocale())->translatedFormat('d M Y');
    }
    public static function time_format($data){
            $time=config('timeformat') ?? 'H:i';
        return  Carbon::parse($data)->locale(app()->getLocale())->translatedFormat($time);
    }


    public static function get_zones_name($zones){
        if(is_array($zones)){
            $data = Zone::whereIn('id',$zones)->pluck('name')->toArray();
        }else{
            $data = Zone::where('id',$zones)->pluck('name')->toArray();
        }
        $data = implode(', ', $data);
        return $data;
    }

    public static function get_restaurant_name($restaurant){
        if(is_array($restaurant)){
            $data = Restaurant::whereIn('id',$restaurant)->pluck('name')->toArray();
        }else{
            $data = Restaurant::where('id',$restaurant)->pluck('name')->toArray();
        }
        $data = implode(', ', $data);
        return $data;
    }

    public static function get_category_name($id){
        $id=Json_decode($id,true);
        $id=data_get($id,'0.id','NA');
        $data= Category::with('translations')->where('id',$id)->first()?->name ?? translate('messages.uncategorize');
        return $data;
    }
    public static function get_sub_category_name($id){
        $id=Json_decode($id,true);
        $id=data_get($id,'1.id','NA');
        return Category::where('id',$id)->first()?->name;
    }


    public static function get_food_variations($variations){
        try{
            $data=[];
            $data2=[];
            foreach((array)json_decode($variations,true) as $key => $choice){
                if(data_get($choice,'values',null)){
                    foreach( data_get($choice,'values',[]) as $k => $v){
                        $data2[$k] =  $v['label'];
                    // if(!next($choice['values'] )) {
                        //     $data2[$k] =  $v['label'].";";
                        // }
                        }
                        $data[$choice['name']] = $data2;
                    }
                }
            return str_ireplace(['\'', '"', '{','}', '[',']', '<', '>', '?'], ' ',json_encode($data));
            } catch (\Exception $ex) {
                info(["line___{$ex->getLine()}",$ex->getMessage()]);
                return 0;
            }

        }

        public static function get_customer_name($id){
            $user = User::where('id',$id)->first();

            return $user->f_name.' '.$user->l_name;
        }
        public static function get_addon_data($id){
            try{
                $data=[];
                $addon= AddOn::whereIn('id',json_decode($id, true))->get(['name','price'])->toArray();
                    foreach($addon as $key => $value){
                        $data[$key]= $value['name'] .' - ' .\App\CentralLogics\Helpers::format_currency($value['price']);
                    }
                return str_ireplace(['\'', '"', '{','}', '[',']', '<', '>', '?'], ' ',json_encode($data, JSON_UNESCAPED_UNICODE));
            } catch (\Exception $ex) {
                info(["line___{$ex->getLine()}",$ex->getMessage()]);
                return 0;
            }
        }
        public static function get_business_data($name)
        {
            $paymentmethod = BusinessSetting::where('key', $name)->first();
            return $paymentmethod?->value;
        }

        public static function add_or_update_translations($request, $key_data,$name_field ,$model_name, $data_id,$data_value ){
            try{
                $model = 'App\\Models\\'.$model_name;
                $default_lang = str_replace('_', '-', app()->getLocale());
                foreach ($request->lang as $index => $key) {
                    if ($default_lang == $key && !($request->{$name_field}[$index])) {
                        if ($key != 'default') {
                            Translation::updateorcreate(
                                [
                                    'translationable_type' =>  $model,
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
        } catch(\Exception $e){
            info(["line___{$e->getLine()}",$e->getMessage()]);
            return false;
        }
    }

    public static function offline_payment_formater($user_data){
        $userInputs = [];

        $user_inputes=  json_decode($user_data->payment_info, true);
        $method_name= $user_inputes['method_name'];
        $method_id= $user_inputes['method_id'];

        foreach ($user_inputes as $key => $value) {
            if(!in_array($key,['method_name','method_id'])){
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

        $result = [
        'input' => $userInputs,
        'data' => $data,
        'method_fields' =>json_decode($user_data->method_fields ,true),
        ];

        return $result;
    }

    public static function getDeliveryFee($restaurant): string
    {
        if(!request()->header('latitude') || !request()->header('longitude')){
            return 'out_of_range';
        }
            $zone = Zone::where('id', $restaurant->zone_id)->whereContains('coordinates', new Point(request()->header('latitude') && request()->header('longitude'), POINT_SRID))->first();
        if(!$zone) {
            return 'out_of_range';
        }
        if(isset($restaurant->distance) && $restaurant->distance > 0){
            $distance = $restaurant->distance / 1000;
            $distance=   round($distance,5);
        }
        elseif( $restaurant->latitude &&  $restaurant->longitude){

        $originCoordinates =[
            $restaurant->latitude,
            $restaurant->longitude
        ];
        $destinationCoordinates =[
            request()->header('latitude') ,
            request()->header('longitude')
        ];
            $distance = self::get_distance($originCoordinates, $destinationCoordinates);
            $distance=   round($distance,5);
        } else {
            return 'out_of_range';
        }

        if($restaurant['self_delivery_system'] ==  1){

            if($restaurant->free_delivery == 1){
                return 'free_delivery';
            }
            if($restaurant->free_delivery_distance_status == 1 &&  $distance <= $restaurant->free_delivery_distance_value){
                return 'free_delivery';
            }

            $per_km_shipping_charge = $restaurant->per_km_shipping_charge ?? 0 ;
            $minimum_shipping_charge = $restaurant->minimum_shipping_charge ?? 0;
            $maximum_shipping_charge = $restaurant->maximum_shipping_charge ?? 0;
            $extra_charges= 0;
            $increased= 0;

        }
        else{

        $businessSettings = BusinessSetting::whereIn('key', [ 'free_delivery_over', 'free_delivery_distance','admin_free_delivery_status', 'admin_free_delivery_option'])->pluck('value', 'key');

        $free_delivery_over = (float) ($businessSettings['free_delivery_over'] ?? 0);
        $free_delivery_distance = (float) ($businessSettings['free_delivery_distance'] ?? 0);
        $admin_free_delivery_status = (int) ($businessSettings['admin_free_delivery_status'] ?? 0);
        $admin_free_delivery_option = $businessSettings['admin_free_delivery_option'] ?? null;


        if ($admin_free_delivery_status == 1) {

            if ($admin_free_delivery_option === 'free_delivery_to_all_store' ||($admin_free_delivery_option === 'free_delivery_by_specific_criteria' && ($free_delivery_distance > 0 && $distance <= $free_delivery_distance)) ) {
                return 'free_delivery';
            }
        }

            $per_km_shipping_charge = $zone->per_km_shipping_charge ?? 0;
            $minimum_shipping_charge = $zone->minimum_shipping_charge ?? 0;
            $maximum_shipping_charge = $zone->maximum_shipping_charge ?? 0;
            $increased= 0;
            if($zone->increased_delivery_fee_status == 1){
                $increased=$zone->increased_delivery_fee ?? 0;
            }
            $data = self::vehicle_extra_charge(distance_data:$distance);
            $extra_charges = (float) (isset($data) ? $data['extra_charge']  : 0);

        }

            $original_delivery_charge = ($distance * $per_km_shipping_charge > $minimum_shipping_charge) ? $distance * $per_km_shipping_charge + $extra_charges  : $minimum_shipping_charge + $extra_charges;
        if($increased > 0  && $original_delivery_charge > 0){
                $increased_fee = ($original_delivery_charge * $increased) / 100;
                $original_delivery_charge = $original_delivery_charge + $increased_fee;
        }
        return (string) $original_delivery_charge ;

    }




    public static function get_distance(array $originCoordinates,array $destinationCoordinates, $unit = 'K'): float
    {
        $lat1 = (float) $originCoordinates[0];
        $lat2 = (float) $destinationCoordinates[0];
        $lon1 = (float) $originCoordinates[1];
        $lon2 = (float) $destinationCoordinates[1];

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }

    public static function onerror_image_helper($data, $src, $error_src ,$path){

        if(isset($data) && strlen($data) >1 && Storage::disk('public')->exists($path.$data)){
            return $src;
        }
        return $error_src;
    }



   public static function getNextOpeningTime($schedule) {
    $currentTime =now()->format('H:i');
    if ($schedule) {
        foreach($schedule as $entry) {
            if ($entry['day'] == now()->format('w')) {
                    if ($currentTime >= $entry['opening_time'] && $currentTime <= $entry['closing_time']) {
                        return $entry['opening_time'];
                    } elseif($currentTime < $entry['opening_time']){
                        return $entry['opening_time'];
                    }
            }
        }
    }
        return 'closed';
    }

    public static function generateDatesForSubscriptionOrders($start_at, $end_at, $scheduleDates,$scheduleTime,$pauseArray,$scheduleType) {
        $start = new DateTime($start_at);
        $end = new DateTime($end_at);
        $interval = new DateInterval('P1D');
        $end->modify('+1 day');
        $period = new DatePeriod($start, $interval, $end);

        $result = [];
        foreach ($period as $date) {
            $skipDate = false;
            foreach ($pauseArray as $pauseStart => $pauseEnd) {
                if ($date >= new DateTime($pauseStart) && $date <= new DateTime($pauseEnd)) {
                    $skipDate = true;
                    break;
                }
            }
            if (!$skipDate && $date->format('Y-m-d') > now()->format('Y-m-d') && (in_array($date->format('j'), $scheduleDates) || in_array($date->format('w'), $scheduleDates) || in_array('daily', $scheduleDates)) ) {
                    foreach ($scheduleTime as $key =>  $time) {
                        if(($date->format('j') == $key && $scheduleType == 'monthly') || ( $date->format('w') == $key && $scheduleType == 'weekly')  || in_array('daily', $scheduleDates)){
                            $result[] = $date->format('Y-m-d') . ' ' . $time;
                        }
                    }
                }
        }
        return $result;
    }


    public static function getCalculatedCashBackAmount($amount,$customer_id){
        $data=[
            'calculated_amount'=> (float) 0,
            'cashback_amount'=>0,
            'cashback_type'=>'',
            'min_purchase'=>0,
            'max_discount'=>0,
            'id'=>0,
        ];

        try {
            $percent_bonus = CashBack::active()
            ->where('cashback_type', 'percentage')
            ->Running()
            ->where('min_purchase', '<=', $amount)
            ->where(function($query) use ($customer_id) {
                $query->whereJsonContains('customer_id', [(string) $customer_id])->orWhereJsonContains('customer_id', ['all']);
            })
                ->when(is_numeric($customer_id), function($q) use ($customer_id){
                $q->where('same_user_limit', '>', function($query) use ($customer_id) {
                    $query->select(DB::raw('COUNT(*)'))
                            ->from('cash_back_histories')
                            ->where('user_id', $customer_id)
                            ->whereColumn('cash_back_id', 'cash_backs.id');
                    });
                })

            ->orderBy('cashback_amount', 'desc')
            ->first();

            $amount_bonus = CashBack::active()->where('cashback_type','amount')
            ->Running()
            ->where(function($query)use($customer_id){
                $query->whereJsonContains('customer_id', [(string) $customer_id])->orWhereJsonContains('customer_id', ['all']);
            })
            ->where('min_purchase','<=',$amount )
            ->when(is_numeric($customer_id), function($q) use ($customer_id){
                $q->where('same_user_limit', '>', function($query) use ($customer_id) {
                    $query->select(DB::raw('COUNT(*)'))
                            ->from('cash_back_histories')
                            ->where('user_id', $customer_id)
                            ->whereColumn('cash_back_id', 'cash_backs.id');
                    });
                })
            ->orderBy('cashback_amount','desc')->first();

            if($percent_bonus && ($amount >=$percent_bonus->min_purchase)){
                $p_bonus = ($amount  * $percent_bonus->cashback_amount)/100;
                $p_bonus = $p_bonus > $percent_bonus->max_discount ? $percent_bonus->max_discount : $p_bonus;
                $p_bonus = round($p_bonus,config('round_up_to_digit'));
            }else{
                $p_bonus = 0;
            }

            if($amount_bonus && ($amount >=$amount_bonus->min_purchase)){
                $a_bonus = $amount_bonus?$amount_bonus->cashback_amount: 0;
                $a_bonus = round($a_bonus,config('round_up_to_digit'));
            }else{
                $a_bonus = 0;
            }

            $cashback_amount = max([$p_bonus,$a_bonus]);

            if($p_bonus ==  $cashback_amount){
                $data=[
                    'calculated_amount'=> (float)$cashback_amount,
                    'cashback_amount'=>$percent_bonus?->cashback_amount ?? 0,
                    'cashback_type'=>$percent_bonus?->cashback_type ?? '',
                    'min_purchase'=>$percent_bonus?->min_purchase ?? 0,
                    'max_discount'=>$percent_bonus?->max_discount ?? 0,
                    'id'=>$percent_bonus?->id,
                ];

            } elseif($a_bonus == $cashback_amount){
                $data=[
                    'calculated_amount'=> (float)$cashback_amount,
                    'cashback_amount'=>$amount_bonus?->cashback_amount ?? 0,
                    'cashback_type'=>$amount_bonus?->cashback_type ?? '',
                    'min_purchase'=>$amount_bonus?->min_purchase ?? 0,
                    'max_discount'=>$amount_bonus?->max_discount ?? 0,
                    'id'=>$amount_bonus?->id,
                ];
            }

            return $data ;
        } catch (\Exception $exception) {
            info([$exception->getFile(),$exception->getLine(),$exception->getMessage()]);
            return $data ;
        }

    }

    public static function getCusromerFirstOrderDiscount($order_count, $user_creation_date,$refby, $price = null){

        $data=[
            'is_valid' => false,
            'discount_amount' => 0,
            'discount_amount_type' => '',
            'validity' => '',
            'calculated_amount' => 0,
        ];
        if($order_count > 0 || !$refby){
            return $data?? [];
        }
        $settings =  array_column(BusinessSetting::whereIn('key',['new_customer_discount_status','new_customer_discount_amount','new_customer_discount_amount_type','new_customer_discount_amount_validity','new_customer_discount_validity_type',])->get()->toArray(), 'value', 'key');

        $validity_value = data_get($settings,'new_customer_discount_amount_validity');
        $validity_unit = data_get($settings,'new_customer_discount_validity_type');

        if($validity_unit == 'day'){
            $validity_end_date = (new DateTime($user_creation_date))->modify("+$validity_value day");

        } elseif($validity_unit == 'month'){
            $validity_end_date = (new DateTime($user_creation_date))->modify("+$validity_value month");

        } elseif($validity_unit == 'year'){
            $validity_end_date = (new DateTime($user_creation_date))->modify("+$validity_value year");
        }
        else{
            $validity_end_date = (new DateTime($user_creation_date))->modify("-1 day");
        }

        $is_valid=false;
        $current_date = new DateTime();
        if($validity_end_date >= $current_date){
        $is_valid=true;
        }



    if($order_count == 0 && $is_valid && data_get($settings,'new_customer_discount_status' ) == 1 && data_get($settings,'new_customer_discount_amount' ) > 0 ){
        $calculated_amount=0;
        if(data_get($settings,'new_customer_discount_amount_type') == 'percentage' && isset($price)){
            $calculated_amount= ($price / 100) * data_get($settings,'new_customer_discount_amount');
        } else{
            $calculated_amount=data_get($settings,'new_customer_discount_amount');
        }

        $data=[
            'is_valid' => $is_valid,
            'discount_amount' => data_get($settings,'new_customer_discount_amount'),
            'discount_amount_type' => data_get($settings,'new_customer_discount_amount_type'),
            'validity' => data_get($settings,'new_customer_discount_amount_validity') .' '. translate(Str::plural((data_get($settings,'new_customer_discount_validity_type') ?? 'day'),data_get($settings,'new_customer_discount_amount_validity'))),
            'calculated_amount' => round($calculated_amount,config('round_up_to_digit')),
        ];
    }

    return $data?? [];
    }




    public static function addonAndVariationStockCheck($product, $quantity=1, $add_on_qtys=1, $variation_options=null,$add_on_ids= null ,$incrementCount = false ,$old_selected_variations=[] ,$old_selected_without_variation = 0,$old_selected_addons=[]){

        if($product?->stock_type && $product?->stock_type !== 'unlimited'){
            $availableMainStock=$product->item_stock + $old_selected_without_variation ;
            if(  $availableMainStock <= 0 || $availableMainStock < $quantity  ){
                return [
                    'out_of_stock' =>$availableMainStock > 0 ? translate('Only') .' '.$availableMainStock . " ". translate('Quantity_is_abailable_for').' '.$product?->name : $product?->name.' ' . translate('is_out_of_stock_!!!') ,
                    'id'=>$product->id,
                'current_stock' =>  $availableMainStock > 0 ?  $availableMainStock : 0,
                ];
            }
            if($product?->stock_type && $incrementCount == true){
                $product->increment('sell_count',$quantity);
            }

            if(is_array($variation_options) && (data_get($variation_options,0) != ''|| data_get($variation_options,0)  != null)) {
                $variation_options= VariationOption::whereIn('id', $variation_options)->get();
                foreach($variation_options as $variation_option){
                        if($variation_option->stock_type !== 'unlimited'){
                            $availableStock=$variation_option->total_stock  - $variation_option->sell_count;
                            if(is_array($old_selected_variations) && data_get($old_selected_variations, $variation_option->id) ){
                                $availableStock= $availableStock + data_get($old_selected_variations, $variation_option->id);
                            }
                            if($availableStock <= 0 || $availableStock < $quantity){
                                return ['out_of_stock' => $availableStock > 0 ? translate('Only') .' '.$availableStock . " ". translate('Quantity_is_abailable_for').' '.$product?->name.' \'s ' . $variation_option->option_name .' ' . translate('Variation_!!!') : $product?->name.' \'s ' . $variation_option->option_name .' ' . translate('Variation_is_out_of_stock_!!!') ,
                                        'id'=>$variation_option->id,
                                        'current_stock' =>  $availableStock > 0 ?  $availableStock : 0,
                                        ];
                            }
                            if($incrementCount == true){
                                $variation_option->increment('sell_count',$quantity);
                            }
                        }
                    }
            }
        }

        if(is_array($add_on_ids) && count($add_on_ids) > 0) {
            return  Helpers::calculate_addon_price(addons: AddOn::whereIn('id',$add_on_ids)->get(), add_on_qtys: $add_on_qtys ,incrementCount:$incrementCount ,old_selected_addons:$old_selected_addons);
        }
        return null;
    }


    public static function decreaseSellCount($order_details){
        foreach ($order_details as $detail) {
            $optionIds=[];
            if($detail->variation != '[]'){
                foreach (json_decode($detail->variation, true) as $value) {
                    foreach (data_get($value,'values' ,[]) as $item) {
                        if(data_get($item, 'option_id', null ) != null){
                            $optionIds[] = data_get($item, 'option_id', null );
                        }
                    }
                }
                VariationOption::whereIn('id', $optionIds)->where('sell_count', '>', 0)->decrement('sell_count' ,$detail->quantity);
            }
            $detail->food()->where('sell_count', '>', 0)->decrement('sell_count' ,$detail->quantity);

            foreach (json_decode($detail->add_ons, true) as $add_ons) {
                if(data_get($add_ons, 'id', null ) != null){
                AddOn::where('id',data_get($add_ons, 'id', null ))->where('sell_count', '>', 0)->decrement('sell_count' ,data_get($add_ons, 'quantity', 1 ));
                }
            }
        }
        return true;
    }


    public static function notificationDataSetup(){
        $data=self::getAdminNotificationSetupData();
        $data = NotificationSetting::upsert($data,['key','type'],['title','mail_status','sms_status','push_notification_status','sub_title']);
        return true;
    }

    public static function restaurantNotificationDataSetup($id){
        $data=self::getRestaurantNotificationSetupData($id);
        $data = RestaurantNotificationSetting::upsert($data,['key','restaurant_id'],['title','mail_status','sms_status','push_notification_status','sub_title']);
        return true;
    }


    public static function getNotificationStatusData($user_type,$key){
        $data= NotificationSetting::where('type',$user_type)->where('key',$key)->select(['mail_status','push_notification_status','sms_status'])->first();
        return $data ?? null ;
    }



    public static function getRestaurantNotificationStatusData($restaurant_id,$key){
        $data= RestaurantNotificationSetting::where('restaurant_id',$restaurant_id)->where('key',$key)->select(['mail_status','push_notification_status','sms_status'])->first();
        if(!$data){
            self::addNewRestaurantNotificationSetupData($restaurant_id);
            $data= RestaurantNotificationSetting::where('restaurant_id',$restaurant_id)->where('key',$key)->select(['mail_status','push_notification_status','sms_status'])->first();
            if (!$data) {
                self::restaurantNotificationDataSetup($restaurant_id);
                $data= RestaurantNotificationSetting::where('restaurant_id',$restaurant_id)->where('key',$key)->select(['mail_status','push_notification_status','sms_status'])->first();
            }
        }
        return $data ?? null ;
    }
    public static function addNewAdminNotificationSetupDataSetup(){
        self::addNewAdminNotificationSetupData();
    return true;
    }

    public static function getActivePaymentGateways(){

        if (!Schema::hasTable('addon_settings')) {
            return [];
        }
        $digital_payment=\App\CentralLogics\Helpers::get_business_settings('digital_payment');

        if($digital_payment && $digital_payment['status']==0){
            return [];
        }

        $published_status = 0;
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }


        if($published_status == 1){
            $methods = DB::table('addon_settings')->where('is_active',1)->where('settings_type', 'payment_config')->get();
            $env = env('APP_ENV') == 'live' ? 'live' : 'test';
            $credentials = $env . '_values';

        } else{
            $methods = DB::table('addon_settings')->where('is_active',1)->whereIn('settings_type', ['payment_config'])->whereIn('key_name', ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paytabs','paystack','paymob_accept','paytm','flutterwave','liqpay','bkash','mercadopago'])->get();
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
                        'gateway_image_full_url' => Helpers::get_full_url('payment_modules/gateway_image',$additional_data?->gateway_image,$additional_data?->storage ?? 'public')
                    ];
                }
            }
            return $data;

    }



    public static function checkCurrency($data , $type= null){

        $digital_payment=self::get_business_settings('digital_payment');

        if($digital_payment && $digital_payment['status']==1){
            if($type === null){
                if(is_array(self::getActivePaymentGateways())){
                    foreach(self::getActivePaymentGateways() as $payment_gateway){

                        if(!empty(self::getPaymentGatewaySupportedCurrencies($payment_gateway['gateway'])) && !array_key_exists($data,self::getPaymentGatewaySupportedCurrencies($payment_gateway['gateway']))    ){
                            return  $payment_gateway['gateway'];
                        }
                    }
                }
            }
            elseif($type == 'payment_gateway'){
                $currency=  BusinessSetting::where('key','currency')->first()?->value;
                    if(!empty(self::getPaymentGatewaySupportedCurrencies($data)) && !array_key_exists($currency,self::getPaymentGatewaySupportedCurrencies($data))    ){
                        return  $data;
                    }
            }
        }

        return true;
        }

    public static function updateStorageTable($dataType, $dataId, $image)
    {
        $value = Helpers::getDisk();
        DB::table('storages')->updateOrInsert([
            'data_type' => $dataType,
            'data_id' => $dataId,
            'key' => 'image',
        ], [
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    public static function add_fund_push_notification($user_id, $amount = ''){
            $customer_push_notification_status=self::getNotificationStatusData('customer','customer_add_fund_to_wallet');
            $user= User::where('id',$user_id)->first();
            if ($customer_push_notification_status?->push_notification_status  == 'active' && $user?->cm_firebase_token) {
                $data = [
                    'title' => translate('messages.Fund_added'),
                    'description' => translate('messages.Fund_added_to_your_wallet'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'add_fund',
                    'order_status' =>'',
                    'amount' => $amount,
                ];
                self::send_push_notif_to_device($user?->cm_firebase_token, $data);

                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            return true;
        }

        public static function  getImageForExport($imagePath)
    {
        $temporaryImage = self::getTemporaryImageForExport($imagePath);
        $pngImage = imagecreatetruecolor(imagesx($temporaryImage), imagesy($temporaryImage));
        imagealphablending($pngImage, false);
        imagesavealpha($pngImage, true);
        imagecopy($pngImage, $temporaryImage, 0, 0, 0, 0, imagesx($temporaryImage), imagesy($temporaryImage));
        return $pngImage;
    }
    public static function  getTemporaryImageForExport($imagePath)
    {
        try {
            $imageData = file_get_contents($imagePath);
            return imagecreatefromstring($imageData);
            } catch (\Throwable $th) {
            $imageData = file_get_contents(dynamicAsset('public/assets/admin/img/100x100/no-image-found.png'));
            return imagecreatefromstring($imageData);

        }
    }
    public static function  CheckOldSubscriptionSettings()
    {
        if(BusinessSetting::where(['key' => 'free_trial_period'])->exists()){
            $old_trial_data = BusinessSetting::where(['key' => 'free_trial_period'])->first();
            $data = json_decode($old_trial_data?->value,true);
                if(isset($data['status']) && $data['status'] == 1){
                    $type= data_get($data,'type');

                        if($type == 'year'){
                            $free_trial_period = data_get($data,'data') * 365;
                        } else if($type == 'month'){
                            $free_trial_period = data_get($data,'data') * 30;
                        } else{
                            $free_trial_period = data_get($data,'data',1);
                        }

                    $key=['subscription_free_trial_days','subscription_free_trial_type','subscription_free_trial_status'];
                    foreach ($key as $value) {
                            $status = BusinessSetting::firstOrNew([
                                'key' => $value
                            ]);
                            if( $value == 'subscription_free_trial_days'){
                                $status->value = $free_trial_period;
                            } elseif($value == 'subscription_free_trial_type'){
                                $status->value =$type ?? 'day';
                            } elseif($value == 'subscription_free_trial_status'){
                                $status->value =$data['status'];
                            }
                            $status->save();
                    }
                }

                $old_trial_data?->delete();
            }
    }

    public static function calculateSubscriptionRefundAmount($restaurant,$return_data=null){
        return SubscriptionService::calculateSubscriptionRefundAmount($restaurant,$return_data);
    }

    public static function subscriptionConditionsCheck($restaurant_id ,$package_id,){
        return SubscriptionService::subscriptionConditionsCheck($restaurant_id,$package_id);
    }

    public static function subscriptionPayment($restaurant_id,$package_id,$payment_gateway,$url,$pending_bill=0,$type='payment',$payment_platform='web'){
        $restaurant = Restaurant::where('id',$restaurant_id)->first();
        $package = SubscriptionPackage::where('id',$package_id)->first();
        $type == null ? 'payment' :$type ;

        $payer = new Payer(
            $restaurant->name ,
            $restaurant->email,
            $restaurant->phone,
            ''
        );
        $restaurant_logo= BusinessSetting::where(['key' => 'logo'])->first();
        $additional_data = [
            'business_name' => BusinessSetting::where(['key'=>'business_name'])->first()?->value,
            'business_logo' => \App\CentralLogics\Helpers::get_full_url('business',$restaurant_logo?->value,$restaurant_logo?->storage[0]?->value ?? 'public')
        ];
        $payment_info = new PaymentInfo(
            success_hook: 'sub_success',
            failure_hook: 'sub_fail',
            currency_code: Helpers::currency_code(),
            payment_method: $payment_gateway,
            payment_platform: $payment_platform,
            payer_id: $restaurant->id,
            receiver_id:  $package->id,
            additional_data: $additional_data,
            payment_amount: $package->price + $pending_bill,
            external_redirect_link: $url,
            attribute: 'restaurant_subscription_'.$type,
            attribute_id: $package->id,
        );
        $receiver_info = new Receiver('Admin','example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public static function getSettingsDataFromConfig($settings,$relations=[])
    {
        try {
            if (!config($settings.'_conf')){
                $data = BusinessSetting::where('key',$settings)->with($relations)->first();
                Config::set($settings.'_conf', $data);
            }
            else{
                $data = config($settings.'_conf');
            }
            return $data;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function businessUpdateOrInsert($key, $value)
    {
        $businessSetting = BusinessSetting::where(['key' => $key['key']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $businessSetting = new BusinessSetting();
            $businessSetting->key = $key['key'];
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        }
    }

    public static function dataUpdateOrInsert($key, $value)
    {
        $businessSetting = DataSetting::where(['key' => $key['key'],'type' => $key['type']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $businessSetting = new DataSetting();
            $businessSetting->key = $key['key'];
            $businessSetting->type = $key['type'];
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        }
    }


    public static function checkAdminDiscount($price, $discount, $max_discount, $min_purchase, $item_wise_price = null)
    {
        if ($price > 0 &&  $discount > 0) {
            $discount = ($price  * $discount) / 100;
            $discount = $discount > $max_discount ? $max_discount : $discount;
            $discount = $price >= $min_purchase ? $discount : 0;
        }

        if ($discount > 0 && $item_wise_price > 0) {
            $discount = ($item_wise_price / $price) * $discount;
        }

        return $discount ?? 0;
    }

    public static function getFinalCalculatedTax($details_data, $additionalCharges, $totalDiscount, $price, $storeId, $storeData = true)
    {
        $addonIds = [];
        $products=[];
        $tempList = [];
        $taxData = [];

        $productDiscountTotal = 0;
        $addonDiscountTotal = 0;
        $totalAfterOwnDiscounts = 0;
        if (addon_published_status('TaxModule')) {

            foreach ($details_data as $item) {
                $item_id = $item['food_id'] ?? data_get($item,'item_campaign_id');
                $itemWiseDiscount = $item['discount_type'] === 'discount_on_product'  ? $item['discount_on_food'] * $item['quantity'] : $item['discount_on_food'];
                $productDiscountTotal += $itemWiseDiscount;

                $itemTotal = $item['price'] * $item['quantity'];
                $itemFinal = $itemTotal - $itemWiseDiscount;

                $tempList[] = [
                    'type' => 'product',
                    'id' => $item_id,
                    'original_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'category_id' => $item['category_id'],
                    'discount' => $item['discount_on_food'],
                    'discount_type' => $item['discount_type'],
                    'base_final' => $itemFinal,
                    'is_campaign_item' => data_get($item,'item_campaign_id') ? true : false,
                ];

                $totalAfterOwnDiscounts += $itemFinal;


                $addons = json_decode($item['add_ons'], true) ?? [];
                $addonDiscount = $item['addon_discount'] ?? 0;
                $addonTotalPrice = $item['total_add_on_price'] ?? 1;

                $addonTotalPrice= max($addonTotalPrice,1);
                $addonDiscountTotal += $addonDiscount;

                foreach ($addons as $addon) {
                    $addonPrice = $addon['price'] * $addon['quantity'];
                    $discountPart = $addonDiscount * ($addonPrice / $addonTotalPrice);
                    $addonFinal = $addonPrice - $discountPart;

                    $tempList[] = [
                        'type' => 'addon',
                        'addon_id' => $addon['id'],
                        'item_id' => $item_id,
                        'quantity' => $addon['quantity'],
                        'category_id' => $addon['category_id'] ?? null,
                        'original_price' => $addon['price'],
                        'base_final' => $addonFinal,
                        'total_addon_addon_price' => $addonTotalPrice,
                        'total_addon_discount' => $addonDiscount,
                    ];

                    $totalAfterOwnDiscounts += $addonFinal;
                }

            }

            $otherDiscounts = $totalDiscount - ($productDiscountTotal + $addonDiscountTotal);

            foreach ($tempList as $entry) {
                $share = ($entry['base_final'] / $totalAfterOwnDiscounts) * $otherDiscounts;
                $finalPrice = $entry['base_final'] - $share;

                if ($entry['type'] === 'product') {
                    $products[] = [
                        'id' => $entry['id'],
                        'original_price' => $entry['original_price'],
                        'quantity' => $entry['quantity'],
                        'category_id' => $entry['category_id'],
                        'discount' => $entry['discount'],
                        'discount_type' => $entry['discount_type'],
                        'after_discount_final_price' => $finalPrice,
                        'is_campaign_item' => $entry['is_campaign_item'],
                    ];
                } else {
                    $addonIds[] = [
                        'addon_id' => $entry['addon_id'],
                        'item_id' => $entry['item_id'],
                        'quantity' => $entry['quantity'],
                        'category_id' => $entry['category_id'],
                        'original_price' => $entry['original_price'],
                        'after_discount_final_price' => $finalPrice,
                        'total_addon_addon_price' => $entry['total_addon_addon_price'],
                        'total_addon_discount' => $entry['total_addon_discount'],
                    ];
                }
            }

            $taxData =  \Modules\TaxModule\Services\CalculateTaxService::getCalculatedTax(
                amount: $price,
                productIds: $products,
                taxPayer: 'vendor',
                storeData: $storeData,
                additionalCharges: $additionalCharges,
                addonIds: $addonIds,
                orderId: null,
                storeId: $storeId
            );

            $tax_amount = $taxData['totalTaxamount'];
            $tax_included = $taxData['include'];
            $tax_status = $tax_included ?  'included' : 'excluded';

            foreach ($taxData['productWiseData'] ?? [] as $key => $item) {
                $taxMap[$key] = $item;
            }
        }

        return [
            'tax_amount' => $tax_amount ?? 0,
            'tax_included' => $tax_included ?? null,
            'tax_status' => $tax_status ?? 'excluded',
            'taxMap' => $taxMap ?? [],
            'taxType'=> data_get($taxData,'taxType'),
            'taxData' => $taxData ?? [],
        ];
    }

        public static function getTaxSystemType($getTaxVatList = true,$tax_payer='vendor'){
        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)
                ->where('tax_payer', $tax_payer)->where('is_default', 1)->first();
            if(!$SystemTaxVat){
                 return [ 'productWiseTax' => false ,'categoryWiseTax'=> false,  'taxVats' =>  []];
            }
            if($getTaxVatList){
                $taxVats =  \Modules\TaxModule\Entities\Tax::where('is_active', 1)->where('is_default', 1)->get(['id', 'name', 'tax_rate']);
            }

            if ($SystemTaxVat?->tax_type == 'product_wise') {
                $productWiseTax = true;
            } elseif ($SystemTaxVat?->tax_type == 'category_wise') {
                $categoryWiseTax = true;
            }
        }
        return [ 'productWiseTax' => $productWiseTax?? false ,'categoryWiseTax'=> $categoryWiseTax?? false,  'taxVats' => $taxVats ?? []];
    }

    public static function deleteCacheData($prefix)
    {
        $cacheKeys = DB::table('cache')
            ->where('key', 'like', "%" . $prefix . "%")
            ->pluck('key');
        $appName = env('APP_NAME').'_cache';
        $remove_prefix = strtolower(str_replace('=', '', $appName));
        $sanitizedKeys = $cacheKeys->map(function ($key) use ($remove_prefix) {
            $key = str_replace($remove_prefix, '', $key);
            return $key;
        });
        foreach ($sanitizedKeys as $key) {
            Cache::forget($key);
        }
    }

}
