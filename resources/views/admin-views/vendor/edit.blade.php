@extends('layouts.admin.app')

@section('title', translate('Update_restaurant_info'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ dynamicAsset('/public/assets/admin/css/intlTelInput.css') }}" />
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="content container-fluid initial-57">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-shop-outlined"></i>
                        {{ translate('messages.update_restaurant') }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        @php
            $delivery_time_start = explode('-', $restaurant->delivery_time)[0] ?? 10;
            $delivery_time_end = explode('-', $restaurant->delivery_time)[1] ?? 30;
            $delivery_time_type = explode('-', $restaurant->delivery_time)[2] ?? 'min';
        @endphp
        @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
        @php($language = $language->value ?? null)
        @php($default_lang = str_replace('_', '-', app()->getLocale()))

        <form action="{{ route('admin.restaurant.update', [$restaurant['id']]) }}" method="post" class="js-validate"
            id="res_form" enctype="multipart/form-data">
            @csrf
            @if (request()->type === 'new_join')
                <input type="hidden" name="new_join" value="1">
            @endif
            <div class="row g-2">
                <div class="col-md-7 col-xl-8">
                    <div class="card shadow--card-2">
                        <div class="card-body">
                            <div class="bg-light rounded p-md-3 bg-clr-none mb-20">
                                @if ($language)
                                    <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                        <ul class="nav nav-tabs mb-4">
                                            <li class="nav-item">
                                                <a class="nav-link lang_link active" href="#"
                                                    id="default-link">{{ translate('Default') }}</a>
                                            </li>
                                            @foreach (json_decode($language) as $lang)
                                                <li class="nav-item">
                                                    <a class="nav-link lang_link" href="#"
                                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="lang_form" id="default-form">
                                    <div class="form-group ">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.restaurant_name') }}
                                            ({{ translate('messages.default') }})</label>
                                        <input type="text" name="name[]" class="form-control"
                                            placeholder="{{ translate('messages.Ex:_ABC_Company') }} " maxlength="191"
                                            value="{{ $restaurant?->getRawOriginal('name') }}">
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">

                                    <div>
                                        <label class="input-label"
                                            for="address">{{ translate('messages.restaurant_address') }}
                                            ({{ translate('messages.default') }})</label>
                                        <textarea id="address" name="address[]" class="form-control h-70px"
                                            placeholder="{{ translate('messages.Ex:_House#94,_Road#8,_Abc_City') }} ">{{ $restaurant?->getRawOriginal('address') }}</textarea>
                                    </div>
                                </div>
                                @if ($language)
                                    @foreach (json_decode($language) as $lang)
                                        <?php
                                        if (count($restaurant['translations'])) {
                                            $translate = [];
                                            foreach ($restaurant['translations'] as $t) {
                                                if ($t->locale == $lang && $t->key == 'name') {
                                                    $translate[$lang]['name'] = $t->value;
                                                }
                                                if ($t->locale == $lang && $t->key == 'address') {
                                                    $translate[$lang]['address'] = $t->value;
                                                }
                                            }
                                        }
                                        ?>

                                        <div class="d-none lang_form" id="{{ $lang }}-form">

                                            <div class="form-group mb-0">
                                                <label class="input-label"
                                                    for="exampleFormControlInput1">{{ translate('messages.restaurant_name') }}
                                                    ({{ strtoupper($lang) }})</label>
                                                <input type="text" name="name[]" class="form-control"
                                                    placeholder="{{ translate('messages.Ex:_ABC_Company') }}  maxlength="191"
                                                    value="{{ $translate[$lang]['name'] ?? '' }}">
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{ $lang }}">

                                            <div>
                                                <label class="input-label"
                                                    for="address">{{ translate('messages.restaurant_address') }}
                                                    ({{ strtoupper($lang) }})</label>
                                                <textarea id="address{{ $lang }}" name="address[]" class="form-control h-70px"
                                                    placeholder="{{ translate('messages.Ex:_House#94,_Road#8,_Abc_City') }} "> {{ $translate[$lang]['address'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group m-0">
                                        <label class="input-label"
                                            for="cuisine">{{ translate('messages.cuisine') }}</label>
                                        <select name="cuisine_ids[]" id="cuisine"
                                            class="form-control h--45px min--45 js-select2-custom" multiple="multiple"
                                            data-placeholder="{{ translate('messages.select_Cuisine') }}">
                                            </option>
                                            @php($cuisine_array = \App\Models\Cuisine::where('status', 1)->get(['id', 'name'])->toArray())
                                            @php($selected_cuisine = isset($restaurant->cuisine) ? $restaurant->cuisine->pluck('id')->toArray() : [])
                                            @foreach ($cuisine_array as $cu)
                                                <option value="{{ $cu['id'] }}"
                                                    {{ in_array($cu['id'], $selected_cuisine) ? 'selected' : '' }}>
                                                    {{ $cu['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group m-0">
                                        <label class="input-label" for="choice_zones">{{ translate('messages.zone') }}
                                            <span data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.select_zone_for_map') }}"
                                                class="input-label-secondary"><img
                                                    src="{{ dynamicAsset('/public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.restaurant_lat_lng_warning') }}"></span>
                                        </label>
                                        <select name="zone_id" id="choice_zones"
                                            data-placeholder="{{ translate('messages.select_zone') }}"
                                            class="form-control h--45px js-select2-custom get_zone_data">
                                            <option value="{{ $restaurant->zone_id }}" selected>
                                                {{ $restaurant->zone->name }}</option>
                                            @foreach (\App\Models\Zone::where('status', 1)->get(['id', 'name']) as $zone)
                                                @if (isset(auth('admin')->user()->zone_id))
                                                    @if (auth('admin')->user()->zone_id == $zone->id)
                                                        <option value="{{ $zone->id }}"
                                                            {{ $restaurant->zone_id == $zone->id ? 'selected' : '' }}>
                                                            {{ $zone->name }}</option>
                                                    @endif
                                                @else
                                                    @if ($restaurant->zone_id !== $zone->id)
                                                        <option value="{{ $zone->id }}"
                                                            {{ $restaurant->zone_id == $zone->id ? 'selected' : '' }}>
                                                            {{ $zone->name }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="latitude" name="latitude"
                                    class="form-control h--45px disabled"
                                    placeholder="{{ translate('messages.Ex:_-94.22213') }}"
                                    value="{{ $restaurant->latitude }}"required readonly>
                                <input type="hidden" name="longitude" class="form-control h--45px disabled"
                                    placeholder="{{ translate('messages.Ex:_103.344322') }} " id="longitude"
                                    value="{{ $restaurant->longitude }}" required readonly>

                                <div class="col-md-12">
                                    <input id="pac-input" class="controls rounded initial-8"
                                        title="{{ translate('messages.search_your_location_here') }}" type="text"
                                        placeholder="{{ translate('messages.search_here') }}"
                                        style="position:absolute;z-index:9999;top:10px;left:50%;transform:translateX(-50%);width:300px;padding:6px;border:1px solid #ccc;background:#fff;">
                                    <div style="height: 220px !important" id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-xl-4">
                    <div class="card shadow--card-2">

                        <div class="card-body">
                            <div class="">
                                <div class="d-flex flex-column gap-3 bg-light rounded p-3 mb-20">
                                    <label class="__custom-upload-img mr-lg-5">
                                        <div class="mb-20 pb-2">
                                            <h5 class="mb-0">{{ translate('logo') }}</h5>
                                            <p class="mb-0 fs-12">{{ translate('Upload your website Logo') }}</p>
                                        </div>
                                        <center>
                                            <img class="img--110 w-100px h-100px onerror-image" id="viewer"
                                                data-onerror-image="{{ dynamicAsset('public/assets/admin/img/upload.png') }}"
                                                src="{{ $restaurant->logo_full_url ?? dynamicAsset('public/assets/admin/img/upload.png') }}"
                                                alt="logo image" />
                                        </center>
                                        <input type="file" name="logo" id="customFileEg1"
                                            class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                        <p class="opacity-75 mx-auto mt-3 fs-10 text-center">
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB (1:1)') }}
                                        </p>
                                    </label>
                                </div>
                                <div class="bg-light rounded p-3">
                                    <label class="__custom-upload-img">
                                        <div class="mb-20 pb-2">
                                            <h5 class="mb-0">{{ translate('Restaurant Cover (2:1)') }}</h5>
                                            <p class="mb-0 fs-12">{{ translate('Upload your website Logo') }}</p>
                                        </div>
                                        <center>
                                            <img class="img--vertical min-width-170px onerror-image" id="coverImageViewer"
                                                data-onerror-image="{{ dynamicAsset('public/assets/admin/img/upload-img.png') }}"
                                                src="{{ $restaurant->cover_photo_full_url ?? dynamicAsset('public/assets/admin/img/upload-img.png') }}"
                                                alt="Fav icon" />
                                        </center>
                                        <input type="file" name="cover_photo" id="coverImageUpload"
                                            class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <p class="opacity-75 mx-auto fs-10 text-center mt-3">
                                            {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB (3:1)') }}
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                        <div class="mb-20">
                            <h4 class="m-0">
                                {{ translate('Restaurant_Info') }}
                            </h4>
                            {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                        </div>
                        <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                            <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                                <div class="row g-2">

                                    <div class="col-md-6">

                                        <label class="mb-2"
                                            for="time">{{ translate('Estimated Delivery Time ( Min & Maximum Time )') }}<span
                                                class="text-danger">*</span></label>
                                        <div
                                            class="floating--date-inner d-flex align-items-center border rounded overflow-hidden">
                                            <div class="item w-100">
                                                <input id="minimum_delivery_time" type="number"
                                                    name="minimum_delivery_time"
                                                    class="form-control w-100 h--45px border-0 rounded-0"
                                                    placeholder="{{ translate('messages.Ex:_30') }}" pattern="^[0-9]{2}$"
                                                    required value="{{ $delivery_time_start }}">
                                            </div>
                                            <div class="item w-100 border-inline-start">
                                                <input id="maximum_delivery_time" type="number"
                                                    name="maximum_delivery_time"
                                                    class="form-control w-100 h--45px border-0 rounded-0"
                                                    placeholder="{{ translate('messages.Ex:_60') }}" pattern="[0-9]{2}"
                                                    required value="{{ $delivery_time_end }}">
                                            </div>
                                            <div class="item smaller min-w-100px">
                                                <select name="delivery_time_type" id="delivery_time_type"
                                                    class="custom-select bg-light h--45px border-0 rounded-0">
                                                    <option value="min" {{ $delivery_time_type == 'min' ? 'selected' : '' }}>
                                                        {{ translate('messages.minutes') }}</option>
                                                    <option
                                                        value="hours"{{ $delivery_time_type == 'hours' ? 'selected' : '' }}>
                                                        {{ translate('messages.hours') }}</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                        <div class="mb-20">
                            <h4 class="m-0">
                                {{ translate('owner_info') }}
                            </h4>
                            {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                        </div>
                        <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="form-group m-0">
                                        <label class="input-label"
                                            for="f_name">{{ translate('messages.first_name') }}</label>
                                        <input id="f_name" type="text" name="f_name" class="form-control h--45px"
                                            placeholder="{{ translate('messages.Ex:_Jhon') }} "
                                            value="{{ $restaurant->vendor->f_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group m-0">
                                        <label class="input-label"
                                            for="l_name">{{ translate('messages.last_name') }}</label>
                                        <input id="l_name" type="text" name="l_name" class="form-control h--45px"
                                            placeholder="{{ translate('messages.Ex:_Doe') }} "
                                            value="{{ $restaurant->vendor->l_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group m-0">
                                        <label class="input-label"
                                            for="phone">{{ translate('messages.phone') }}</label>
                                        <input id="phone" type="tel" name="phone" class="form-control h--45px"
                                            placeholder="{{ translate('messages.Ex:_+9XXX-XXX-XXXX') }} "
                                            value="{{ $restaurant->phone }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12">
                    <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                        <div class="mb-20">
                            <h4 class="m-0">
                                {{ translate('Tags') }}
                            </h4>
                            {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                        </div>
                        <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                            <input type="text" class="form-control" name="tags"
                                value="@foreach ($restaurant->tags as $c) {{ $c->tag . ',' }} @endforeach"
                                placeholder="Enter tags" data-role="tagsinput">
                        </div>
                    </div>
                </div>


                <div class="col-lg-12">
                    <div>
                        <div class="card p-20">
                            <div class="mb-20">
                                <h3 class="mb-1">{{ translate('Business TIN') }}</h3>
                                {{-- <p class="fz-12px mb-0">{{translate('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')}}</p> --}}
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8 col-xxl-9">
                                    <div class="bg--secondary rounded p-20 h-100">
                                        <div class="form-group">
                                            <label class="input-label mb-2 d-block title-clr fw-normal"
                                                for="exampleFormControlInput1">{{ translate('Taxpayer Identification Number(TIN)') }}</label>
                                            <input type="text" name="tin" id="tin"
                                                placeholder="{{ translate('Type Your Taxpayer Identification Number(TIN)') }}"
                                                class="form-control" value="{{ $restaurant->tin }}">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="input-label mb-2 d-block title-clr fw-normal"
                                                for="exampleFormControlInput1">{{ translate('Expire Date') }}</label>
                                            <input type="date" id="tin_expire_date" name="tin_expire_date"
                                                class="form-control" value="{{ $restaurant->tin_expire_date }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xxl-3">

                                    <div class="bg--secondary rounded p-20 h-100 single-document-uploaderwrap"
                                        data-document-uploader>
                                        <div class="d-flex align-items-center gap-1 justify-content-between mb-20">
                                            <div>
                                                <h4 class="mb-1 fz--14px">{{ translate('TIN Certificate') }}</h4>
                                                <p class="fz-12px mb-0">
                                                    {{ translate('pdf, doc, jpg. File size : max 2 MB') }}</p>
                                            </div>
                                            <div class="d-flex gap-3 align-items-center">
                                                <button type="button"
                                                    class="doc_edit_btn w-30px h-30 rounded d-flex align-items-center justify-content-center btn--primary btn px-3 icon-btn">
                                                    <i class="tio-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="file-assets"
                                                data-picture-icon="{{ dynamicAsset('public/assets/admin/img/picture.svg') }}"
                                                data-document-icon="{{ dynamicAsset('public/assets/admin/img/document.svg') }}"
                                                data-blank-thumbnail="{{ dynamicAsset('public/assets/admin/img/picture.svg') }}">
                                            </div>
                                            <!-- Upload box -->
                                            <div class="d-flex justify-content-center pdf-container">
                                                <div class="document-upload-wrapper d-none">
                                                    <input type="file" name="tin_certificate_image"
                                                        class="document_input" accept=".doc, .pdf, .jpg, .png, .jpeg">
                                                    <div class="textbox">
                                                        <img width="40" height="40" class="svg"
                                                            src="{{ dynamicAsset('public/assets/admin/img/doc-uploaded.png') }}"
                                                            alt="">
                                                        <p class="fs-12 mb-0">{{ translate('Select a file or') }} <span
                                                                class="font-semibold">{{ translate('Drag & Drop') }}</span>
                                                            {{ translate('here') }}</p>
                                                    </div>
                                                </div>
                                                <div class="pdf-single" data-file-name="${file.name}"
                                                    data-file-url="{{ $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png') }}">
                                                    <div class="pdf-frame">
                                                        @php($imgPath = $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png'))
                                                        @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                            @php($imgPath = dynamicAsset('public/assets/admin/img/document.svg'))
                                                        @endif
                                                        <img class="pdf-thumbnail-alt" src="{{ $imgPath }}"
                                                            alt="File Thumbnail">
                                                    </div>
                                                    <div class="overlay">
                                                        <div class="pdf-info">
                                                            @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                                <img src="{{ dynamicAsset('public/assets/admin/img/document.svg') }}"
                                                                    width="34" alt="File Type Logo">
                                                            @else
                                                                <img src="{{ dynamicAsset('public/assets/admin/img/picture.svg') }}"
                                                                    width="34" alt="File Type Logo">
                                                            @endif
                                                            <div class="file-name-wrapper">
                                                                <span
                                                                    class="file-name js-filename-truncate text-limit-show"
                                                                    data-limit="15">{{ $restaurant->tin_certificate_image }}</span>
                                                                <span
                                                                    class="opacity-50">{{ translate('Click to view the file') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12">
                    <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                        <div class="mb-20">
                            <h4 class="m-0">
                                {{ translate('Account info') }}
                            </h4>
                            {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                        </div>
                        <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="form-group m-0">
                                        <label class="input-label"
                                            for="email">{{ translate('messages.email') }}</label>
                                        <input id="email" type="email" name="email" class="form-control h--45px"
                                            placeholder="{{ translate('messages.Ex:_Jhone@company.com') }} "
                                            value="{{ $restaurant->email }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="js-form-message form-group m-0">
                                        <label class="input-label"
                                            for="signupSrPassword">{{ translate('messages.password') }}
                                            <span class="input-label-secondary ps-1" data-toggle="tooltip"
                                                title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"><img
                                                    src="{{ dynamicAsset('public/assets/admin/img/info-circle.svg') }}"
                                                    alt="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"></span>

                                        </label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control h--45px"
                                                name="password" id="signupSrPassword"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                                placeholder="{{ translate('messages.Ex:_8+_Character') }} "
                                                aria-label="{{ translate('messages.password_length_8+') }}"
                                                data-msg="Your password is invalid. Please try again."
                                                data-hs-toggle-password-options='{
                                                                                    "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                                                    "defaultClass": "tio-hidden-outlined",
                                                                                    "showClass": "tio-visible-outlined",
                                                                                    "classChangeTarget": ".js-toggle-passowrd-show-icon-1"
                                                                                    }'>
                                            <div class="js-toggle-password-target-1 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-passowrd-show-icon-1 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="js-form-message form-group m-0">
                                        <label class="input-label"
                                            for="signupSrConfirmPassword">{{ translate('messages.confirm_password') }}</label>

                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control h--45px"
                                                name="confirmPassword" id="signupSrConfirmPassword"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                                placeholder="{{ translate('messages.Ex:_8+ Character') }}"
                                                aria-label="{{ translate('messages.password_length_8+') }}"
                                                data-msg="Password does not match the confirm password."
                                                data-hs-toggle-password-options='{
                                                                                        "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                                                        "defaultClass": "tio-hidden-outlined",
                                                                                        "showClass": "tio-visible-outlined",
                                                                                        "classChangeTarget": ".js-toggle-passowrd-show-icon-2"
                                                                                        }'>
                                            <div class="js-toggle-password-target-2 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container justify-content-end mt-3">
                <button id="reset_btn" type="button" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                <button type="submit" class="btn btn--primary h--45px"><i class="tio-save"></i>
                    {{ translate('messages.save_information') }}</button>
            </div>
        </form>

    </div>

@endsection

@push('script_2')
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/file-preview/document-upload.js"></script>

    <script src="{{ dynamicAsset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/tags-input.min.js"></script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places&callback=initMap&v=3.45.8">
    </script>

    <script>
        "use strict";

        //Clear All Data
        $("#reset_btn").on("click", function() {
            $("#res_form")[0].reset();

            location.reload();
        });

        $('#tin_expire_date').attr('min', (new Date()).toISOString().split('T')[0]);

        $(document).ready(function() {
            function previewFile(inputSelector, previewImgSelector, textBoxSelector) {
                const input = $(inputSelector);
                const imagePreview = $(previewImgSelector);
                const textBox = $(textBoxSelector);

                input.on('change', function() {
                    const file = this.files[0];
                    if (!file) return;

                    const fileType = file.type;
                    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

                    if (validImageTypes.includes(fileType)) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.attr('src', e.target.result).removeClass('display-none');
                            textBox.hide();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.attr('src',
                                '{{ dynamicAsset('public/assets/admin/img/file-icon.png') }}')
                            .removeClass('display-none');
                        textBox.hide();
                    }
                });
            }

            previewFile('#tin_certificate_image', '#logoImageViewer2', '.upload-file__textbox');
        });






        $("#customFileEg1").change(function() {
            readURL(this, 'viewer');
        });

        $("#coverImageUpload").change(function() {
            readURL(this, 'coverImageViewer');
        });
        $('#res_form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        let myLatlng = {
            lat: {{ $restaurant->latitude }},
            lng: {{ $restaurant->longitude }}
        };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: myLatlng,
        });
        let zonePolygon = null;
        let infoWindow = new google.maps.InfoWindow({
            content: "Click the map to get Lat/Lng!",
            position: myLatlng,
        });
        let bounds = new google.maps.LatLngBounds();

        function initMap() {
            // Create the initial InfoWindow.
            new google.maps.Marker({
                position: {
                    lat: {{ $restaurant->latitude }},
                    lng: {{ $restaurant->longitude }}
                },
                map,
                title: "{{ $restaurant->name }}",
            });
            infoWindow.open(map);
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }
        initMap();


        $('.get_zone_data').on('change', function() {
            let id = $(this).val();

            get_zone_data(id);
        })


        function get_zone_data(id) {
            $.get({
                url: '{{ url('/') }}/admin/zone/get-coordinates/' + id,
                dataType: 'json',
                success: function(data) {
                    if (zonePolygon) {
                        zonePolygon.setMap(null);
                    }

                    zonePolygon = new google.maps.Polygon({
                        paths: data.coordinates,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: 'white',
                        fillOpacity: 0,
                    });
                    zonePolygon.setMap(map);
                    // map.setCenter(data.center);


                    bounds = new google.maps.LatLngBounds();
                    zonePolygon.getPaths().forEach(function(path) {
                        path.forEach(function(latlng) {
                            bounds.extend(latlng);
                        });
                    });
                    map.fitBounds(bounds);


                    google.maps.event.addListener(zonePolygon, 'click', function(mapsMouseEvent) {
                        infoWindow.close();
                        // Create a new InfoWindow.
                        infoWindow = new google.maps.InfoWindow({
                            position: mapsMouseEvent.latLng,
                            content: JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2),
                        });
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                        var coordinates = JSON.parse(coordinates);

                        document.getElementById('latitude').value = coordinates['lat'];
                        document.getElementById('longitude').value = coordinates['lng'];
                        infoWindow.open(map);
                    });
                },
            });
        }



        $(document).on('ready', function() {
            get_zone_data({{ $restaurant->zone_id }});
        });
    </script>
@endpush
