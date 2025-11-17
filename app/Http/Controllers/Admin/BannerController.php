<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\DataSetting;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    function index()
    {
        $banners = Banner::latest()->paginate(config('default_pagination'));
        return view('admin-views.banner.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'image' => 'nullable|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,mkv,webm,m4v|max:10240',
            'banner_type' => 'required',
            'zone_id' => 'required',
            'restaurant_id' => 'required_if:banner_type,restaurant_wise',
            'item_id' => 'required_if:banner_type,item_wise',
        ], [
            'zone_id.required' => translate('messages.select_a_zone'),
            'restaurant_id.required_if'=> translate('messages.Restaurant is required when banner type is restaurant wise'),
            'item_id.required_if'=> translate('messages.Food is required when banner type is food wise'),
            'video.mimes' => translate('messages.video_must_be_mp4_mov_avi_mkv_webm_or_m4v'),
            'video.max' => translate('messages.video_size_must_not_exceed_10MB'),
        ]);

        if($request->title[array_search('default', $request->lang)] == '' ){
            $validator->getMessageBag()->add('title', translate('messages.default_title_is_required'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
            }

        if (!$request->hasFile('image') && !$request->hasFile('video')) {
            $validator->getMessageBag()->add('media', translate('messages.either_image_or_video_is_required'));
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $banner = new Banner;
        $banner->title = $request->title[array_search('default', $request->lang)];
        $banner->type = $request->banner_type;
        $banner->zone_id = $request->zone_id;

        // Handle image upload and blurhash generation
        if ($request->hasFile('image')) {
            $banner->image = Helpers::upload(dir:'banner/',  format:'png', image: $request->file('image'));
            if ($banner->image) {
                $banner->image_blurhash = Helpers::generate_blurhash('banner/', $banner->image);
            }
        }

        // Handle video upload, thumbnail, and blurhash generation
        if ($request->hasFile('video')) {
            $banner->video = Helpers::upload(dir:'banner/', format: $request->file('video')->getClientOriginalExtension(), image: $request->file('video'));

            // Generate thumbnail automatically
            if ($banner->video) {
                $banner->video_thumbnail = Helpers::generate_video_thumbnail('banner/', $banner->video);

                // Generate blurhash for thumbnail
                if ($banner->video_thumbnail) {
                    $banner->video_thumbnail_blurhash = Helpers::generate_blurhash('banner/', $banner->video_thumbnail);
                }
            }
        }

        $banner->data = ($request->banner_type == 'restaurant_wise')?$request->restaurant_id:$request->item_id;
        $banner->save();
        $data=[];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Banner',
                        'translationable_id' => $banner->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $banner->title,
                    ));
                }
            }else{
                if ($request->title[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Banner',
                        'translationable_id' => $banner->id,
                        'locale' => $key,
                        'key' => 'title',
                        'value' => $request->title[$index],
                    ));
                }
            }
        }
        Translation::insert($data);

        // Clear banner cache so new banner appears immediately
        $this->clearBannerCache();

        return response()->json([], 200);
    }

    public function edit(Banner $banner)
    {
        return view('admin-views.banner.edit', compact('banner'));
    }


    public function status(Request $request)
    {
        $banner = Banner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();

        // Clear banner cache so status change appears immediately
        $this->clearBannerCache();

        Toastr::success(translate('messages.banner_status_updated'));
        return back();
    }

    public function update(Request $request, Banner $banner)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'banner_type' => 'required',
            'zone_id' => 'required',
            'image' => 'nullable|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,mkv,webm,m4v|max:10240',
            'restaurant_id' => 'required_if:banner_type,restaurant_wise',
            'item_id' => 'required_if:banner_type,item_wise',
        ], [
            'zone_id.required' => translate('messages.select_a_zone'),
            'restaurant_id.required_if'=> translate('messages.Restaurant is required when banner type is restaurant wise'),
            'item_id.required_if'=> translate('messages.Food is required when banner type is food wise'),
            'video.mimes' => translate('messages.video_must_be_mp4_mov_avi_mkv_webm_or_m4v'),
            'video.max' => translate('messages.video_size_must_not_exceed_10MB'),
        ]);


        if($request->title[array_search('default', $request->lang)] == '' ){
            $validator->getMessageBag()->add('title', translate('messages.default_title_is_required'));
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
            }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $banner->title = $request->title[array_search('default', $request->lang)];;
        $banner->type = $request->banner_type;
        $banner->zone_id = $request->zone_id;

        // Handle image update and blurhash regeneration
        if ($request->has('image')) {
            $banner->image = Helpers::update(dir:'banner/',old_image: $banner->image, format:'png', image: $request->file('image'));
            if ($banner->image) {
                $banner->image_blurhash = Helpers::generate_blurhash('banner/', $banner->image);
            }
        }

        // Handle video update, thumbnail, and blurhash regeneration
        if ($request->has('video')) {
            // Delete old thumbnail if exists
            if ($banner->video_thumbnail) {
                Helpers::check_and_delete('banner/', $banner->video_thumbnail);
            }

            $banner->video = Helpers::update(dir:'banner/', old_image: $banner->video, format: $request->file('video')->getClientOriginalExtension(), image: $request->file('video'));

            // Generate new thumbnail and blurhash
            if ($banner->video) {
                $banner->video_thumbnail = Helpers::generate_video_thumbnail('banner/', $banner->video);

                if ($banner->video_thumbnail) {
                    $banner->video_thumbnail_blurhash = Helpers::generate_blurhash('banner/', $banner->video_thumbnail);
                }
            }
        }

        $banner->data = $request->banner_type=='restaurant_wise'?$request->restaurant_id:$request->item_id;
        $banner->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->title[$index])){
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Banner',
                            'translationable_id' => $banner->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $banner->title]
                    );
                }
            }else{

                if ($request->title[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\Banner',
                            'translationable_id' => $banner->id,
                            'locale' => $key,
                            'key' => 'title'
                        ],
                        ['value' => $request->title[$index]]
                    );
                }
            }
        }

        // Clear banner cache so updates appear immediately
        $this->clearBannerCache();

        return response()->json([], 200);
    }

    public function delete(Banner $banner)
    {
        Helpers::check_and_delete('banner/' , $banner['image']);
        if ($banner['video']) {
            Helpers::check_and_delete('banner/' , $banner['video']);
        }
        if ($banner['video_thumbnail']) {
            Helpers::check_and_delete('banner/' , $banner['video_thumbnail']);
        }
        $banner?->translations()?->delete();
        $banner->delete();

        // Clear banner cache so deletion appears immediately
        $this->clearBannerCache();

        Toastr::success(translate('messages.banner_deleted_successfully'));
        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $banners=Banner::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.banner.partials._table',compact('banners'))->render(),
            'count'=>$banners->count()
        ]);
    }





    public function promotional_banner(){
        $banner_title =  DataSetting::where('type','promotional_banner')->where('key' ,'promotional_banner_title')->withoutGlobalScope('translate')->with('translations')->first();
        $banner_image =  DataSetting::where('type','promotional_banner')->where('key', 'promotional_banner_image')->withoutGlobalScope('translate')->with('translations')->first();
        return view('admin-views.banner.promotional_banner', compact('banner_title','banner_image'));
    }

    public function promotional_banner_update(Request $request){

        $request->validate([
            'promotional_banner_title.*' => 'max:191',
            'promotional_banner_title.0'=>'required',
            'promotional_banner_image' => 'nullable|max:2048',
        ], [
            'promotional_banner_title.required' => translate('messages.Title is required!'),
            'promotional_banner_title.0.required'=>translate('default_Title_is_required'),
        ]);

        if( $request->has('promotional_banner_image')){
            $banner = DataSetting::firstOrNew(
                ['key' =>  'promotional_banner_image',
                'type' =>  'promotional_banner'],
            );
            $banner->value=   Helpers::update(dir:'banner/',old_image: $banner->value, format:'png', image: $request->file('promotional_banner_image'));
            $banner->save();
        }

        // dd($request->all());
        $this->update_data($request , 'promotional_banner_title','promotional_banner_title' );
        Toastr::success(translate('messages.banner_updated_successfully'));
        return back();

    }


    private function update_data($request, $key_data, $name_field , $type = 'promotional_banner' ){
        $data = DataSetting::firstOrNew(
            ['key' =>  $key_data,
            'type' =>  $type],
        );
// dd($request->{$name_field}[array_search('default', $request->lang)]);
        $data->value = $request->{$name_field}[array_search('default', $request->lang)];
        $data->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if ($default_lang == $key && !($request->{$name_field}[$index])) {
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\DataSetting',
                            'translationable_id' => $data->id,
                            'locale' => $key,
                            'key' => $key_data
                        ],
                        ['value' => $data->value]
                    );
                }
            } else {
                if ($request->{$name_field}[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\DataSetting',
                            'translationable_id' => $data->id,
                            'locale' => $key,
                            'key' => $key_data
                        ],
                        ['value' => $request->{$name_field}[$index]]
                    );
                }
            }
        }

        return true;
    }

    /**
     * Clear all banner-related cache keys
     */
    private function clearBannerCache()
    {
        // Clear all cache keys starting with "banners_"
        // This ensures all zone-specific banner caches are cleared
        $keys = Cache::get('banners_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Also use wildcard clearing for safety
        Cache::flush(); // Note: This clears ALL cache. For production, use Cache::tags() if Redis is available
    }
}
