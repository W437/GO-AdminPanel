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
                <a class="nav-link " href="javascript:void(0)">Wishlist</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0)">Rating & Reviews</a>
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
    <div class="card">
        <div class="card-header py-xl-20 flex-wrap gap-2 border-0">
            <h5 class="card-header-title">{{ translate('messages.Ratings & Reviews') }}</h5>
            <div class="search--button-wrapper flex-xxs-nowrap">
                <form>
                    <input type="hidden" name="id" value="" id="">
                    <div class="input--group input-group input-group-merge input-group-flush">
                        <input id="datatableSearch_" type="search" name="search" class="form-control" value=""
                            placeholder="{{  translate('Search here') }}" aria-label="Search" required>
                        <button type="submit" class="btn btn--reset px-2 w-35px">
                            <i class="tio-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Table -->
        <div class="table-responsive datatable-custom pt-0">
            <table id="" class="table table-borderless table-thead-borderless table-nowrap table-align-middle card-table">
                <thead class="global-bg-box">
                    <tr>
                        <th class="py-3 fs-14 text-capitalize">{{ translate('messages.sl') }}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Item')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Rating & Review')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Date')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Restaurant Reply')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.status')}}</th>                
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                                        <span class="fs-12 text-gray">Order ID: 100082</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="m-0 fs-12 font-regular gray-dark fs-14 mb-1">
                                <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                            </div>
                            <p class="line-limit-2 min-w-170px gray-dark fs-14 text-wrap max-w-400px" data-toggle="tooltip" data-placement="right" title="The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!">
                                The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!”
                            </p>
                        </td>
                        <td class="text-uppercase text-title fs-14">
                            28 Dec 2024 11:09 pm
                        </td>
                        <td>
                            <div class="gray-dark fs-14">
                               n/a
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="toggle-switch mx-auto toggle-switch-sm" for="status1">
                                <input type="checkbox" class="toggle-switch-input" id="status1" checked="">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn offcanvas-trigger" data-target="#rating-quick-view" href="">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                                        <span class="fs-12 text-gray">Order ID: 100082</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="m-0 fs-12 font-regular gray-dark fs-14 mb-1">
                                <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                            </div>
                            <p class="line-limit-2 min-w-170px gray-dark fs-14 text-wrap max-w-400px" data-toggle="tooltip" data-placement="right" title="The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!">
                                The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!”
                            </p>
                        </td>
                        <td class="text-uppercase text-title fs-14">
                            28 Dec 2024 11:09 pm
                        </td>
                        <td>
                            <div class="gray-dark fs-14">
                               n/a
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="toggle-switch mx-auto toggle-switch-sm" for="status2">
                                <input type="checkbox" class="toggle-switch-input" id="status2" checked="">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn offcanvas-trigger" data-target="#rating-quick-view" href="">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                                        <span class="fs-12 text-gray">Order ID: 100082</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="m-0 fs-12 font-regular gray-dark fs-14 mb-1">
                                <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                            </div>
                            <p class="line-limit-2 min-w-170px gray-dark fs-14 text-wrap max-w-400px" data-toggle="tooltip" data-placement="right" title="The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!">
                                The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!”
                            </p>
                        </td>
                        <td class="text-uppercase text-title fs-14">
                            28 Dec 2024 11:09 pm
                        </td>
                        <td>
                            <div class="gray-dark fs-14">
                               Thanks for the review.
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="toggle-switch mx-auto toggle-switch-sm" for="status3">
                                <input type="checkbox" class="toggle-switch-input" id="status3" checked="">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn offcanvas-trigger" data-target="#rating-quick-view" href="">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                                        <span class="fs-12 text-gray">Order ID: 100082</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="m-0 fs-12 font-regular gray-dark fs-14 mb-1">
                                <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                            </div>
                            <p class="line-limit-2 min-w-170px gray-dark fs-14 text-wrap max-w-400px" data-toggle="tooltip" data-placement="right" title="The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!">
                                The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!”
                            </p>
                        </td>
                        <td class="text-uppercase text-title fs-14">
                            28 Dec 2024 11:09 pm
                        </td>
                        <td>
                            <div class="gray-dark fs-14">
                               Thanks for the review.
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="toggle-switch mx-auto toggle-switch-sm" for="status4">
                                <input type="checkbox" class="toggle-switch-input" id="status4" checked="">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn offcanvas-trigger" data-target="#rating-quick-view" href="">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                                        <span class="fs-12 text-gray">Order ID: 100082</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="m-0 fs-12 font-regular gray-dark fs-14 mb-1">
                                <i class="tio-star brand-base-clr"></i> 4.5 <small class="text-gray1">(25+)</small>
                            </div>
                            <p class="line-limit-2 min-w-170px gray-dark fs-14 text-wrap max-w-400px" data-toggle="tooltip" data-placement="right" title="The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!">
                                The delivery was surprisingly fast considering the weather and traffic conditions. I placed my order during peak hours, yet it arrived hot and fresh within 30 minutes. The packaging was neat, and the rider followed the delivery instructions perfectly. Highly impressed with the service and professionalism. Will definitely order again!”
                            </p>
                        </td>
                        <td class="text-uppercase text-title fs-14">
                            28 Dec 2024 11:09 pm
                        </td>
                        <td>
                            <div class="gray-dark fs-14">
                               Thanks for the review.
                            </div>
                        </td>
                        <td class="text-center">
                            <label class="toggle-switch mx-auto toggle-switch-sm" for="status5">
                                <input type="checkbox" class="toggle-switch-input" id="status5" checked="">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--warning btn-outline-warning action-btn offcanvas-trigger" data-target="#rating-quick-view" href="">
                                    <i class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-lg-5" colspan="7">
                            <div class="py-md-5 d-flex align-items-center justify-content-center w-100 h-100">
                                <div class="text-center pt-80 pb-80 text-gray1 fs-16">
                                    <img src="{{dynamicAsset('/public/assets/admin/img/emty-review.svg')}}" alt="no" class="d-block mb-10px mx-auto">
                                    No Ratting & Reviews
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
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


<div id="rating-quick-view" class="custom-offcanvas d-flex flex-column justify-content-between"
    style="--offcanvas-width: 500px">
    <div>
        <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
            <div class="px-3 py-3 d-flex justify-content-between w-100">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Ratings & Reviews Quick View') }}</h2>
                </div>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                    aria-label="Close">&times;
                </button>
            </div>
        </div>
        <div class="custom-offcanvas-body p-20">
            <div class="d-flex flex-column gap-20px">
                <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded p-xxl-20 p-16">
                    <div class="w-40 min-w-40">
                        <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                        <div>
                            <h5 class="mb-0 font-normal text-color font-medium">Cheese sandwich</h5>
                            <span class="fs-12 text-title">Order ID: 100082</span>
                        </div>
                        <div class="m-0 bg-white rounded py-sm-2 py-1 px-xxl-5 px-sm-3 px-2 fs-18 text-title font-medium">
                            4.5 <i class="tio-star brand-base-clr"></i>
                        </div>
                    </div>
                </a>
                <div>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-10px">
                        <h4 class="m-0 text-title">Review</h4>
                        <span class="gray-dark">10 Jan 2025 12:10 PM</span>
                    </div>
                    <div class="global-bg-box rounded p-sm-3 p-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="min-w-35px rounded-circle">
                                <img width="35" height="35" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-0 font-normal text-color font-medium">Devid Jack</h5>
                            </div>
                        </div>
                        <div class="pragraph-description mb-2" data-limit="350">
                            <p class="m-0 gray-dark fs-14">
                                It is a long established fact that a reader will be distracted the
                                readable content of a page when looking at its layout. The point of
                                using Lorem Ipsum is that it has a more-or-less normal distribution of
                                letters, as opposed to using 'Content here,that it has a more-or-less normal distribution of
                                letters,
                            </p>
                            <a href="#0" class="theme-clr d-inline-block cursor-pointer text-underline see-more">{{ translate('messages.see_more') }}</a>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded">
                                <img src="{{dynamicAsset('/public/assets/admin/img/xl-view.png')}}" alt="img" class="rounded">
                            </div>
                            <div class="rounded">
                                <img src="{{dynamicAsset('/public/assets/admin/img/pdf-view.png')}}" alt="img" class="rounded">
                            </div>
                        </div>
                    </div>
                </div> 
                <div>
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-10px">
                        <h4 class="m-0 text-title">Reply</h4>
                        <span class="gray-dark">10 Jan 2025 12:10 PM</span>
                    </div>
                    <div class="global-bg-box rounded p-sm-3 p-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="min-w-35px rounded-circle">
                                <img width="35" height="35" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-0 font-normal text-color font-medium">Food Fair</h5>
                            </div>
                        </div>
                        <div class="pragraph-description" data-limit="300">
                            <p class="m-0 gray-dark fs-14">
                                It is a long established fact that a reader will be distracted the
                                readable content of a page when looking at its layout. The point of
                                using Lorem Ipsum is that it has a more-or-less normal distribution.
                            </p>
                            <a href="#0" class="theme-clr d-inline-block cursor-pointer text-underline see-more">{{ translate('messages.see_more') }}</a>
                        </div>
                    </div>
                </div>               
            </div>
        </div>
    </div>
</div>
<div id="offcanvasOverlay" class="offcanvas-overlay"></div>

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