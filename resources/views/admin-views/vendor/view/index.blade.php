@extends('layouts.admin.app')

@section('title',$restaurant->name)

@push('css_or_js')
<!-- Custom styles for this page -->
<link href="{{dynamicAsset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title text-break me-2">
                <i class="tio-shop"></i> <span>{{$restaurant->name}}</span>
            </h1>
            @if($restaurant->vendor->status)
            <a href="{{route('admin.restaurant.edit',[$restaurant->id])}}" class="btn btn--primary my-2">
                <i class="tio-edit mr-2"></i> {{translate('messages.edit_restaurant')}}
            </a>
            @else
            <div>
                @if(!isset($restaurant->vendor->status))
                <a class="btn btn--danger text-capitalize my-2 request_alert"
                    data-url="{{route('admin.restaurant.application',[$restaurant['id'],0])}}" data-message="{{translate('messages.you_want_to_deny_this_application')}}"
                    href="javascript:">{{translate('messages.deny')}}</a>
                @endif
                <a class="btn btn--primary text-capitalize my-2 request_alert"
                    data-url="{{route('admin.restaurant.application',[$restaurant['id'],1])}}" data-message="{{translate('messages.you_want_to_approve_this_application')}}"
                    href="javascript:">{{translate('messages.approve')}}</a>
            </div>
            @endif
        </div>
        @if($restaurant->vendor->status)
        <!-- Nav Scroller -->
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <!-- Nav -->
            @include('admin-views.vendor.view.partials._header',['restaurant'=>$restaurant])

            <!-- End Nav -->
        </div>
        <!-- End Nav Scroller -->
        @endif
    </div>
    <!-- End Page Header -->
    <!-- Page Heading -->
    <div class="row my-2 g-3">
        <!-- Earnings (Monthly) Card Example -->
        <div class="for-card col-md-4">
            <div class="card bg--1 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <div class="cash--subtitle">
                        {{translate('messages.collected_cash_by_restaurant')}}
                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-3">
                        <i class="tio-wallet cash-icon mr-3"></i>
                        <h2 class="cash--title">{{\App\CentralLogics\Helpers::format_currency($wallet->collected_cash)}}
                        </h2>
                    </div>
                </div>
                <div class="card-footer pt-0 bg-transparent">
                    <a class="btn btn-- bg--title h--45px w-100" href="{{route('admin.account-transaction.index')}}"
                        title="{{translate('messages.goto_account_transaction')}}">{{translate('messages.collect_cash_from_restaurant')}}</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-3">
                <!-- Panding Withdraw Card Example -->
                <div class="col-sm-6">
                    <div class="resturant-card  bg--2">
                        <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->pending_withdraw)}}
                        </h4>
                        <span class="subtitle">{{translate('messages.pending_withdraw')}}</span>
                        <i class="tio-clock resturant-icon"></i>
                    </div>
                </div>

                <!-- Earnings (Monthly) Card Example -->
                <div class="col-sm-6">
                    <div class="resturant-card  bg--3">
                        <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->total_withdrawn)}}</h4>
                        <span class="subtitle">{{translate('messages.total_withdrawn_amount')}}</span>
                        <img class="resturant-icon"
                            src="{{dynamicAsset('/public/assets/admin/img/transactions/withdraw-amount.png')}}"
                            alt="transactions">
                    </div>
                </div>

                <!-- Collected Cash Card Example -->
                <div class="col-sm-6">
                    <div class="resturant-card  bg--5">
                        @if($wallet->balance ==  0)
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->balance)}}</h4>
                            <span class="subtitle">{{translate('messages.Balance')}}</span>
                        @elseif($wallet->balance >  0)
                        <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->balance)}}</h4>
                        <span class="subtitle">{{translate('messages.Withdraw_able_balance')}}</span>
                        @else
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency(abs($wallet->balance))}}</h4>
                        <span class="subtitle">{{translate('messages.Payable_balance')}}</span>

                        @endif



                        <img class="resturant-icon"
                            src="{{dynamicAsset('/public/assets/admin/img/transactions/withdraw-balance.png')}}"
                            alt="transactions">
                    </div>
                </div>

                <!-- Pending Requests Card Example -->
                <div class="col-sm-6">
                    <div class="resturant-card  bg--1">
                        <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($wallet->total_earning)}}</h4>
                        <span class="subtitle">{{translate('messages.total_earning')}}</span>
                        <i class="tio-chart-bar-4 resturant-icon"></i>
                    </div>
                </div>

                <!-- Total Food Likes Card -->
                <div class="col-sm-6">
                    <div class="resturant-card bg--4">
                        <h4 class="title">{{ number_format($restaurant->total_food_likes ?? 0) }}</h4>
                        <span class="subtitle">{{translate('messages.total_food_likes')}}</span>
                        <i class="tio-star resturant-icon"></i>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="mt-4">
        <div id="restaurant_details" class="row g-3">
            <div class="col-lg-12">
                <div class="card mt-2">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <span class="card-header-icon mr-2">
                                <i class="tio-shop-outlined"></i>
                            </span>
                            <span class="ml-1">{{translate('messages.restaurant_info')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-6">
                                <div class="resturant--info-address">
                                    <div class="logo">
                                        <img class="onerror-image" data-onerror-image="{{dynamicAsset('public/assets/admin/img/100x100/restaurant-default-image.png')}}"

                                             src="{{ $restaurant->logo_full_url ?? dynamicAsset('public/assets/admin/img/100x100/restaurant-default-image.png') }}">
                                    </div>
                                    <ul class="address-info list-unstyled list-unstyled-py-3 text-dark">
                                        <li>
                                            <h5 class="name">
                                                {{$restaurant->name}}
                                            </h5>
                                        </li>
                                        <li>
                                            <i class="tio-city nav-icon"></i>
                                            <span class="pl-1">
                                                {{translate('messages.address')}} : {{$restaurant->address}}
                                            </span>
                                        </li>

                                        <li>
                                            <i class="tio-call-talking nav-icon"></i>
                                            <span class="pl-1">
                                                {{translate('messages.phone')}} : {{$restaurant->phone}}
                                            </span>
                                        </li>
                                        <li>
                                            <i class="tio-email nav-icon"></i>
                                            <span class="pl-1">
                                                {{translate('messages.email')}} : {{$restaurant->email}}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div id="map" class="single-page-map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <span class="card-header-icon mr-2">
                                <i class="tio-user"></i>
                            </span>
                            <span class="ml-1">{{translate('messages.owner_info')}}</span>
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="resturant--info-address">
                            <div class="avatar avatar-xxl avatar-circle avatar-border-lg">
                                <img class="avatar-img onerror-image" data-onerror-image="{{dynamicAsset('public/assets/admin/img/160x160/img1.jpg')}}"
                                     src="{{ $restaurant?->vendor?->image_full_url ?? dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                    alt="Image Description">
                            </div>
                            <ul class="address-info address-info-2 list-unstyled list-unstyled-py-3 text-dark">
                                <li>
                                    <h5 class="name">
                                        {{$restaurant->vendor->f_name}} {{$restaurant->vendor->l_name}}
                                    </h5>
                                </li>
                                <li>
                                    <i class="tio-call-talking nav-icon"></i>
                                    <span class="pl-1">
                                        {{$restaurant->vendor->phone}}
                                    </span>
                                </li>
                                <li>
                                    <i class="tio-email nav-icon"></i>
                                    <span class="pl-1">
                                        {{$restaurant->vendor->email}}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <span class="card-header-icon mr-2">
                                <i class="tio-crown"></i>
                            </span>
                            <span class="ml-1">{{translate('messages.Business_Plan')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="resturant--info-address">
                            <ul class="address-info address-info-2 list-unstyled list-unstyled-py-3 text-dark">

                            @if ($restaurant->restaurant_model == 'commission')
                            <li>
                                <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{ translate($restaurant->restaurant_model) }}
                            </li>
                            @php($admin_commission = \App\Models\BusinessSetting::where(['key' => 'admin_commission'])->first()?->value)
                            <li>
                                <span><strong>{{translate('messages.Commission_percentage')}}</strong></span> <span>:</span> &nbsp; {{ $restaurant->comission > 0 ?  $restaurant->comission : $admin_commission }} %
                            </li>
                            @elseif ($restaurant->restaurant_model == 'subscription')
                                <li>
                                    <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{ translate($restaurant->restaurant_model) }} &nbsp;
                                    @if ($restaurant?->restaurant_sub_update_application->is_trial == '1')
                                    <small> <span class="badge badge-info" >{{ translate('messages.Free_trial')}}</span> </small>
                                    @endif
                                </li>
                                <li>
                                    <span> <strong>{{translate('messages.Package_name')}}</strong></span> <span>:</span> &nbsp; {{ $restaurant?->restaurant_sub_update_application?->package?->package_name  ?? translate('Pacakge_not_found!!!')}}
                                </li>
                            @elseif ($restaurant->restaurant_model == 'unsubscribed' && $restaurant?->restaurant_sub_update_application )
                                <li>
                                    <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{ translate($restaurant->restaurant_model) }} &nbsp;

                                    <small> <span class="badge badge-danger" >{{ translate('messages.Expired')}}</span> </small>

                                </li>
                                <li>
                                    <span> <strong>{{translate('messages.Package_name')}}</strong></span> <span>:</span> &nbsp; {{ $restaurant?->restaurant_sub_update_application?->package?->package_name  ?? translate('Pacakge_not_found!!!')}}
                                </li>
                            @elseif ($restaurant->restaurant_model == 'unsubscribed')
                                <li>
                                    <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{ translate('Plan_isn’t_selected_yet') }} &nbsp;
                                </li>

                            @elseif($restaurant->restaurant_model == 'none' && $restaurant->package_id )
                                    <li>
                                    <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{translate('messages.Subscription')}}
                                </li>
                                    <li>
                                    <span>  <strong>{{translate('messages.Package_Name')}}</span></strong>  <span>:</span> &nbsp; {{App\Models\SubscriptionPackage::where('id',$restaurant->package_id)->first()?->package_name   }}
                                </li>
                                    <li>
                                    <span>  <strong>{{translate('Payment_status')}}</span></strong>  <span>:</span> &nbsp; {{ translate('messages.payment_failed')   }}
                                </li>
                                @else
                                    <li>
                                    <span>  <strong>{{translate('messages.Business_Plan')}}</span></strong>  <span>:</span> &nbsp; {{ translate('Plan_isn’t_selected_yet.') }}
                                </li>
                            @endif




                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- @if ($restaurant->tin)
        <div class="row pt-3 g-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <span class="card-header-icon mr-2">
                                <i class="tio-user"></i>
                            </span>
                            <span class="ml-1">{{ translate('messages.business_registration_tax_id') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="resturant--info-address flex-sm-nowrap flex-wrap gap-2">
                            <div class="pdf-single  cus-document-responsive"
                                data-pdf-url="{{ $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png') }}">
                                <div class="pdf-frame">
                                    @php($imgPath = $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png'))
                                    @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                        @php($imgPath = dynamicAsset('public/assets/admin/img/document.svg'))
                                    @endif
                                    <img class="pdf-thumbnail-alt" src="{{ $imgPath }}" alt="File Thumbnail">
                                </div>
                                <div class="overlay">
                                    <a href="javascript:void(0);" class="download-btn" title="">
                                        <i class="tio-download-to"></i>
                                    </a>
                                    <div class="pdf-info d-flex gap-10px align-items-center">
                                        @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                            <i class="tio-document" style="font-size: 34px;"></i>
                                        @else
                                            <i class="tio-image" style="font-size: 34px;"></i>
                                        @endif
                                        <div class="fs-13 text--title d-flex flex-column">
                                            <span class="file-name js-filename-truncate"></span>
                                            <span class="opacity-50">{{ translate('Click to view the file') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex-column address-info address-info-2 list-unstyled list-unstyled-py-3">

                                <div class=" d-flex justify-content-start gap-1">
                                    <span class="text-custom-nowrap text-wrap"><strong class=" text-dark">
                                            {{ translate('messages.business_registration_tax_id') }}: </strong></span>
                                    <span class="pl-1">{{ $restaurant->tin }}</span>
                                </div>

                                <div class=" d-flex justify-content-start gap-1">
                                    <span class="text-custom-nowrap text-wrap"><strong
                                            class=" text-dark">{{ translate('Expire Date') }}: </strong></span>
                                    <span class="pl-1">{{ $restaurant->tin_expire_date }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif -->


        @if ($restaurant->tin)
        <div class="row pt-3 g-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title m-0 d-flex align-items-center">
                            <span class="card-header-icon mr-2">
                                <i class="tio-user"></i>
                            </span>
                            <span class="ml-1">{{ translate('messages.business_registration_tax_id') }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="resturant--info-address flex-sm-nowrap flex-wrap gap-2 single-document-uploaderwrap" data-document-uploader>
                            <div>
                                <div class="file-assets"
                                        data-picture-icon="{{ dynamicAsset('public/assets/admin/img/picture.svg') }}"
                                        data-document-icon="{{ dynamicAsset('public/assets/admin/img/document.svg') }}"
                                        data-blank-thumbnail="{{ dynamicAsset('public/assets/admin/img/picture.svg') }}">
                                </div>
                                <!-- Upload box -->
                                <div class="d-flex align-items-center flex-md-nowrap flex-wrap gap-2 justify-content-center pdf-container">
                                    <div class="document-upload-wrapper d-none">
                                        <input type="file" name="tin_certificate_image" class="document_input" accept=".doc, .pdf, .jpg, .png, .jpeg">
                                        <div class="textbox">
                                            <img width="40" height="40" class="svg"
                                                    src="{{ dynamicAsset('public/assets/admin/img/doc-uploaded.png') }}"
                                                    alt="">
                                            <p class="fs-12 mb-0">{{ translate('Select a file or') }} <span class="font-semibold">{{ translate('Drag & Drop') }}</span>
                                                {{ translate('here') }}</p>
                                        </div>
                                    </div>
                                    <div class="pdf-single pdf-single-reponsive" data-file-name="${file.name}" data-file-url="{{ $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png') }}">
                                        <div class="pdf-frame">
                                            @php($imgPath =  $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png'))
                                            @if(Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                @php($imgPath =  dynamicAsset('public/assets/admin/img/document.svg'))
                                            @endif
                                            <img class="pdf-thumbnail-alt" src="{{ $imgPath }}" alt="File Thumbnail">
                                        </div>
                                        <div class="overlay">
                                            <div class="pdf-info">
                                                @if(Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                                    <i class="tio-document" style="font-size: 34px;"></i>
                                                @else
                                                    <i class="tio-image" style="font-size: 34px;"></i>
                                                @endif
                                                <div class="file-name-wrapper">
                                            <span class="file-name js-filename-truncate text-limit-show" data-limit="15">{{ $restaurant->tin_certificate_image }}</span>
                                                    <span class="opacity-50">{{ translate('Click to view the file') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex-column address-info address-info-2 list-unstyled list-unstyled-py-3">
                                        <div class=" d-flex justify-content-start gap-1">
                                            <span class="text-custom-nowrap text-wrap"><strong class=" text-dark">
                                                    {{ translate('messages.business_registration_tax_id') }}: </strong></span>
                                            <span class="pl-1">{{ $restaurant->tin }}</span>
                                        </div>
                                        <div class=" d-flex justify-content-start gap-1">
                                            <span class="text-custom-nowrap text-wrap"><strong
                                                    class=" text-dark">{{ translate('Expire Date') }}: </strong></span>
                                            <span class="pl-1">{{ $restaurant->tin_expire_date }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="resturant--info-address flex-sm-nowrap flex-wrap gap-2">
                            <div class="pdf-single  cus-document-responsive"
                                data-pdf-url="{{ $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png') }}">
                                <div class="pdf-frame">
                                    @php($imgPath = $restaurant->tin_certificate_image_full_url ?? dynamicAsset('public/assets/admin/img/upload-cloud.png'))
                                    @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                        @php($imgPath = dynamicAsset('public/assets/admin/img/document.svg'))
                                    @endif
                                    <img class="pdf-thumbnail-alt" src="{{ $imgPath }}" alt="File Thumbnail">
                                </div>
                                <div class="overlay">
                                    <a href="javascript:void(0);" class="download-btn" title="">
                                        <i class="tio-download-to"></i>
                                    </a>
                                    <div class="pdf-info d-flex gap-10px align-items-center">
                                        @if (Str::endsWith($imgPath, ['.pdf', '.doc', '.docx']))
                                            <i class="tio-document" style="font-size: 34px;"></i>
                                        @else
                                            <i class="tio-image" style="font-size: 34px;"></i>
                                        @endif
                                        <div class="fs-13 text--title d-flex flex-column">
                                            <span class="file-name js-filename-truncate"></span>
                                            <span class="opacity-50">{{ translate('Click to view the file') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex-column address-info address-info-2 list-unstyled list-unstyled-py-3">

                                <div class=" d-flex justify-content-start gap-1">
                                    <span class="text-custom-nowrap text-wrap"><strong class=" text-dark">
                                            {{ translate('messages.business_registration_tax_id') }}: </strong></span>
                                    <span class="pl-1">{{ $restaurant->tin }}</span>
                                </div>

                                <div class=" d-flex justify-content-start gap-1">
                                    <span class="text-custom-nowrap text-wrap"><strong
                                            class=" text-dark">{{ translate('Expire Date') }}: </strong></span>
                                    <span class="pl-1">{{ $restaurant->tin_expire_date }}</span>
                                </div>
                            </div>

                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        @endif



        @if($restaurant->additional_data && count(json_decode($restaurant->additional_data, true)) > 0 )
        <div class="row mb-2">

        <div class="col-lg-12  mt-2">
            <div class="card ">

                <div class="card-header justify-content-between align-items-center">
                    <label class="input-label text-capitalize d-inline-flex align-items-center m-0">
                        <span class="line--limit-1"><i class="tio-building"></i> {{ translate('Additional_Information') }} </span>
                        <span data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('Additional_Information') }}"
                            class="input-label-secondary">
                            <i class="tio-info-outined"></i></span>
                    </label>
                </div>
                <div class="card-body">
                    <div class="__registration-information">
                        <div class="item">
                            <ul>
                                @foreach (json_decode($restaurant->additional_data, true) as $key => $item)
                                    @if (is_array($item))


                                    @foreach ($item as $k => $data)
                                    <li>
                                        <span class="left"> {{ $k == 0 ? translate($key) : ''}} </span>
                                        <span class="right">{{ $data }} </span>
                                    </li>
                                    @endforeach
                                    @else
                                    <li>
                                        <span class="left"> {{ translate($key) }} </span>
                                        <span class="right">{{ $item ?? translate('messages.N/A')}} </span>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        @endif

        @if($restaurant->additional_documents && count(json_decode($restaurant->additional_documents, true)) > 0 )
        <div class="row mb-2">
        <div class="col-lg-12 mb-2 mt-2">
            <div class="card ">
                <div class="card-header justify-content-between align-items-center">
                    <label class="input-label text-capitalize d-inline-flex align-items-center m-0">
                        <span class="line--limit-1"><i class="tio-file-text-outlined"></i>
                            {{ translate('Documents') }} </span>
                        <span data-toggle="tooltip" data-placement="right"
                            data-original-title="{{ translate('Additional_Documents') }}"
                            class="input-label-secondary">
                            <i class="tio-info-outined"></i></span>
                    </label>
                </div>
                <div class="card-body">
                    <h5 class="mb-3"> {{ translate('Attachments') }} </h5>
                    <div class="d-flex flex-wrap gap-4 align-items-start">
                            @foreach (json_decode($restaurant->additional_documents, true) as $key => $item)

                            @php($item  = is_string($item) ? json_deocde($item,true) : $item  )
                            @foreach ($item as $file)

                            @php($file =  is_string($file) ? ['file' => $file, 'storage' => 'public'] :  $file )
                                        <?php
                                            $path_info = pathinfo(\App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']));
                                            $f_date = $path_info['extension'];
                                            ?>

                                    @if (in_array($f_date, ['pdf', 'doc', 'docs', 'docx' ]))
                                            @if ($f_date == 'pdf')
                                                <div class="attachment-card min-w-260">
                                                    <label for="">{{ translate($key) }}</label>
                                                    <a href="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}" target="_blank" rel="noopener noreferrer">
                                                        <div class="img ">


                                                            <iframe src="https://docs.google.com/gview?url={{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}&embedded=true"></iframe>

                                                        </div>
                                                    </a>

                                                    <a href="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}" download
                                                        class="download-icon mt-3">
                                                        <img src="{{ dynamicAsset('/public/assets/admin/new-img/download-icon.svg') }}" alt="">
                                                    </a>
                                                    <a href="#" class="pdf-info">
                                                        <img src="{{ dynamicAsset('/public/assets/admin/new-img/pdf.png') }}" alt="">
                                                        <div class="w-0 flex-grow-1">
                                                            <h6 class="title">{{ translate('Click_To_View_The_file.pdf') }}
                                                            </h6>

                                                        </div>
                                                    </a>
                                                </div>
                                                @else
                                                <div class="attachment-card  min-w-260">
                                                    <label for="">{{ translate($key) }}</label>
                                                    <a href="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}" target="_blank" rel="noopener noreferrer">
                                                        <div class="img ">

                                                            <iframe src="https://docs.google.com/gview?url={{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}&embedded=true"></iframe>


                                                        </div>
                                                    </a>
                                                    <a href="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}" download
                                                        class="download-icon mt-3">
                                                        <img src="{{ dynamicAsset('/public/assets/admin/new-img/download-icon.svg') }}" alt="">
                                                    </a>
                                                    <a href="#" class="pdf-info">
                                                        <img src="{{ dynamicAsset('/public/assets/admin/new-img/doc.png') }}" alt="">
                                                        <div class="w-0 flex-grow-1">
                                                            <h6 class="title">{{ translate('Click_To_View_The_file.doc') }}
                                                            </h6>

                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                    @endif

                                @endforeach
                            @endforeach
                        </div>
                    <br>
                    <br>

                    <h5 class="mb-3"> {{ translate('Images') }} </h5>
                    <div class="d-flex flex-wrap gap-4 align-items-start">
                        @foreach (json_decode($restaurant->additional_documents, true) as $key => $item)

                        @php($item  = is_string($item) ? json_deocde($item,true) : $item  )

                        @foreach ($item as $file)
                        @php($file =  is_string($file) ? ['file' => $file, 'storage' => 'public'] :  $file )
                                <?php
                                    $path_info = pathinfo(\App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) );
                                    $f_date = $path_info['extension'];
                                    ?>
                                @if (in_array($f_date, ['jpg', 'jpeg', 'png']))
                                <div class="attachment-card max-w-360">
                                    <label for="">{{ translate($key) }}</label>
                                    <a href="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage']) }}" download
                                        class="download-icon mt-3">
                                        <img src="{{ dynamicAsset('/public/assets/admin/new-img/download-icon.svg') }}" alt="">
                                    </a>
                                    <img src="{{ \App\CentralLogics\Helpers::get_full_url('additional_documents',$file['file'],$file['storage'])  }}"
                                        class="aspect-615-350 cursor-pointer mw-100 object--cover" alt="">
                                </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        </div>
        @endif
    <!-- Card -->


    </div>
</div>
@endsection

@push('script_2')
<script
        src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&callback=initMap&v=3.45.8">
</script>
<script src="{{ dynamicAsset('public/assets/admin') }}/js/file-preview/document-upload.js"></script>
<script>
    "use strict";
    // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

    const myLatLng = { lat: {{$restaurant->latitude}}, lng: {{$restaurant->longitude}} };
        let map;
        initMap();
        function initMap() {
                 map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: myLatLng,
            });
            new google.maps.Marker({
                position: myLatLng,
                map,
                title: "{{$restaurant->name}}",
            });
        }

    $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('.request_alert').on('click', function (event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            request_alert(url, message)
        })

        function request_alert(url, message) {
            Swal.fire({
                title: "{{translate('messages.are_you_sure_?')}}",
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: "{{translate('messages.no')}}",
                confirmButtonText: "{{translate('messages.yes')}}",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>
@endpush
