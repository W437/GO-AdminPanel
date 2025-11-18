<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Restaurant;
use App\Models\Translation;
use Illuminate\Support\Str;
use App\Models\ItemCampaign;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Exports\FoodCampaignExport;
use App\Exports\BasicCampaignExport;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\FoodCampaignOrderListExport;

class CampaignController extends Controller
{
    function index($type)
    {
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        return view('admin-views.campaign.'.$type.'.index', compact('productWiseTax', 'taxVats'));
    }

    function list($type)
    {
        $key = explode(' ', request()?->search);
        if($type=='basic')
        {
            $campaigns=Campaign::
            when(isset($key), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(config('default_pagination'));
        }
        else{
            $campaigns=ItemCampaign::
            when(isset($key), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(config('default_pagination'));
        }
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];

        return view('admin-views.campaign.'.$type.'.list', compact('campaigns','productWiseTax', 'taxVats'));
    }

    public function storeBasic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:campaigns|max:191',
            'description'=>'max:1000',
            'image' => 'required|max:2048',
            'title.0' => 'required',
            'description.0' => 'required',
            'end_time' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->start_date == $request->end_date && strtotime($value) <= strtotime($request->start_time)) {
                        $fail('The end time must be after the start time if the start and end dates are the same.');
                    }
                },
            ],
        ],[
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new Campaign;
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = Helpers::upload(dir: 'campaign/',format: 'png', image: $request->file('image'));
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->save();

        Helpers::add_or_update_translations(request: $request, key_data: 'title', name_field: 'title', model_name: 'Campaign', data_id: $campaign->id, data_value: $campaign->title);
        Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'Campaign', data_id: $campaign->id, data_value: $campaign->description);

        // Clear cache to show changes immediately
        Cache::flush();

        return response()->json([], 200);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'description'=>'max:1000',
            'image' => 'nullable|max:2048',
            'title.0' => 'required',
            'description.0' => 'required',
            'end_time' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->start_date == $request->end_date && strtotime($value) <= strtotime($request->start_time)) {
                        $fail('The end time must be after the start time if the start and end dates are the same.');
                    }
                },
            ],
        ],[
            'title.0.required'=>translate('default_title_is_required'),
            'description.0.required'=>translate('default_description_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)];
        $campaign->image = $request->has('image') ? Helpers::update(dir:'campaign/',old_image: $campaign->image, format:'png', image:$request->file('image')) : $campaign->image;;
        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->save();

        Helpers::add_or_update_translations(request: $request, key_data: 'title', name_field: 'title', model_name: 'Campaign', data_id: $campaign->id, data_value: $campaign->title);
        Helpers::add_or_update_translations(request: $request, key_data: 'description', name_field: 'description', model_name: 'Campaign', data_id: $campaign->id, data_value: $campaign->description);

        // Clear cache to show changes immediately
        Cache::flush();

        return response()->json([], 200);
    }

    public function storeItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191|unique:item_campaigns',
            'image' => 'nullable|max:2048',
            'zone_id' => 'required|exists:zones,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'food_ids' => 'required|array|min:1',
            'food_ids.*' => 'exists:food,id',
            'description' => 'nullable|max:1000',
            'title.0' => 'required',
        ], [
            'zone_id.required' => translate('messages.zone_is_required'),
            'zone_id.exists' => translate('messages.zone_not_found'),
            'food_ids.required' => translate('messages.please_select_at_least_one_food_item'),
            'food_ids.min' => translate('messages.please_select_at_least_one_food_item'),
            'title.0.required' => translate('default_title_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        // Verify all selected food items belong to restaurants in the selected zone
        $invalidFoods = \App\Models\Food::whereIn('id', $request->food_ids)
            ->whereHas('restaurant', function($q) use ($request) {
                $q->where('zone_id', '!=', $request->zone_id);
            })
            ->count();

        if ($invalidFoods > 0) {
            $validator->getMessageBag()->add('food_ids', translate('messages.some_food_items_dont_belong_to_selected_zone'));
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign = new ItemCampaign;

        $campaign->admin_id = auth('admin')->id();
        $campaign->zone_id = $request->zone_id;
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)] ?? null;

        if ($request->hasFile('image')) {
            $campaign->image = Helpers::upload(dir: 'campaign/', format: 'png', image: $request->file('image'));
        }

        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;
        $campaign->status = 1;

        $campaign->save();

        // Attach selected food items to campaign
        $campaign->foods()->attach($request->food_ids);

        // Handle translations
        Helpers::add_or_update_translations(
            request: $request,
            key_data: 'title',
            name_field: 'title',
            model_name: 'ItemCampaign',
            data_id: $campaign->id,
            data_value: $campaign->title
        );

        if ($request->description) {
            Helpers::add_or_update_translations(
                request: $request,
                key_data: 'description',
                name_field: 'description',
                model_name: 'ItemCampaign',
                data_id: $campaign->id,
                data_value: $campaign->description
            );
        }

        // Clear cache to show changes immediately
        Cache::flush();

        return response()->json(['message' => translate('messages.campaign_created_successfully')], 200);
    }

    public function updateItem(ItemCampaign $campaign, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:item_campaigns,title,' . $campaign->id,
            'zone_id' => 'required|exists:zones,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'food_ids' => 'required|array|min:1',
            'food_ids.*' => 'exists:food,id',
            'image' => 'nullable|max:2048',
            'description' => 'nullable|max:1000',
            'title.0' => 'required',
        ], [
            'zone_id.required' => translate('messages.zone_is_required'),
            'zone_id.exists' => translate('messages.zone_not_found'),
            'food_ids.required' => translate('messages.please_select_at_least_one_food_item'),
            'food_ids.min' => translate('messages.please_select_at_least_one_food_item'),
            'title.0.required' => translate('default_title_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        // Verify all selected food items belong to restaurants in the selected zone
        $invalidFoods = \App\Models\Food::whereIn('id', $request->food_ids)
            ->whereHas('restaurant', function($q) use ($request) {
                $q->where('zone_id', '!=', $request->zone_id);
            })
            ->count();

        if ($invalidFoods > 0) {
            $validator->getMessageBag()->add('food_ids', translate('messages.some_food_items_dont_belong_to_selected_zone'));
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $campaign->zone_id = $request->zone_id;
        $campaign->title = $request->title[array_search('default', $request->lang)];
        $campaign->description = $request->description[array_search('default', $request->lang)] ?? null;

        if ($request->hasFile('image')) {
            $campaign->image = Helpers::update(dir: 'campaign/', old_image: $campaign->image, format: 'png', image: $request->file('image'));
        }

        $campaign->start_date = $request->start_date;
        $campaign->end_date = $request->end_date;
        $campaign->start_time = $request->start_time;
        $campaign->end_time = $request->end_time;

        $campaign->save();

        // Sync food items (add new, remove old)
        $campaign->foods()->sync($request->food_ids);

        // Handle translations
        Helpers::add_or_update_translations(
            request: $request,
            key_data: 'title',
            name_field: 'title',
            model_name: 'ItemCampaign',
            data_id: $campaign->id,
            data_value: $campaign->title
        );

        if ($request->description) {
            Helpers::add_or_update_translations(
                request: $request,
                key_data: 'description',
                name_field: 'description',
                model_name: 'ItemCampaign',
                data_id: $campaign->id,
                data_value: $campaign->description
            );
        }

        // Clear cache to show changes immediately
        Cache::flush();

        return response()->json(['message' => translate('messages.campaign_updated_successfully')], 200);
    }

    public function getZoneFoods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zone_id' => 'required|exists:zones,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        // Get all active food items from restaurants in the selected zone
        $foods = \App\Models\Food::with(['restaurant:id,name'])
            ->whereHas('restaurant', function($q) use ($request) {
                $q->where('zone_id', $request->zone_id)
                  ->where('status', 1); // Only active restaurants
            })
            ->where('status', 1) // Only active food items
            ->select('id', 'name', 'price', 'restaurant_id', 'image')
            ->orderBy('restaurant_id')
            ->orderBy('name')
            ->get()
            ->map(function($food) {
                return [
                    'id' => $food->id,
                    'name' => $food->name,
                    'price' => $food->price,
                    'restaurant_name' => $food->restaurant->name ?? 'Unknown',
                    'restaurant_id' => $food->restaurant_id,
                    'display_text' => $food->name . ' - ' . ($food->restaurant->name ?? 'Unknown') . ' ($' . number_format($food->price, 2) . ')',
                ];
            });

        return response()->json($foods, 200);
    }

    public function edit($type, $campaign)
    {
        if($type=='basic')
        {
            $campaign = Campaign::withoutGlobalScope('translate')->findOrFail($campaign);
            $taxVats=[];
            $productWiseTax=false;
            $taxVatIds=[];
        }
        else
        {
            $campaign = ItemCampaign::withoutGlobalScope('translate')->findOrFail($campaign);
            $taxData = Helpers::getTaxSystemType();
            $productWiseTax = $taxData['productWiseTax'];
            $taxVatIds = $productWiseTax ? $campaign?->taxVats()->pluck('tax_id')->toArray() : [];
            $taxVats = $taxData['taxVats'];
        }


        return view('admin-views.campaign.'.$type.'.edit', compact('campaign','taxVats', 'productWiseTax', 'taxVatIds'));
    }

    public function view(Request $request ,$type, $campaign)
    {
        $key = explode(' ', $request['search']);
        $productWiseTax=false;
        if($type=='basic')
        {
            $campaign = Campaign::findOrFail($campaign);

            $restaurants = $campaign->restaurants()->with(['vendor','zone'])
            ->when(isset($key) ,function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                    // ->orWhere('email', 'like', "%{$value}%");
                }
            })
            ->paginate(config('default_pagination'));

            $restaurant_ids = [];
            foreach($campaign?->restaurants as $restaurant)
            {
                $restaurant_ids[] = $restaurant->id;
            }
            return view('admin-views.campaign.basic.view', compact('campaign', 'restaurants', 'restaurant_ids'));
        }
        else
        {
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
            $campaign = ItemCampaign::with($productWiseTax ? ['taxVats.tax','restaurant'] : ['restaurant'])->findOrFail($campaign);

            $orders = $campaign->orderdetails()->with(['order','order.customer','order.restaurant'])

            ->when(isset($key) ,function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('order_id', 'like', "%{$value}%");
                }
            })
            ->paginate(config('default_pagination'));

        }
        return view('admin-views.campaign.item.view', compact('campaign','orders','productWiseTax'));

    }

    public function status($type, $id, $status)
    {
        if($type=='item')
        {
            $campaign = ItemCampaign::findOrFail($id);
        }
        else{
            $campaign = Campaign::findOrFail($id);
        }
        $campaign->status = $status;
        $campaign->save();

        // Clear cache to show changes immediately
        Cache::flush();

        Toastr::success(translate('messages.campaign_status_updated'));
        return back();
    }

    public function delete(Campaign $campaign)
    {
        Helpers::check_and_delete('campaign/' , $campaign->image);
        $campaign?->translations()?->delete();
        $campaign->delete();

        // Clear cache to show changes immediately
        Cache::flush();

        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }
    public function delete_item(ItemCampaign $campaign)
    {
        Helpers::check_and_delete('campaign/' , $campaign->image);
        $campaign?->translations()?->delete();
        $campaign?->taxVats()->delete();
        $campaign->delete();

        // Clear cache to show changes immediately
        Cache::flush();

        Toastr::success(translate('messages.campaign_deleted_successfully'));
        return back();
    }

    public function remove_restaurant(Campaign $campaign, $restaurant)
    {
        $campaign?->restaurants()?->detach($restaurant);
        $campaign->save();

        // Clear cache to show changes immediately
        Cache::flush();

        Toastr::success(translate('messages.restaurant_remove_from_campaign'));
        return back();
    }
    public function addrestaurant(Request $request, Campaign $campaign)
    {
        $campaign?->restaurants()?->attach($request->restaurant_id,['campaign_status' => 'confirmed']);
        $campaign->save();

        // Clear cache to show changes immediately
        Cache::flush();

        Toastr::success(translate('messages.restaurant_added_to_campaign'));
        return back();
    }

    public function restaurant_confirmation($campaign,$restaurant_id,$status)
    {
        $campaign = Campaign::findOrFail($campaign);
        $campaign?->restaurants()?->updateExistingPivot($restaurant_id,['campaign_status' => $status]);
        $campaign->save();
        try
        {
            $restaurant=Restaurant::find($restaurant_id);

            $reataurant_push_notification_status= null ;
            $reataurant_push_notification_title= '' ;
            $reataurant_push_notification_description= '' ;

            if($status == 'rejected'){
                $reataurant_push_notification_title= translate('Campaign_Request_Rejected') ;
                $reataurant_push_notification_description= translate('Campaign_Request_Has_Been_Rejected_By_Admin') ;
                $push_notification_status=Helpers::getNotificationStatusData('restaurant','restaurant_campaign_join_rejaction');
                $reataurant_push_notification_status=Helpers::getRestaurantNotificationStatusData($restaurant?->id,'restaurant_campaign_join_rejaction');

                }

                elseif($status == 'confirmed'){
                    $reataurant_push_notification_description= translate('Campaign_Request_Has_Been_Approved_By_Admin') ;
                    $reataurant_push_notification_title= translate('Campaign_Request_Approved') ;
                $push_notification_status=Helpers::getNotificationStatusData('restaurant','restaurant_campaign_join_approval');
                $reataurant_push_notification_status=Helpers::getRestaurantNotificationStatusData($restaurant?->id,'restaurant_campaign_join_approval');

            }



            if( $push_notification_status?->push_notification_status  == 'active' && $reataurant_push_notification_status?->push_notification_status  == 'active' && $restaurant?->vendor?->firebase_token ){

                $data = [
                    'title' => $reataurant_push_notification_title,
                    'description' => $reataurant_push_notification_description,
                    'order_id' => '',
                    'image' => '',
                    'type' => 'campaign',
                    'data_id'=> $campaign->id,
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($restaurant->vendor->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $restaurant->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }



            $notification_status= Helpers::getNotificationStatusData('restaurant','restaurant_campaign_join_rejaction');
            $restaurant_notification_status= Helpers::getRestaurantNotificationStatusData($restaurant->id,'restaurant_campaign_join_rejaction');
            if( $notification_status?->mail_status == 'active' && $restaurant_notification_status?->mail_status == 'active' && config('mail.status') && Helpers::get_mail_status('campaign_deny_mail_status_restaurant') == '1' && $status == 'rejected') {
                Mail::to($restaurant->vendor->email)->send(new \App\Mail\VendorCampaignRequestMail($restaurant->name,'denied'));
                }
                $notification_status= null ;
            $notification_status= Helpers::getNotificationStatusData('restaurant','restaurant_campaign_join_approval');
            $restaurant_notification_status= Helpers::getRestaurantNotificationStatusData($restaurant->id,'restaurant_campaign_join_approval');
            if(  $notification_status?->mail_status == 'active' && $restaurant_notification_status?->mail_status == 'active' && config('mail.status') && Helpers::get_mail_status('campaign_approve_mail_status_restaurant') == '1' && $status == 'confirmed') {
                Mail::to($restaurant->vendor->email)->send(new \App\Mail\VendorCampaignRequestMail($restaurant->name,'approved'));
            }
        }
        catch(\Exception $e)
        {
            info($e->getMessage());
        }
        // Clear cache to show changes immediately
        Cache::flush();

        if($status=='rejected' ){

            Toastr::success(translate('messages.campaign_join_request_rejected'));
        }
        else{

            Toastr::success(translate('messages.restaurant_added_to_campaign'));
        }
        return back();
    }

    public function basic_campaign_export(Request $request){
        try{
            $key = explode(' ', $request['search']);
            $campaigns=Campaign::
            when(isset($key), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();
            if($request->type == 'csv'){
                return Excel::download(new BasicCampaignExport($campaigns,$request['search']), 'Campaign.csv');
            }
            return Excel::download(new BasicCampaignExport($campaigns,$request['search']), 'Campaign.xlsx');
        }
            catch(\Exception $e)
        {
            Toastr::error("line___{$e->getLine()}",$e->getMessage());
            info(["line___{$e->getLine()}",$e->getMessage()]);
            return back();
        }

    }

    public function item_campaign_export(Request $request){
        try{
            $key = explode(' ', $request['search']);
            $campaigns=ItemCampaign::with('restaurant:id,name')->
            when(isset($key), function ($q) use ($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('title', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();

            if($request->type == 'csv'){
                return Excel::download(new FoodCampaignExport($campaigns,$request['search']), 'FoodCampaign.csv');
            }
            return Excel::download(new FoodCampaignExport($campaigns,$request['search']), 'FoodCampaign.xlsx');
            }
                catch(\Exception $e)
            {
                Toastr::error("line___{$e->getLine()}",$e->getMessage());
                info(["line___{$e->getLine()}",$e->getMessage()]);
                return back();
            }
    }


    public function food_campaign_list_export(Request $request){
        try{
        $key = explode(' ', $request['search']);
        $campaign = ItemCampaign::with(['restaurant'])->findOrFail($request->campaign_id);

        $orders = $campaign->orderdetails()->with(['order','order.customer','order.restaurant'])
        ->when(isset($key) ,function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('order_id', 'like', "%{$value}%");
            }
        })
        ->latest()->get();
        $data=[
            'data' => $orders,
            'search' => $request['search'],
            'campaign' => $campaign,
        ];

        if($request->type == 'csv'){
            return Excel::download(new FoodCampaignOrderListExport($data), 'FoodCampaignOrderList.csv');
        }
        return Excel::download(new FoodCampaignOrderListExport($data), 'FoodCampaignOrderList.xlsx');
        }
                catch(\Exception $e)
            {
                Toastr::error("line___{$e->getLine()}",$e->getMessage());
                info(["line___{$e->getLine()}",$e->getMessage()]);
                return back();
            }
        }
}
