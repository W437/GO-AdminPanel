@extends('layouts.admin.app')

@section('title', translate('messages.add_new_restaurant'))
@push('css_or_js')
    <link rel="stylesheet" href="{{dynamicAsset('/public/assets/admin/css/intlTelInput.css')}}" />
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">

@endpush
@section('content')
    <div class="content container-fluid initial-57">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-shop-outlined"></i>
                        {{ translate('messages.add_new_restaurant') }}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        @php($default_lang = str_replace('_', '-', app()->getLocale()))

        <form action="{{ route('admin.restaurant.store') }}" method="post" enctype="multipart/form-data"
            class="js-validate" id="res_form">
            @csrf
            <div class="card mb-20">
                <div class="card-header">
                    <div>
                        <h3 class="m-0">{{ translate('messages.Basic Information') }}</h3>
                        <p class="m-0 fs-12">{{ translate('Here you setup your all business information') }}.</p>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-7 col-xl-8">
                            <div class="card shadow--card-2">
                                <div class="card-body">
                                    <div class="bg-light rounded p-md-3 bg-clr-none mb-20">
                                        @if($language)
                                            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                                <ul class="nav nav-tabs mb-4">
                                                    <li class="nav-item">
                                                        <a class="nav-link lang_link active"
                                                        href="#"
                                                        id="default-link">{{ translate('Default') }}</a>
                                                    </li>
                                                    @foreach (json_decode($language) as $lang)
                                                        <li class="nav-item">
                                                            <a class="nav-link lang_link"
                                                                href="#"
                                                                id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="lang_form" id="default-form">
                                            <div class="form-group ">
                                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.restaurant_name') }} ({{translate('messages.default')}})</label>
                                                <input type="text" name="name[]"  value="{{ old('name.0') }}" class="form-control"  placeholder="{{ translate('messages.Ex:_ABC_Company') }} " maxlength="191"   >
                                            </div>
                                            <input type="hidden" name="lang[]" value="default">

                                            <div>
                                                <label class="input-label" for="address">{{ translate('messages.restaurant_address') }} ({{translate('messages.default')}})</label>
                                                <textarea id="address" name="address[]"   class="form-control" rows="1" placeholder="{{ translate('messages.Ex:_House#94,_Road#8,_Abc_City') }}"  >{{ old('address.0') }}</textarea>
                                            </div>
                                        </div>

                                        @if ($language)
                                        @foreach(json_decode($language) as $key => $lang)
                                        <div class="d-none lang_form" id="{{$lang}}-form">
                                            <div class="form-group" >
                                                <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.restaurant_name') }} ({{strtoupper($lang)}})</label>
                                                <input type="text" name="name[]" value="{{ old('name.' . $key + 1) }}"  class="form-control"  placeholder="{{ translate('messages.Ex:_ABC_Company') }} " maxlength="191"  >
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                            <div>
                                                <label class="input-label" for="address">{{ translate('messages.restaurant_address') }} ({{strtoupper($lang)}})</label>
                                                <textarea id="address{{$lang}}"   name="address[]" class="form-control" placeholder="{{ translate('messages.Ex:_House#94,_Road#8,_Abc_City') }} "  >{{ old('address.' . $key + 1) }}</textarea>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label" for="cuisine">{{ translate('messages.cuisine') }} <span class="text-danger">*</span></label>
                                                <select name="cuisine_ids[]" id="cuisine" class="form-control h--45px min--45 js-select2-custom"
                                                multiple="multiple"  data-placeholder="{{ translate('messages.select_Cuisine') }}" >
                                                    <option value="" disabled>{{ translate('messages.select_Cuisine') }}</option>
                                                    @foreach (\App\Models\Cuisine::where('status',1 )->get(['id','name']) as $cu)
                                                            <option value="{{ $cu->id }}">{{ $cu->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label" for="choice_zones">{{ translate('messages.zone') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="zone_id" id="choice_zones" required class="form-control h--45px js-select2-custom"
                                                    data-placeholder="{{ translate('messages.select_zone') }}">
                                                    <option value="" selected disabled>{{ translate('messages.select_zone') }}</option>
                                                    @foreach (\App\Models\Zone::where('status',1 )->get(['id','name']) as $zone)
                                                        @if (isset(auth('admin')->user()->zone_id))
                                                            @if (auth('admin')->user()->zone_id == $zone->id)
                                                                <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                                </option>
                                                            @endif
                                                        @else
                                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <input id="pac-input" class="controls rounded initial-8" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                            <div style="height: 170px !important" id="map" class="rounded overflow-hidden"></div>
                                            <small class="text-danger d-none mt-2 validation-message" id="coordinates_error">
                                                {{ translate('messages.restaurant_coordinates_are_required') }}
                                            </small>
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
                                            <div class="mb-20">
                                                <h5 class="mb-0">{{ translate('logo') }}</h5>
                                                <p class="mb-0 fs-12">{{ translate('Upload your website Logo') }}</p>
                                            </div>

                                            <div class="image-box w-100px h-100px mx-auto bg-white" id="logo_upload_box">
                                                <label for="image-input" class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                                <img width="30" class="upload-icon" src="{{dynamicAsset('public/assets/admin/img/img-up.png')}}" alt="Upload Icon">
                                                <span class="upload-text text-primary font-semibold fs-10">{{ translate('Click to upload')}}
                                                    <span class="text-secondary fs-10 d-block">{{ translate('or drag and drop') }}</span>
                                                </span>
                                                <img src="#" alt="Preview Image" class="preview-image">
                                                </label>
                                                <button type="button" class="delete_image">
                                                <i class="tio-delete"></i>
                                                </button>
                                                <input type="file" id="image-input" name="logo" accept="image/*" hidden>
                                            </div>
                                            <small class="text-danger d-none mt-2 validation-message" id="logo_error">
                                                {{ translate('messages.restaurant_logo_is_required') }}
                                            </small>

                                            <p class="opacity-75 mx-auto fs-10 text-center">
                                                {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB (1:1)')}}
                                            </p>
                                        </div>

                                        <div class="bg-light rounded p-3">
                                            <div class="d-flex flex-column gap-3 mw-100">
                                                <div class="mb-20">
                                                    <h5 class="mb-0">{{ translate('Restaurant_Cover') }}</h5>
                                                    <p class="mb-0 fs-12">{{ translate('Upload your website Logo') }}</p>
                                                </div>

                                                <div class="image-box ratio-3-1 max-w-300 mx-auto h-100px bg-white max-auto" id="cover_upload_box">
                                                    <label for="image-input2" class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                                    <img width="30" class="upload-icon" src="{{dynamicAsset('public/assets/admin/img/img-up.png')}}" alt="Upload Icon">
                                                    <span class="upload-text text-primary font-semibold fs-10">{{ translate('Click to upload')}}
                                                        <span class="text-secondary fs-10 d-block">{{ translate('or drag and drop') }}</span>
                                                    </span>
                                                    <img src="#" alt="Preview Image" class="preview-image">
                                                    </label>
                                                    <button type="button" class="delete_image">
                                                    <i class="tio-delete"></i>
                                                    </button>
                                                    <input type="file" id="image-input2" name="cover_photo" accept="image/*" hidden>
                                                </div>
                                                <small class="text-danger d-none mt-2 validation-message" id="cover_error">
                                                    {{ translate('messages.restaurant_cover_photo_is_required') }}
                                                </small>

                                                <p class="opacity-75 mx-auto fs-10 text-center">
                                                    {{ translate('JPG, JPEG, PNG, Gif Image size : Max 2 MB (3:1)')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="m-0">{{ translate('messages.General_Settings') }}</h3>
                        <p class="m-0 fs-12">{{ translate('messages.Here you setup your all business general settings') }}</p>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-lg-12">
                            <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                                <div class="mb-20">
                                    <h4 class="m-0">
                                        {{translate('Restaurant_Info')}}
                                    </h4>
                                    {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                                </div>
                                <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                                    <div class="row g-2">

                                        <div class="col-md-6">

                                            <label class="mb-2" for="time">{{ translate('Estimated Delivery Time ( Min & Maximum Time )') }}<span class="text-danger">*</span></label>
                                            <div class="floating--date-inner d-flex align-items-center border rounded overflow-hidden">
                                                <div class="item w-100">
                                                    <input id="minimum_delivery_time" type="number" name="minimum_delivery_time" class="form-control w-100 h--45px border-0 rounded-0" placeholder="{{ translate('messages.Ex:_30') }}"
                                                        pattern="^[0-9]{2}$" required value="{{ old('minimum_delivery_time') }}">
                                                </div>
                                                <div class="item w-100 border-inline-start">
                                                    <input id="maximum_delivery_time" type="number" name="maximum_delivery_time" class="form-control w-100 h--45px border-0 rounded-0" placeholder="{{ translate('messages.Ex:_60') }}"
                                                        pattern="[0-9]{2}" required value="{{ old('maximum_delivery_time') }}">
                                                </div>
                                                <div class="item smaller min-w-100px">
                                                    <select name="delivery_time_type" id="delivery_time_type" class="custom-select bg-light h--45px border-0 rounded-0">
                                                        <option value="min">{{translate('messages.minutes')}}</option>
                                                        <option value="hours">{{translate('messages.hours')}}</option>
                                                    </select>
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
                                        {{translate('Owner_Info')}}
                                    </h4>
                                    {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                                </div>
                                <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group m-0">
                                                <label class="input-label" for="f_name">{{ translate('messages.first_name') }} <span class="text-danger">*</span></label>
                                                <input id="f_name" type="text" name="f_name" class="form-control h--45px"
                                                    placeholder="{{ translate('messages.Ex:_Jhone') }}"
                                                    value="{{ old('f_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group m-0">
                                                <label class="input-label" for="l_name">{{ translate('messages.last_name') }} <span class="text-danger">*</span></label>
                                                <input id="l_name" type="text" name="l_name" class="form-control h--45px"
                                                    placeholder="{{ translate('messages.Ex:_Doe') }}"
                                                    value="{{ old('l_name') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group m-0">
                                                <label class="input-label" for="phone">{{ translate('messages.phone') }} <span class="text-danger">*</span></label>
                                                <input id="phone" type="tel" name="phone" class="form-control h--45px" placeholder="{{ translate('messages.Ex:_+9XXX-XXX-XXXX') }} "
                                                    value="{{ old('phone') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                           <input type="hidden" id="latitude" name="latitude" class="form-control h--45px disabled"   placeholder="{{ translate('messages.Ex:_-94.22213') }} " value="{{ old('latitude') }}" required readonly>
                            <input type="hidden" name="longitude" class="form-control h--45px disabled" placeholder="{{ translate('messages.Ex:_103.344322') }} "   id="longitude" value="{{ old('longitude') }}" required readonly>

                        @if (isset($page_data) && count($page_data) > 0 )

                        <div class="col-lg-12">
                            <div class="card shadow--card-2">
                                <div class="card-header">
                                    <h4 class="card-title m-0 d-flex align-items-center"> <span class="card-header-icon mr-2"><i class="tio-user"></i></span> <span>{{ translate('messages.Additional_Data') }}</span></h4>
                                </div>
                                <div class="card-body pb-0">
                                    <div class="row">
                                        @foreach ( data_get($page_data,'data',[])  as $key=>$item)
                                            @if (!in_array($item['field_type'], ['file' , 'check_box']) )
                                                <div class="col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="{{ $item['input_data'] }}">{{translate($item['input_data'])  }}</label>
                                                        <input id="{{ $item['input_data'] }}" {{ $item['is_required']  == 1? 'required' : '' }} type="{{ $item['field_type'] }}" name="additional_data[{{ $item['input_data'] }}]" class="form-control h--45px"
                                                            placeholder="{{ translate($item['placeholder_data']) }}"
                                                        >
                                                    </div>
                                                </div>
                                                @elseif ($item['field_type'] == 'check_box' )
                                                    @if ($item['check_data'] != null)
                                                    <div class="col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for=""> {{translate($item['input_data'])  }} </label>
                                                        @foreach ($item['check_data'] as $k=> $i)
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="additional_data[{{ $item['input_data'] }}][]"  class="form-check-input" value="{{ $i }}"> {{ translate($i) }}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    </div>
                                                    @endif
                                                @elseif ($item['field_type'] == 'file' )
                                                    @if ($item['media_data'] != null)
                                                    <?php
                                                    $image= '';
                                                    $pdf= '';
                                                    $docs= '';
                                                        if(data_get($item['media_data'],'image',null)){
                                                            $image ='.jpg, .jpeg, .png,';
                                                        }
                                                        if(data_get($item['media_data'],'pdf',null)){
                                                            $pdf =' .pdf,';
                                                        }
                                                        if(data_get($item['media_data'],'docs',null)){
                                                            $docs =' .doc, .docs, .docx' ;
                                                        }
                                                        $accept = $image.$pdf. $docs ;
                                                    ?>
                                                        <div class="col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="form-label" for="{{ $item['input_data'] }}">{{translate($item['input_data'])  }}</label>
                                                                <input id="{{ $item['input_data'] }}" {{ $item['is_required']  == 1? 'required' : '' }} type="{{ $item['field_type'] }}" name="additional_documents[{{ $item['input_data'] }}][]" class="form-control h--45px"
                                                                    placeholder="{{ translate($item['placeholder_data']) }}"
                                                                        {{ data_get($item['media_data'],'upload_multiple_files',null) ==  1  ? 'multiple' : '' }} accept="{{ $accept ??  '.jpg, .jpeg, .png'  }}"
                                                                    >
                                                            </div>
                                                        </div>
                                                    @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                        @endif

                        <div class="col-lg-12">
                            <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                                <div class="mb-20">
                                    <h4 class="m-0">
                                        {{translate('Tags')}}
                                    </h4>
                                    {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                                </div>
                                <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                                    <label for="" class="mb-2 d-block">{{ translate('Tags') }}</label>
                                    <input type="text" class="form-control rounded" name="tags" placeholder="Enter tags" data-role="tagsinput">
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12">
                            <div>
                                <div class="shadow-sm rounded p-lg-3 bg-clr-none">
                                    <div class="mb-20">
                                        <h4 class="m-0">
                                            {{translate('Business TIN')}}
                                        </h4>
                                        <p class="m-0 fs-12">{{ translate('Setup your Business TIN') }}</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-7 col-xxl-8">
                                            <div class="bg-light rounded p-20 h-100">
                                                <div class="form-group">
                                                    <label class="input-label mb-2 d-block title-clr fw-normal" for="exampleFormControlInput1">{{translate('Taxpayer Identification Number(TIN)')}}
                                                    <span class="input-label-secondary ps-1" data-toggle="tooltip" title="{{ translate('messages.Taxpayer Identification Number(TIN)') }}"><i class="tio-info text-muted fs-14"></i></span>
                                                </label>
                                                    <input type="text" id="tin" name="tin" placeholder="{{translate('Type Your Taxpayer Identification Number(TIN)')}}" class="form-control">
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label class="input-label mb-2 d-block title-clr fw-normal" for="exampleFormControlInput1">{{translate('Expire Date')}}
                                                    <span class="input-label-secondary ps-1" data-toggle="tooltip" title="{{ translate('messages.Expire Date') }}"><i class="tio-info text-muted fs-14"></i></span>
                                                </label>
                                                    <input type="date" id="tin_expire_date" name="tin_expire_date" class="form-control" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xxl-4">
                                            <div class="bg-light rounded h-100 p-20 single-document-uploaderwrap" data-document-uploader>
                                                <div class="d-flex align-items-center gap-1 justify-content-between mb-20">
                                                    <div>
                                                        <h4 class="mb-1 fz--14px">{{translate('TIN Certificate')}}</h4>
                                                        <p class="fz-12px mb-0">{{translate('pdf, doc, jpg. File size : max 2 MB')}}</p>
                                                    </div>
                                                    <div class="d-flex gap-3 align-items-center">
                                                        <button type="button" class="doc_edit_btn w-30px h-30 rounded d-flex align-items-center justify-content-center btn--primary btn px-3 icon-btn">
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
                                                        <div class="document-upload-wrapper">
                                                            <input type="file" id="tin_certificate_image" name="tin_certificate_image" class="document_input" accept=".doc, .pdf, .jpg, .png, .jpeg">
                                                            <div class="textbox">
                                                                <img width="40" height="40" class="svg"
                                                                    src="{{ dynamicAsset('public/assets/admin/img/doc-uploaded.png') }}"
                                                                    alt="">
                                                                <p class="fs-12 mb-0 text-center">{{ translate('Select a file or') }} <span class="font-semibold">{{ translate('Drag & Drop') }}</span>
                                                                    {{ translate('here') }}</p>
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
                                        {{translate('account_info')}}
                                    </h4>
                                    {{-- <p class="m-0 fs-12">Setup your business time zone and format from here</p> --}}
                                </div>
                                <div class="bg-light rounded p-space-0 bg-clr-none p-md-3">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="email">{{ translate('messages.email') }}</label>
                                                <input id="email" type="email" name="email" class="form-control h--45px" placeholder="{{ translate('messages.Ex:_Jhone@company.com') }} "
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label"
                                                    for="signupSrPassword">{{ translate('messages.password') }}

                                                </label>

                                                <div class="input-group input-group-merge">
                                                    <input type="password" class="js-toggle-password form-control h--45px" name="password"
                                                        id="signupSrPassword"
                                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"

                                                        placeholder="{{ translate('messages.Ex:_8+_Character') }}"
                                                        aria-label="{{translate('messages.password_length_8+')}}"
                                                        required data-msg="Your password is invalid. Please try again."
                                                        data-hs-toggle-password-options='{
                                                                                            "target": [".js-toggle-password-target-1"],
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
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label"
                                                    for="signupSrConfirmPassword">{{ translate('messages.confirm_password') }}</label>

                                                <div class="input-group input-group-merge">
                                                    <input type="password" class="js-toggle-password form-control h--45px" name="confirmPassword"
                                                        id="signupSrConfirmPassword"
                                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"

                                                        placeholder="{{ translate('messages.Ex:_8+_Character') }}"
                                                        aria-label="{{translate('messages.password_length_8+')}}"
                                                        required data-msg="Password does not match the confirm password."
                                                        data-hs-toggle-password-options='{
                                                                                                "target": [".js-toggle-password-target-2"],
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
                </div>
            </div>
            <div class="btn--container justify-content-end mt-3">
                <button id="reset_btn" type="button" class="btn btn--reset">{{translate('messages.reset')}}</button>
                <button type="submit" class="btn btn--primary h--45px"><i class="tio-save"></i> {{ translate('messages.save_information') }}</button>
            </div>
        </form>

    </div>

@endsection

@push('script_2')

<script src="{{ dynamicAsset('public/assets/admin') }}/js/file-preview/document-upload.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/file-preview/pdf.min.js"></script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/file-preview/pdf-worker.min.js"></script>

<script src="{{ dynamicAsset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/spartan-multi-image-picker.js') }}"></script>
    <script
            src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&loading=async&libraries=drawing,places&v=3.58&language={{ str_replace('_', '-', app()->getLocale()) }}&callback=initMap">
    </script>
    <script>
        "use strict";

        //Clear All Data
        $("#reset_btn").on("click", function () {
            $("#res_form")[0].reset();

            location.reload();
        });

        const uploadFields = [
            {
                input: $('#image-input'),
                wrapper: $('#logo_upload_box'),
                errorElement: $('#logo_error'),
                message: '{{ translate('messages.restaurant_logo_is_required') }}'
            },
            {
                input: $('#image-input2'),
                wrapper: $('#cover_upload_box'),
                errorElement: $('#cover_error'),
                message: '{{ translate('messages.restaurant_cover_photo_is_required') }}'
            }
        ];

        function showUploadError(field) {
            field.wrapper.addClass('border border-danger');
            field.errorElement.removeClass('d-none');
        }

        function clearUploadError(field) {
            field.wrapper.removeClass('border border-danger');
            field.errorElement.addClass('d-none');
        }

        function validateUploads(messages) {
            let hasError = false;
            uploadFields.forEach(field => {
                if (!field.input[0].files.length) {
                    showUploadError(field);
                    messages.add(field.message);
                    hasError = true;
                } else {
                    clearUploadError(field);
                }
            });
            return hasError;
        }

        function showCoordinateError() {
            $('#map').addClass('border border-danger');
            $('#coordinates_error').removeClass('d-none');
        }

        function clearCoordinateError() {
            $('#map').removeClass('border border-danger');
            $('#coordinates_error').addClass('d-none');
        }

        $('#latitude, #longitude').on('change', function () {
            if ($('#latitude').val() && $('#longitude').val()) {
                clearCoordinateError();
            }
        });

        uploadFields.forEach(field => {
            field.input.on('change', function () {
                if (this.files.length) {
                    clearUploadError(field);
                }
            });
        });

        $("#res_form").on("submit", function (event) {
            const form = this;
            const messages = new Set();
            let hasError = false;
            const hasHtmlValidationError = !form.checkValidity();

            if (hasHtmlValidationError) {
                hasError = true;
            }

            if ($('#latitude').val() === '' || $('#longitude').val() === '') {
                hasError = true;
                showCoordinateError();
                messages.add('{{ translate('messages.restaurant_coordinates_are_required') }}');
            } else {
                clearCoordinateError();
            }

            if (validateUploads(messages)) {
                hasError = true;
            }

            if (hasError) {
                event.preventDefault();
                if (hasHtmlValidationError) {
                    form.reportValidity();
                }

                messages.forEach(message => {
                    toastr.error(message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                });

                const $firstError = $('.validation-message').not('.d-none').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 120
                    }, 400);
                }
            }
        });

        $('#tin_expire_date').attr('min',(new Date()).toISOString().split('T')[0]);

        $(document).ready(function () {
            function previewFile(inputSelector, previewImgSelector, textBoxSelector) {
                const input = $(inputSelector);
                const imagePreview = $(previewImgSelector);
                const textBox = $(textBoxSelector);

                input.on('change', function () {
                    const file = this.files[0];
                    if (!file) return;

                    const fileType = file.type;
                    const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

                    if (validImageTypes.includes(fileType)) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            imagePreview.attr('src', e.target.result).removeClass('display-none');
                            textBox.hide();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.attr('src', '{{ dynamicAsset('public/assets/admin/img/file-icon.png') }}').removeClass('display-none');
                        textBox.hide();
                    }
                });
            }

            previewFile('#tin_certificate_image', '#logoImageViewer2', '.upload-file__textbox');
        });



        $(document).on('ready', function() {
            @if (isset(auth('admin')->user()->zone_id))
            $('#choice_zones').trigger('change');
            @endif
        });






        $('#res_form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });



                @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
                @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)

                let zonePolygon = null;
                let map = null;
                let bounds = null;
                let infoWindow = null;


            function initMap() {

                let myLatlng = {
                lat: {{ $default_location ? $default_location['lat'] : '23.757989' }},
                lng: {{ $default_location ? $default_location['lng'] : '90.360587' }}
                };
                map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: myLatlng,
                });

                zonePolygon = null;

                infoWindow = new google.maps.InfoWindow({
                    content: "{{  translate('Click_the_map_inside_the_red_marked_area_to_get_Lat/Lng!!!') }}",
                    position: myLatlng,
                });

                bounds = new google.maps.LatLngBounds();

                    infoWindow = new google.maps.InfoWindow();
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                myLatlng = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };
                                infoWindow.setPosition(myLatlng);
                                infoWindow.setContent("{{ translate('Select_Zone_From_The_Dropdown') }}");
                                infoWindow.open(map);
                                map.setCenter(myLatlng);
                            },
                            () => {
                                handleLocationError(true, infoWindow, map.getCenter());
                            }
                        );
                    } else {
                        // Browser doesn't support Geolocation
                        handleLocationError(false, infoWindow, map.getCenter());
                    }
                    //-----end block------
                    // Create the search box and link it to the UI element.
                    const input = document.getElementById("pac-input");
                    const searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                    let markers = [];
                    searchBox.addListener("places_changed", () => {
                        const places = searchBox.getPlaces();
                        if (places.length == 0) {
                        return;
                        }
                        markers.forEach((marker) => {
                        marker.setMap(null);
                        });
                        markers = [];
                        // For each place, get the icon, name and location.
                        const bounds = new google.maps.LatLngBounds();
                        places.forEach((place) => {
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

                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                    infoWindow.setPosition(pos);
                    infoWindow.setContent(
                        browserHasGeolocation ?
                        "{{ translate('Select_Zone_From_The_Dropdown.') }}" :
                        "{{ translate('Error:_Your_browser_doesnot_support_geolocation.') }}"
                    );
                    infoWindow.open(map);
                }
                $('#choice_zones').on('change', function() {
                    infoWindow.close();
                    var id = $(this).val();
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
                            // zonePolygon.getPaths().forEach(function(path) {
                            //     path.forEach(function(latlng) {
                            //         bounds.extend(latlng);
                            //         map.fitBounds(bounds);
                            //     });
                            // });


                            bounds = new google.maps.LatLngBounds();
                            zonePolygon.getPaths().forEach(function(path) {
                                path.forEach(function(latlng) {
                                    bounds.extend(latlng);
                                });
                            });
                            map.fitBounds(bounds);

                            infoWindow = new google.maps.InfoWindow({
                                content: "{{  translate('Click_the_map_inside_the_red_marked_area_to_get_Lat/Lng!') }}",
                                position: bounds.getCenter(),
                            });
                        infoWindow.open(map);
                            map.setCenter(data.center);
                            google.maps.event.addListener(zonePolygon, 'click', function(mapsMouseEvent) {
                                infoWindow.close();
                                // Create a new InfoWindow.
                                infoWindow = new google.maps.InfoWindow({
                            position: mapsMouseEvent.latLng,
                            content: JSON.stringify(mapsMouseEvent.latLng.toJSON(),
                                null, 2),
                        });
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null,
                            2);
                            infoWindow.close();
                        var coordinates = JSON.parse(coordinates);
                                    document.getElementById('latitude').value = coordinates['lat'];
                                    document.getElementById('longitude').value = coordinates['lng'];
                                    $('#latitude').trigger('change');
                                    $('#longitude').trigger('change');
                                    infoWindow.open(map);
                                });
                            },
                        });
                    });
                    document.addEventListener('keypress', function (e) {
                        if (e.keyCode === 13 || e.which === 13) {
                            e.preventDefault();
                            return false;
                        }
                    });

</script>
@endpush
