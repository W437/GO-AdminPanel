@extends('layouts.admin.app')

@section('title', translate('Customer_list'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm">
                <h1 class="page-header-title gap-1 flex-wrap">
                    {{ translate('messages.Customer Details') }} <span class="gray-dark"> #456785</span>
                </h1>
            </div>
        </div>
    </div>
    <div class="js-nav-scroller hs-nav-scroller-horizontal mb-15">
        <ul class="nav nav-tabs border-0 nav--tabs nav--pills nav--theme-version">
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Order List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0)">Wishlist</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Rating & Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Wallet</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Loyalty point</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Coupon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Referral Summary</a>
            </li>
        </ul>
    </div>
    <div class="row g-3">
        <div class="col-sm-6">
            <div class="card wishlist__controller-inner">
                <div class="card-header fs-14 text-dark font-semibold">
                    Food
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-10px">
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Café Monarch</h5>
                                    <span class="fs-12 text-gray">Food Fair Restaurant</span>
                                </div>
                                <h5 class="m-0 font-regular text-color">$ 1,4002.49</h5>
                            </div>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-center w-100 h-100">
                        <div class="text-center text-gray1 fs-16">
                            <img src="{{dynamicAsset('/public/assets/admin/img/no-wishlist-here.svg')}}" alt="no" class="d-block mb-10px mx-auto">
                            No Food Wishlist
                        </div>
                    </div>
                </div>
                <div class="page-area px-4 pb-3">
                    <div class="d-flex align-items-center justify-content-end">
                        <div>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                                        <span class="page-link" aria-hidden="true">‹</span>
                                    </li>
                                    <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                    <li class="page-item"><a class="page-link" href="">2</a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="">3</a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="2" rel="next" aria-label="Next »">›</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card wishlist__controller-inner">
                <div class="card-header fs-14 text-dark font-semibold">
                    Restaurant
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-10px">
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                            <div class="w-48">
                                <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                            </div>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                <div>
                                    <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                                </div>
                                <div class="m-0 fs-12 font-regular gray-dark fs-14">
                                   <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="d-flex align-items-center justify-content-center w-100 h-100">
                        <div class="text-center text-gray1 fs-16">
                            <img src="{{dynamicAsset('/public/assets/admin/img/no-wishlist-here.svg')}}" alt="no" class="d-block mb-10px mx-auto">
                            No Restaurants Wishlist
                        </div>
                    </div>
                </div>
                <div class="page-area px-4 pb-3">
                    <div class="d-flex align-items-center justify-content-end">
                        <div>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                                        <span class="page-link" aria-hidden="true">‹</span>
                                    </li>
                                    <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
                                    <li class="page-item"><a class="page-link" href="">2</a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="">3</a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="2" rel="next" aria-label="Next »">›</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@push('script_2')
<script>
"use strict";
$(document).on('ready', function() {
    // INITIALIZATION OF NAV SCROLLER
    // =======================================================
    $('.js-nav-scroller').each(function() {
        new HsNavScroller($(this)).init()
    });

    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function() {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});
</script>
@endpush