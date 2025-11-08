<?php

namespace App\Http\Controllers\Vendor;

use App\Exports\FoodListExport;
use App\Models\Allergy;
use App\Models\Nutrition;
use App\Models\Restaurant;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Food;
use App\Models\Review;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\VariationOption;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\FoodSeoData;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
    public function index()
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $categories = Category::where(['position' => 0])->get();
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        return view('vendor-views.product.index', compact('categories','productWiseTax','taxVats'));
    }

    public function store(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            return response()->json([
                    'errors'=>[
                        ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                    ]
                ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'array',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'image' => 'nullable|max:2048',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'description.*' => 'max:65535',
            'discount' => 'required|numeric|min:0',
        ], [
            'name.0.required' => translate('messages.item_name_required'),
            'category_id.required' => translate('messages.category_required'),
            'veg.required'=>translate('messages.item_type_is_required'),
            'description.*.max' => translate('messages.Description must be in 65535 char limit'),
        ]);


        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }


        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids,$tag->id);
            }
        }

        $nutrition_ids = [];
        if ($request->nutritions != null) {
            $nutritions = $request->nutritions;
        }
        if (isset($nutritions)) {
            foreach ($nutritions as $key => $value) {
                $nutrition = Nutrition::firstOrNew(
                    ['nutrition' => $value]
                );
                $nutrition->save();
                array_push($nutrition_ids, $nutrition->id);
            }
        }
        $allergy_ids = [];
        if ($request->allergies != null) {
            $allergies = $request->allergies;
        }
        if (isset($allergies)) {
            foreach ($allergies as $key => $value) {
                $allergy = Allergy::firstOrNew(
                    ['allergy' => $value]
                );
                $allergy->save();
                array_push($allergy_ids, $allergy->id);
            }
        }

        $food = new Food;
        $food->name = $request->name[array_search('default', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }
        $food->category_id = $request->sub_category_id ?? $request->category_id;
        $food->category_ids = json_encode($category);
        $food->description = $request->description[array_search('default', $request->lang)];

        $food->choice_options = json_encode([]);


        $food->variations = json_encode([]);
        $food->price = $request->price;
        $food->veg = $request->veg;
        $food->image = Helpers::upload(dir:'product/', format:'png', image:$request->file('image'));
        $food->available_time_starts = $request->available_time_starts;
        $food->available_time_ends = $request->available_time_ends;
        $food->discount =  $request->discount ?? 0;
        $food->discount_type = $request->discount_type;
        $food->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $food->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $food->restaurant_id = Helpers::get_restaurant_id();
        $food->maximum_cart_quantity = $request->maximum_cart_quantity;
        $food->is_halal =  $request->is_halal ?? 0;
        $food->item_stock = $request?->item_stock ?? 0;
        $food->stock_type = $request->stock_type;

        $restaurant= Helpers::get_restaurant_data();
        if ( $restaurant->restaurant_model == 'subscription' ) {
            $rest_sub = $restaurant?->restaurant_sub;
            if (isset($rest_sub)) {
                if ($rest_sub->max_product != "unlimited" && $rest_sub->max_product > 0 ) {
                    $total_food= Food::where('restaurant_id', $restaurant->id)->count()+1;
                    if ( $total_food >= $rest_sub->max_product){
                        $restaurant->food_section = 0;
                        $restaurant->save();
                    }
                }
            } else{
                return response()->json([
                    'errors'=>[
                        ['code'=>'unauthorized', 'message'=>translate('messages.you_are_not_subscribed_to_any_package')]
                    ]
                ]);
            }
        }elseif( $restaurant->restaurant_model == 'unsubscribed'){
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.you_are_not_subscribed_to_any_package')]
                ]
            ]);
        }

        if(isset($request->options))
        {
            foreach(array_values($request->options) as $key=>$option)
            {
                if($option['min'] > 0 &&  $option['min'] > $option['max']  ){
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if(!isset($option['values'])){
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for').$option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if($option['max'] > count($option['values'])  ){
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for').$option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
            }

            $food->save();

            foreach(array_values($request->options) as $key=>$option)
            {
                $variation=  New Variation ();
                $variation->food_id =$food->id;
                $variation->name = $option['name'];
                $variation->type = $option['type'];
                $variation->min = $option['min'] ?? 0;
                $variation->max = $option['max'] ?? 0;
                $variation->is_required =   data_get($option, 'required') == 'on' ? true : false;
                $variation->save();

                foreach(array_values($option['values']) as $value)
                {
                    $VariationOption=  New VariationOption ();
                    $VariationOption->food_id =$food->id;
                    $VariationOption->variation_id =$variation->id;
                    $VariationOption->option_name = $value['label'];
                    $VariationOption->option_price = $value['optionPrice'];
                    $VariationOption->stock_type = $request?->stock_type ?? 'unlimited' ;
                    $VariationOption->total_stock = data_get($value, 'total_stock') == null || $VariationOption->stock_type == 'unlimited' ? 0 : data_get($value, 'total_stock');
                    $VariationOption->save();
                }
            }
        }
        else{
            $food->save();
        }
        $food->tags()->sync($tag_ids);
        $food->nutritions()->sync($nutrition_ids);
        $food->allergies()->sync($allergy_ids);


        FoodSeoData::create(
            Helpers::getFoodSEOData($request, $food->id, null)
        );

        Helpers::add_or_update_translations(request: $request, key_data:'name' , name_field:'name' , model_name: 'Food' ,data_id: $food->id,data_value: $food->name);
        Helpers::add_or_update_translations(request: $request, key_data:'description' , name_field:'description' , model_name: 'Food' ,data_id: $food->id,data_value: $food->description);
        if (addon_published_status('TaxModule')) {
            $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
            if ($SystemTaxVat?->tax_type == 'product_wise') {
                foreach ($request['tax_ids'] ?? [] as $tax_id) {
                    \Modules\TaxModule\Entities\Taxable::create(
                        [
                            'taxable_type' => Food::class,
                            'taxable_id' => $food->id,
                            'system_tax_setup_id' => $SystemTaxVat->id,
                            'tax_id' => $tax_id
                        ],
                    );
                }
            }
        }
        return response()->json([], 200);
    }

    public function view($id)
    {
         $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $product = Food::with($productWiseTax ? ['taxVats.tax','newVariationOptions.variation'] : ['newVariationOptions.variation'] )->findOrFail($id);
        $reviews=Review::where(['food_id'=>$id])->with('customer')->latest()->paginate(config('default_pagination'));
        return view('vendor-views.product.view', compact('product','reviews','productWiseTax'));
    }

    public function edit($id)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }

        $product = Food::withoutGlobalScope('translate')->with('foodSeoData')->findOrFail($id);
        $product_category = json_decode($product->category_ids);
        $categories = Category::where(['parent_id' => 0])->get();
        $taxData = Helpers::getTaxSystemType();
        $productWiseTax = $taxData['productWiseTax'];
        $taxVats = $taxData['taxVats'];
        $taxVatIds = $productWiseTax ? $product->taxVats()->pluck('tax_id')->toArray() : [];

        return view('vendor-views.product.edit', compact('product', 'product_category', 'categories', 'productWiseTax', 'taxVats', 'taxVatIds'));
    }

    public function status(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $product = Food::find($request->id);
        $product->status = $request->status;
        $product->save();
        if($request->status != 1){
            $product?->carts()?->delete();
        }
        Toastr::success(translate('Food status updated!'));
        return back();
    }
    public function recommended(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $product = Food::find($request->id);
        $product->recommended = $request->status;
        $product->save();
        Toastr::success(translate('Food recommendation updated!'));
        return back();
    }

    public function update(Request $request, $id)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            return response()->json([
                'errors'=>[
                    ['code'=>'unauthorized', 'message'=>translate('messages.permission_denied')]
                ]
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'array',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'category_id' => 'required',
            'price' => 'required|numeric|between:0.01,999999999999.99',
            'description.*' => 'max:65535',
            'discount' => 'required|numeric|min:0',
            'image' => 'nullable|max:2048',
        ], [
            'name.0.required' => translate('messages.item_name_required'),
            'category_id.required' => translate('messages.category_required'),
            'veg.required'=>translate('messages.item_type_is_required'),
            'description.*.max' => translate('messages.Description must be in 65535 char limit'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', translate('messages.discount_can_not_be_more_than_or_equal'));
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $tag_ids = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if(isset($tags)){
            foreach ($tags as $key => $value) {
                $tag = Tag::firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                array_push($tag_ids,$tag->id);
            }
        }

        $nutrition_ids = [];
        if ($request->nutritions != null) {
            $nutritions = $request->nutritions;
        }
        if (isset($nutritions)) {
            foreach ($nutritions as $key => $value) {
                $nutrition = Nutrition::firstOrNew(
                    ['nutrition' => $value]
                );
                $nutrition->save();
                array_push($nutrition_ids, $nutrition->id);
            }
        }
        $allergy_ids = [];
        if ($request->allergies != null) {
            $allergies = $request->allergies;
        }
        if (isset($allergies)) {
            foreach ($allergies as $key => $value) {
                $allergy = Allergy::firstOrNew(
                    ['allergy' => $value]
                );
                $allergy->save();
                array_push($allergy_ids, $allergy->id);
            }
        }

        $p = Food::with('foodSeoData')->find($id);

        $p->name = $request->name[array_search('default', $request->lang)];

        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        $p->slug = $p->slug? $p->slug :"{$slug}-{$p->id}";

        $category = [];
        if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $p->category_id = $request->sub_category_id?$request->sub_category_id:$request->category_id;
        $p->category_ids = json_encode($category);
        $p->description = $request->description[array_search('default', $request->lang)];
        $p->choice_options = json_encode([]);
        $p->variations = json_encode([]);
        if($request->remove_all_old_variations == 1){
            $p->newVariations()->delete();
            $p->newVariationOptions()->delete();
        }
        if(isset($request->options))
        {
            foreach(array_values($request->options) as $key=>$option)
            {
                if($option['min'] > 0 &&  $option['min'] > $option['max']  ){
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if(!isset($option['values'])){
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for').$option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if($option['max'] > count($option['values'])  ){
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for').$option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }

                $variation=Variation::updateOrCreate([
                    'id'=> $option['variation_id'] ?? null,
                    'food_id'=> $p->id,
                    ],[
                        "name" => $option['name'],
                        "type" => $option['type'],
                        "min" => $option['min'] ?? 0,
                        "max" => $option['max'] ?? 0,
                        "is_required" => data_get($option, 'required') == 'on' ? true : false,
                    ]);

                foreach(array_values($option['values']) as $value)
                {
                    VariationOption::updateOrCreate([
                        'id'=> $value['option_id'] ?? null,
                        'food_id'=> $p->id,
                        'variation_id'=> $variation->id,
                    ],[
                        "option_name" =>$value['label'],
                        "option_price" => $value['optionPrice'],
                        "total_stock" =>data_get($value, 'total_stock') == null ||  $request?->stock_type == 'unlimited' ? 0 : data_get($value, 'total_stock'),
                        "stock_type" => $request?->stock_type ?? 'unlimited' ,
                        "sell_count" =>0 ,
                    ]);
                }
            }

        }
        if($request?->removedVariationOptionIDs && is_string($request?->removedVariationOptionIDs)){
            VariationOption::whereIn('id',explode(',',$request->removedVariationOptionIDs))->delete();
        }
        if($request?->removedVariationIDs && is_string($request?->removedVariationIDs)){
            VariationOption::whereIn('variation_id',explode(',',$request->removedVariationIDs))->delete();
            Variation::whereIn('id',explode(',',$request->removedVariationIDs))->delete();
        }

        $p->item_stock = $request?->item_stock ?? 0;
        $p->stock_type = $request->stock_type;

        $p->price = $request->price;
        $p->veg = $request->veg;
        $p->image = $request->has('image') ? Helpers::update(dir:'product/',old_image: $p->image,format: 'png', image:$request->file('image')) : $p->image;
        $p->available_time_starts = $request->available_time_starts;
        $p->available_time_ends = $request->available_time_ends;
        $p->discount = $request->discount ?? 0;
        $p->discount_type = $request->discount_type;
        $p->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $p->add_ons = $request->has('addon_ids') ? json_encode($request->addon_ids) : json_encode([]);
        $p->maximum_cart_quantity = $request->maximum_cart_quantity;
        $p->is_halal =  $request->is_halal ?? 0;
        $p->sell_count = 0;

        $p->save();
        $p->tags()->sync($tag_ids);
        $p->nutritions()->sync($nutrition_ids);
        $p->allergies()->sync($allergy_ids);
        if (addon_published_status('TaxModule')) {
            $taxVatIds = $p->taxVats()->pluck('tax_id')->toArray() ?? [];
            $newTaxVatIds =  array_map('intval', $request['tax_ids'] ?? []);
            sort($newTaxVatIds);
            sort($taxVatIds);
            if ($newTaxVatIds != $taxVatIds) {
                $p->taxVats()->delete();
                $SystemTaxVat = \Modules\TaxModule\Entities\SystemTaxSetup::where('is_active', 1)->where('is_default', 1)->first();
                if ($SystemTaxVat?->tax_type == 'product_wise') {
                    foreach ($request['tax_ids'] ?? [] as $tax_id) {
                        \Modules\TaxModule\Entities\Taxable::create(
                            [
                                'taxable_type' => Food::class,
                                'taxable_id' => $p->id,
                                'system_tax_setup_id' => $SystemTaxVat->id,
                                'tax_id' => $tax_id
                            ],
                        );
                    }
                }
            }
        }

        FoodSeoData::updateOrCreate(
            ['food_id' => $p->id],
            Helpers::getFoodSEOData($request, $p->id, $p->foodSeoData)
        );


        Helpers::add_or_update_translations(request: $request, key_data:'name' , name_field:'name' , model_name: 'Food' ,data_id: $p->id,data_value: $p->name);
        Helpers::add_or_update_translations(request: $request, key_data:'description' , name_field:'description' , model_name: 'Food' ,data_id: $p->id,data_value: $p->description);

        return response()->json([], 200);
    }

    public function delete(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $product = Food::find($request->id);

        if($product->image)
        {
            Helpers::check_and_delete('product/' , $product['image']);
        }
        $product?->carts()?->delete();
        $product?->newVariationOptions()?->delete();
        $product?->newVariations()?->delete();
        $product?->translations()?->delete();
        $product?->taxVats()->delete();
        $product->delete();
        Toastr::success(translate('Food removed!'));
        return back();
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    public function list(Request $request)
    {
        $key = explode(' ', $request['search']);

        // New multi-select filters from offcanvas
        $categoryIds = (array) $request->query('category_ids', []);
        $statuses = (array) $request->query('status', []); // [1], [0] or [1,0]
        $types = (array) $request->query('types', []);     // ['veg'], ['non_veg'] or both
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        $category_id = $request->query('category_id', 'all');
        if ($category_id !== 'all' && is_numeric($category_id) && count($categoryIds) === 0) {
            $categoryIds = [$category_id];
        }

        $type = $request->query('type', 'all'); // legacy single type

        $foods = Food::with(['category.parent','newVariations','newVariationOptions','taxVats.tax'])
            ->when(is_array($categoryIds) && count($categoryIds) > 0, function($query) use ($categoryIds) {
                return $query->where(function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds)
                        ->orWhereHas('category', function($q2) use ($categoryIds) {
                            $q2->whereIn('parent_id', $categoryIds);
                        });
                });
            })
            ->when(isset($key), function($q) use ($key) {
                $q->where(function($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            // Types (veg/non_veg) - apply only when exactly one is selected; both means all
            ->when(is_array($types) && count($types) === 1, function($q) use ($types) {
                if ($types[0] === 'veg') {
                    $q->where('veg', true);
                } elseif ($types[0] === 'non_veg') {
                    $q->where('veg', false);
                }
            })
            // Legacy single type filter - only when multi-types not supplied
            ->when((!is_array($types) || count($types) === 0), function($q) use ($type) {
                $q->type($type);
            })
            // Status filter - apply only when exactly one is selected; both means all
            ->when(is_array($statuses) && count($statuses) === 1, function($q) use ($statuses) {
                $q->where('status', (int) $statuses[0]);
            })
            // Price range filter
            ->when(is_numeric($minPrice) && is_numeric($maxPrice) && (float)$minPrice <= (float)$maxPrice, function($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [(float) $minPrice, (float) $maxPrice]);
            })
            ->latest()
            ->paginate(config('default_pagination'));

        $category =$category_id !='all'? Category::findOrFail($category_id):null;
                $addonIds = collect($foods->items())
            ->pluck('add_ons')
            ->filter()
            ->flatMap(fn($json) => json_decode($json, true) ?? [])
            ->unique()
            ->values();


        $addons = \App\Models\AddOn::withOutGlobalScope(\App\Scopes\RestaurantScope::class)
            ->whereIn('id', $addonIds)
            ->pluck('name', 'id');

        $taxData = Helpers::getTaxSystemType(getTaxVatList: false);
        $productWiseTax = $taxData['productWiseTax'];

        $categoriesList = Category::select('id','name')->orderBy('name')->get();
        $minMaxPrices = Food::active()->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $foodMinPrice = round($minMaxPrices->min_price,2);
        $foodMaxPrice = round(($minMaxPrices->max_price + 1),2);

        return view('vendor-views.product.list', compact('foods', 'category', 'type','productWiseTax','addons','categoriesList','foodMinPrice','foodMaxPrice'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $foods=Food::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.product.partials._table',compact('foods'))->render()
        ]);
    }

    public function bulk_import_index()
    {
        return view('vendor-views.product.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }
        $request->validate([
            'products_file' => 'required|max:2048',
        ]);

        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        $data = [];
        if($request->button == 'import'){

            try {
                foreach ($collections as $collection) {
                    if ($collection['Id'] === "" || $collection['Name'] === "" || $collection['CategoryId'] === "" || $collection['SubCategoryId'] === "" || $collection['Price'] === "" || empty($collection['AvailableTimeStarts'])  || empty($collection['AvailableTimeEnds'])  || $collection['Discount'] === "") {
                        Toastr::error(translate('messages.please_fill_all_required_fields'));
                        return back();
                    }
                    if(isset($collection['Price']) && ($collection['Price'] < 0  )  ) {
                        Toastr::error(translate('messages.Price_must_be_greater_then_0_on_id').' '.$collection['Id']);
                        return back();
                    }
                    if(isset($collection['Discount']) && ($collection['Discount'] < 0  )  ) {
                        Toastr::error(translate('messages.Discount_must_be_greater_then_0_on_id').' '.$collection['Id']);
                        return back();
                    }
                    if (data_get($collection,'Image') != "" &&  strlen(data_get($collection,'Image')) > 30 ) {
                        Toastr::error(translate('messages.Image_name_must_be_in_30_char_on_id') . ' ' . $collection['Id']);
                        return back();
                    }

                    try{
                            $t1= Carbon::parse($collection['AvailableTimeStarts']);
                            $t2= Carbon::parse($collection['AvailableTimeEnds']) ;
                            if($t1->gt($t2)   ) {
                                Toastr::error(translate('messages.AvailableTimeEnds_must_be_greater_then_AvailableTimeStarts_on_id').' '.$collection['Id']);
                                return back();
                            }
                        }catch(\Exception $e){
                            info(["line___{$e->getLine()}",$e->getMessage()]);
                            Toastr::error(translate('messages.Invalid_AvailableTimeEnds_or_AvailableTimeStarts_on_id').' '.$collection['Id']);
                            return back();
                        }


                    array_push($data, [
                        'name' => $collection['Name'],
                        'description' => $collection['Description'],
                        'image' => $collection['Image'],
                        'category_id' => $collection['SubCategoryId']?$collection['SubCategoryId']:$collection['CategoryId'],
                        'category_ids' => json_encode([['id' => $collection['CategoryId'], 'position' => 1], ['id' => $collection['SubCategoryId'], 'position' => 2]]),
                        'restaurant_id' => Helpers::get_restaurant_id(),
                        'price' => $collection['Price'],
                        'discount' => $collection['Discount'] ?? 0,
                        'discount_type' => $collection['DiscountType'] ??  'percent',
                        'available_time_starts' => $collection['AvailableTimeStarts'],
                        'available_time_ends' => $collection['AvailableTimeEnds'],
                        'variations' => $collection['Variations'] ?? json_encode([]),
                        'add_ons' => $collection['Addons'] ?($collection['Addons']==""?json_encode([]):$collection['Addons']): json_encode([]),
                        'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                        'recommended' => $collection['Recommended'] == 'yes' ? 1 : 0,
                        'status' => $collection['Status'] == 'active' ? 1 : 0,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }

            }catch(\Exception $e){
                info(["line___{$e->getLine()}",$e->getMessage()]);
                Toastr::error($e->getMessage());
                return back();

            }

            try{
                DB::beginTransaction();
                $total_food= count($data);

                $restaurant= Helpers::get_restaurant_data();
                if ( $restaurant->restaurant_model == 'subscription' ) {
                    $rest_sub=$restaurant?->restaurant_sub;
                    if (isset($rest_sub)) {

                        if($rest_sub->max_product != "unlimited"){
                            if ( $rest_sub->max_product > 0  &&  $rest_sub->max_product >= $total_food ) {
                                $rest_sub->decrement('max_product' , $total_food);
                                if (  $rest_sub->max_product <= 0 ){
                                    $restaurant->update(['food_section' => 0]);
                                }
                            } else{
                                Toastr::error(translate('messages.you_have_reached_the_maximum_limit_of_food'));
                                return back();
                            }

                            if ( $rest_sub->max_product > 0 ) {
                                $total_all_foods= Food::where('restaurant_id', $restaurant->id)->count();

                                $available_food_uploads= $total_all_foods + $total_food;
                                if ($available_food_uploads > $rest_sub->max_product){
                                    Toastr::error(translate('messages.you_have_reached_the_maximum_limit_of_food'));
                                    return back();
                                }
                            }
                        }

                    } else{
                        return response()->json([
                            'errors'=>[
                                ['code'=>'unauthorized', 'message'=>translate('messages.you_are_not_subscribed_to_any_package')]
                            ]
                        ]);
                    }
                }

                    $chunkSize = 100;
                    $chunk_items= array_chunk($data,$chunkSize);
                    foreach($chunk_items as $key=> $chunk_item){
//                        DB::table('food')->insert($chunk_item);
                        foreach ($chunk_item as $item) {
                            $insertedId = DB::table('food')->insertGetId($item);
                            Helpers::updateStorageTable(get_class(new Food), $insertedId, $item['image']);
                        }
                    }

                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
                info(["line___{$e->getLine()}",$e->getMessage()]);
                Toastr::error($e->getMessage());
                return back();

            }

            Toastr::success(translate('messages.product_imported_successfully', ['count'=>count($data)]));
            return back();
        }

            try{
                foreach ($collections as $collection) {
                    if ($collection['Id'] === "" || $collection['Name'] === "" || $collection['CategoryId'] === "" || $collection['SubCategoryId'] === "" || $collection['Price'] === "" || empty($collection['AvailableTimeStarts'])  || empty($collection['AvailableTimeEnds'])  || $collection['Discount'] === "") {
                        Toastr::error(translate('messages.please_fill_all_required_fields'));
                        return back();
                    }
                    if(isset($collection['Price']) && ($collection['Price'] < 0  )  ) {
                        Toastr::error(translate('messages.Price_must_be_greater_then_0_on_id').' '.$collection['Id']);
                        return back();
                    }
                    if(isset($collection['Discount']) && ($collection['Discount'] < 0  )  ) {
                        Toastr::error(translate('messages.Discount_must_be_greater_then_0_on_id').' '.$collection['Id']);
                        return back();
                    }

                    if (data_get($collection,'Image') != "" &&  strlen(data_get($collection,'Image')) > 30 ) {
                        Toastr::error(translate('messages.Image_name_must_be_in_30_char_on_id') . ' ' . $collection['Id']);
                        return back();
                    }

                    try{
                            $t1= Carbon::parse($collection['AvailableTimeStarts']);
                            $t2= Carbon::parse($collection['AvailableTimeEnds']) ;
                            if($t1->gt($t2)   ) {
                                Toastr::error(translate('messages.AvailableTimeEnds_must_be_greater_then_AvailableTimeStarts_on_id').' '.$collection['Id']);
                                return back();
                            }
                        }catch(\Exception $e){
                            info(["line___{$e->getLine()}",$e->getMessage()]);
                            Toastr::error(translate('messages.Invalid_AvailableTimeEnds_or_AvailableTimeStarts_on_id').' '.$collection['Id']);
                            return back();
                        }

                    array_push($data, [
                        'id' => $collection['Id'],
                        'name' => $collection['Name'],
                        'description' => $collection['Description'],
                        'image' => $collection['Image'],
                        'category_id' => $collection['SubCategoryId']?$collection['SubCategoryId']:$collection['CategoryId'],
                        'category_ids' => json_encode([['id' => $collection['CategoryId'], 'position' => 1], ['id' => $collection['SubCategoryId'], 'position' => 2]]),
                        'restaurant_id' => Helpers::get_restaurant_id(),
                        'price' => $collection['Price'],
                        'discount' => $collection['Discount'] ?? 0,
                        'discount_type' => $collection['DiscountType'] ??  'percent',
                        'available_time_starts' => $collection['AvailableTimeStarts'],
                        'available_time_ends' => $collection['AvailableTimeEnds'],
                        'variations' => $collection['Variations'] ?? json_encode([]),
                        'add_ons' => $collection['Addons'] ?($collection['Addons']==""?json_encode([]):$collection['Addons']): json_encode([]),
                        'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                        'recommended' => $collection['Recommended'] == 'yes' ? 1 : 0,
                        'status' => $collection['Status'] == 'active' ? 1 : 0,
                        'updated_at'=>now()
                    ]);
                }
            }catch(\Exception $e)
            {
                info(["line___{$e->getLine()}",$e->getMessage()]);
                Toastr::error($e->getMessage());
                return back();
            }

        try{
            DB::beginTransaction();
            $chunkSize = 100;
            $chunk_items= array_chunk($data,$chunkSize);
            foreach($chunk_items as $key=> $chunk_item){
//                DB::table('food')->upsert($chunk_item,['id'],['name','description','image','category_id','category_ids','price','discount','discount_type','available_time_starts','available_time_ends','variations','add_ons','status','veg','recommended']);
                foreach ($chunk_item as $item) {
                    if (isset($item['id']) && DB::table('food')->where('id', $item['id'])->exists()) {
                        DB::table('food')->where('id', $item['id'])->update($item);
                        Helpers::updateStorageTable(get_class(new Food), $item['id'], $item['image']);
                    } else {
                        $insertedId = DB::table('food')->insertGetId($item);
                        Helpers::updateStorageTable(get_class(new Food), $insertedId, $item['image']);
                    }
                }
            }
            DB::commit();
        }catch(\Exception $e)
        {
            DB::rollBack();
            info(["line___{$e->getLine()}",$e->getMessage()]);
            Toastr::error($e->getMessage());
            return back();
        }

        Toastr::success(translate('messages.Food_imported_successfully', ['count' => count($data)]));
        return back();



    }

    public function bulk_export_index()
    {
        return view('vendor-views.product.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        if(!Helpers::get_restaurant_data()->food_section)
        {
            Toastr::warning(translate('messages.permission_denied'));
            return back();
        }

        $request->validate([
            'type'=>'required',
            'start_id'=>'required_if:type,id_wise',
            'end_id'=>'required_if:type,id_wise',
            'from_date'=>'required_if:type,date_wise',
            'to_date'=>'required_if:type,date_wise'
        ]);
        $products = Food::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })
        ->where('restaurant_id', Helpers::get_restaurant_id())
        ->get();

        return (new FastExcel(ProductLogic::format_export_foods($products)))->download('Foods.xlsx');
    }

    public function food_variation_generator(Request $request){
        $validator = Validator::make($request->all(), [
            'options' => 'required',
        ]);

        $food_variations = [];
        if (isset($request->options)) {
            foreach (array_values($request->options) as $key => $option) {

                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                if ($option['min'] > 0 &&  $option['min'] > $option['max']) {
                    $validator->getMessageBag()->add('name', translate('messages.minimum_value_can_not_be_greater_then_maximum_value'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if (!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_options_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                if ($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', translate('messages.please_add_more_options_or_change_the_max_value_for') . $option['name']);
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $temp_value = [];

                foreach (array_values($option['values']) as $value) {
                    if (isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    array_push($temp_value, $temp_option);
                }
                $temp_variation['values'] = $temp_value;
                array_push($food_variations, $temp_variation);
            }
        }

        return response()->json([
            'variation' => json_encode($food_variations)
        ]);
    }


    public function stockOutList(Request $request)
    {
        $key = explode(' ', $request['search']);

        // New multi-select filters from offcanvas
        $categoryIds = (array) $request->query('category_ids', []);
        $statuses = (array) $request->query('status', []); // [1], [0] or [1,0]
        $types = (array) $request->query('types', []);     // ['veg'], ['non_veg'] or both
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');

        $category_id = $request->query('category_id', 'all');
        if ($category_id !== 'all' && is_numeric($category_id) && count($categoryIds) === 0) {
            $categoryIds = [$category_id];
        }

        $type = $request->query('type', 'all'); // legacy single type

        $foods = Food::with(['category.parent','newVariations','newVariationOptions','taxVats.tax'])
            ->where('stock_type','!=' ,'unlimited' )->where(function($query){
                $query->whereRaw('item_stock - sell_count <= 0')->orWhereHas('newVariationOptions',function($query){
                    $query->whereRaw('total_stock - sell_count <= 0');
                });
            })
            ->when(is_array($categoryIds) && count($categoryIds) > 0, function($query) use ($categoryIds) {
                return $query->where(function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds)
                        ->orWhereHas('category', function($q2) use ($categoryIds) {
                            $q2->whereIn('parent_id', $categoryIds);
                        });
                });
            })
            ->when(isset($key), function($q) use ($key) {
                $q->where(function($q) use ($key) {
                    foreach ($key as $value) {
                        $q->where('name', 'like', "%{$value}%");
                    }
                });
            })
            // Types (veg/non_veg) - apply only when exactly one is selected; both means all
            ->when(is_array($types) && count($types) === 1, function($q) use ($types) {
                if ($types[0] === 'veg') {
                    $q->where('veg', true);
                } elseif ($types[0] === 'non_veg') {
                    $q->where('veg', false);
                }
            })
            // Legacy single type filter - only when multi-types not supplied
            ->when((!is_array($types) || count($types) === 0), function($q) use ($type) {
                $q->type($type);
            })
            // Status filter - apply only when exactly one is selected; both means all
            ->when(is_array($statuses) && count($statuses) === 1, function($q) use ($statuses) {
                $q->where('status', (int) $statuses[0]);
            })
            // Price range filter
            ->when(is_numeric($minPrice) && is_numeric($maxPrice) && (float)$minPrice <= (float)$maxPrice, function($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [(float) $minPrice, (float) $maxPrice]);
            })
            ->latest()
            ->paginate(config('default_pagination'));

        $category =$category_id !='all'? Category::findOrFail($category_id):null;
        $addonIds = collect($foods->items())
            ->pluck('add_ons')
            ->filter()
            ->flatMap(fn($json) => json_decode($json, true) ?? [])
            ->unique()
            ->values();


        $addons = \App\Models\AddOn::withOutGlobalScope(\App\Scopes\RestaurantScope::class)
            ->whereIn('id', $addonIds)
            ->pluck('name', 'id');

        $taxData = Helpers::getTaxSystemType(getTaxVatList: false);
        $productWiseTax = $taxData['productWiseTax'];

        $categoriesList = Category::select('id','name')->orderBy('name')->get();
        $minMaxPrices = Food::where('stock_type','!=' ,'unlimited' )->where(function($query){
            $query->whereRaw('item_stock - sell_count <= 0')->orWhereHas('newVariationOptions',function($query){
                $query->whereRaw('total_stock - sell_count <= 0');
            });
        })
        ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $foodMinPrice = $minMaxPrices->min_price;
        $foodMaxPrice = $minMaxPrices->max_price;

        return view('vendor-views.product.stock-out-list', compact('foods', 'category', 'type','productWiseTax','addons','categoriesList','foodMinPrice','foodMaxPrice'));

    }

    public function updateStock(Request $request){
        $product = Food::findOrFail($request->food_id);
        $product->item_stock = $request->item_stock;
        $product->sell_count =0;
        $product->save() ;
        if($request->option){
                foreach($request->option  as $key => $value ){
                    VariationOption::where('food_id',$product->id)->where('id',$key)->update([
                        'sell_count' => 0,
                        'total_stock'=> $value
                    ]);
                }
        }
        Toastr::success(translate('Stock_updated_successfully'));
        return back();
    }

    public function addToSession(Request $request)
    {
        Session::put($request->value, true);
        return response()->json(['success' => true]);
    }

    public function export(Request $request){
        try{
            $key = explode(' ', $request['search']);

            $categoryIds = (array) $request->query('category_ids', []);
            $statuses = (array) $request->query('status', []);
            $types = (array) $request->query('types', []);
            $minPrice = $request->query('min_price');
            $maxPrice = $request->query('max_price');

            // Backward compatibility with previous single filters
            $category_id = $request->query('category_id', 'all');
            if ($category_id !== 'all' && is_numeric($category_id) && count($categoryIds) === 0) {
                $categoryIds = [$category_id];
            }
            $type = $request->query('type', 'all'); // legacy single type

            $foods = Food::with(['category.parent','newVariations','newVariationOptions','taxVats.tax'])
                ->when(isset($request->is_stock_out), function ($query) {
                    $query->where('stock_type', '!=', 'unlimited')
                        ->where(function ($query) {
                            $query->whereRaw('item_stock - sell_count <= 0')
                                ->orWhereHas('newVariationOptions', function ($query) {
                                    $query->whereRaw('total_stock - sell_count <= 0');
                                });
                        });
                })
                ->when(is_array($categoryIds) && count($categoryIds) > 0, function($query) use ($categoryIds) {
                    return $query->where(function($q) use ($categoryIds) {
                        $q->whereIn('category_id', $categoryIds)
                            ->orWhereHas('category', function($q2) use ($categoryIds) {
                                $q2->whereIn('parent_id', $categoryIds);
                            });
                    });
                })
                ->when(isset($key), function($q) use ($key) {
                    $q->where(function($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('name', 'like', "%{$value}%");
                        }
                    });
                })
                // Types (veg/non_veg) - apply only when exactly one is selected; both means all
                ->when(is_array($types) && count($types) === 1, function($q) use ($types) {
                    if ($types[0] === 'veg') {
                        $q->where('veg', true);
                    } elseif ($types[0] === 'non_veg') {
                        $q->where('veg', false);
                    }
                })
                // Legacy single type filter - only when multi-types not supplied
                ->when((!is_array($types) || count($types) === 0), function($q) use ($type) {
                    $q->type($type);
                })
                // Status filter - apply only when exactly one is selected; both means all
                ->when(is_array($statuses) && count($statuses) === 1, function($q) use ($statuses) {
                    $q->where('status', (int) $statuses[0]);
                })
                // Price range filter
                ->when(is_numeric($minPrice) && is_numeric($maxPrice) && (float)$minPrice <= (float)$maxPrice, function($q) use ($minPrice, $maxPrice) {
                    $q->whereBetween('price', [(float) $minPrice, (float) $maxPrice]);
                })
                ->latest()
                ->get();

            $taxData = Helpers::getTaxSystemType(getTaxVatList: false);
            $productWiseTax = $taxData['productWiseTax'];

            $minMaxPrices = Food::selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
            $foodMinPrice = $minMaxPrices->min_price;
            $foodMaxPrice = $minMaxPrices->max_price;

            $categoryNames = '';

            if (is_array($categoryIds) && count($categoryIds) > 0) {
                $categoryNames = Category::whereIn('id', $categoryIds)
                    ->pluck('name')
                    ->implode(', ');
            }

            $data = [
                'data' => $foods,
                'search' => $request['search'] ?? null,
                'category' => $categoryNames,
                'productWiseTax' => $productWiseTax,
                'priceRange' => $foodMinPrice . ' - ' .$foodMaxPrice,
                'foodType' => !empty($types) ? ucwords(str_replace('_', ' ', implode(', ', $types))) : '',
                'status' => !empty($statuses) ? ucwords(implode(', ', $statuses)) : '',
            ];

            if(isset($request->is_stock_out)){
                $filename = 'Stock_out_food_list';
            }else{
                $filename = 'Food_list';
            }

            if($request->type == 'csv'){
                return Excel::download(new FoodListExport($data), $filename.'.csv');
            }
            return Excel::download(new FoodListExport($data), $filename.'.xlsx');

        }  catch(\Exception $e)
        {
            Toastr::error("line___{$e->getLine()}",$e->getMessage());
            info(["line___{$e->getLine()}",$e->getMessage()]);
            return back();
        }
    }


}
