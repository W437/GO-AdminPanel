@extends('layouts.admin.app')

@section('title', translate('Customer_list'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <!--  -->
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
                <a class="nav-link " href="javascript:void(0)">Rating & Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Wallet</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0)">Loyalty point</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Coupon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Referral Summary</a>
            </li>
        </ul>
    </div>
    <div class="card mb-10px">
        <div class="card-body p-10px">
            <div class="row g-1">
                <div class="col-sm-6 col-md-4">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg1" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-45px h-45px rounded-circle d-center bg-white">
                                <img width="20" height="20" src="{{dynamicAsset('/public/assets/admin/img/blance-time.png')}}" alt="img" class="object--contain">
                            </div>
                            <div>
                                <h3 class="text-title font-bold mb-1 lh-1 align-items-center">
                                    3005
                                </h3>
                                <span class="fs-14 font-normal text-title">Points</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg2" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-45px h-45px rounded-circle d-center bg-white">
                                <img width="20" height="20" src="{{dynamicAsset('/public/assets/admin/img/deposit-wallet.png')}}" alt="img" class="object--contain">
                            </div>
                            <div>
                                <h3 class="text-title font-bold mb-1 lh-1 align-items-center">
                                  5000
                                </h3>
                                <span class="fs-14 font-normal text-title">Debit</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg3" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-45px h-45px rounded-circle d-center bg-white">
                                <img width="20" height="20" src="{{dynamicAsset('/public/assets/admin/img/blance-time.png')}}" alt="img" class="object--contain">
                            </div>
                            <div>
                                <h3 class="text-title font-bold mb-1 lh-1 align-items-center">
                                    2613
                                </h3>
                                <span class="fs-14 font-normal text-title">Credit</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header py-xl-20 flex-wrap gap-2 border-0">
            <h5 class="card-header-title">{{ translate('messages.Loyalty point History') }}
            </h5>
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
            <table  class="table table-borderless table-thead-borderless table-nowrap table-align-middle card-table">
                <thead class="global-bg-box">
                    <tr>
                        <th class="py-3 fs-14 text-capitalize">{{ translate('messages.sl') }}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Transaction Id')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Order_Date')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-right">{{translate('messages.Point')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Reference')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Transaction type')}}</th>                
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td class="w-400">
                            <div class="text-title line-limit-2 text-wrap max-w-260px min-w-140">
                                4fae74af-cb8-4ae1-b 4fae74 af-cba8- 4ae1-b 4fae7
                            </div>
                        </td>
                        <td class="text-uppercase fs-12">
                            <div>
                                28 Dec 2024 
                            </div>
                            <div>
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">-15</div>
                            <span class="badge badge-soft-danger min-w--50 px-2 mt-1">
                                Debit
                            </span>
                        </td>
                       <td>
                            <div class="text-title">
                                Gift
                            </div>
                       </td>
                       <td>
                            <div class="text-title">
                                CashBack
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td class="w-400">
                            <div class="text-title line-limit-2 text-wrap max-w-260px min-w-140">
                                4fae74af-cb8-4ae1-b 4fae74 af-cba8- 4ae1-b 4fae7 4af cb8-4ae1-b 4fae74 af-cba8-4ae1-b
                            </div>
                        </td>
                        <td class="text-uppercase fs-12">
                            <div>
                                28 Dec 2024 
                            </div>
                            <div>
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">+65</div>
                             <span class="badge badge-soft-success min-w--50 px-2 mt-1">
                                Credit
                            </span>
                        </td>
                       <td>
                            <div class="text-title">
                                N/A
                            </div>
                       </td>
                       <td>
                            <div class="text-title">
                                Add fundAdd fund
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="w-400">
                            <div class="text-title line-limit-2 text-wrap max-w-260px min-w-140">
                                4fae74af-cb8-4ae1-b 4fae74 af-cba8- 4ae1-b 4fae7
                            </div>
                        </td>
                        <td class="text-uppercase fs-12">
                            <div>
                                28 Dec 2024 
                            </div>
                            <div>
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">-95</div>
                            <span class="badge badge-soft-danger min-w--50 px-2 mt-1">
                                Debit
                            </span>
                        </td>
                       <td>
                            <div class="text-title">
                                Gift
                            </div>
                       </td>
                       <td>
                            <div class="text-title">
                                CashBack
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td class="w-400">
                            <div class="text-title line-limit-2 text-wrap max-w-260px min-w-140">
                                4fae74af-cb8-4ae1-b 4fae74 af-cba8- 4ae1-b 4fae7
                            </div>
                        </td>
                        <td class="text-uppercase fs-12">
                            <div>
                                28 Dec 2024 
                            </div>
                            <div>
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">-$ 12.00</div>
                            <span class="badge badge-soft-danger min-w--50 px-2 mt-1">
                                Debit
                            </span>
                        </td>
                       <td>
                            <div class="text-title">
                                Gift
                            </div>
                       </td>
                       <td>
                            <div class="text-title">
                                CashBack
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td class="w-400">
                            <div class="text-title line-limit-2 text-wrap max-w-260px min-w-140">
                                4fae74af-cb8-4ae1-b 4fae74 af-cba8- 4ae1-b 4fae7
                            </div>
                        </td>
                        <td class="text-uppercase fs-12">
                            <div>
                                28 Dec 2024 
                            </div>
                            <div>
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">-$ 12.00</div>
                            <span class="badge badge-soft-danger min-w--50 px-2 mt-1">
                                Debit
                            </span>
                        </td>
                       <td>
                            <div class="text-title">
                                Gift
                            </div>
                       </td>
                       <td>
                            <div class="text-title">
                                CashBack
                            </div>
                       </td>
                    </tr>
                </tbody>

            </table>
            <!-- <div class="empty--data">
                        <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div> -->
            <!-- Pagination -->
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
            <!-- Pagination -->
        </div>
    </div>
</div>


</div>


<!-- Most Order Items -->
<div id="order-list_filter" class="custom-offcanvas d-flex flex-column justify-content-between"
    style="--offcanvas-width: 500px">
    <div>
        <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
            <div class="px-3 py-3 d-flex justify-content-between w-100">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Filter') }}</h2>
                </div>
                <button type="button"
                    class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                    aria-label="Close">&times;
                </button>
            </div>
        </div>
        <div class="custom-offcanvas-body p-20">
            <div class="d-flex flex-column gap-20px">
                <div class="global-bg-box rounded p-xl-20 p-16">
                    <h5 class="mb-10px font-regular text-color font-normal">Date Range</h5>
                    <div class="bg-white rounded p-xl-3 p-2">
                       <div class="row g-2 filter-date-range">
                            <div class="col-sm-6">
                                <div class="form-group m-0">
                                    <label for="date_from" class="mb-10px font-regular text-color font-normal">Start Date</label>
                                    <input type="date" name="from_date" class="form-control" id="date_from" value="{{ isset($from_date) ? $from_date : '' }}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-0">
                                    <label for="date_to" class="mb-10px font-regular text-color font-normal">End Date</label>
                                    <input type="date" name="to_date" class="form-control" id="date_to" value="{{ isset($to_date) ? $to_date : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="global-bg-box rounded p-xl-20 p-16">
                    <h5 class="mb-10px font-regular text-color font-normal">Order Status</h5>
                    <div class="bg-white rounded p-xl-3 p-2">
                        <div class="row gx-xl-3 gx-2 gy-xl-3 gy-2 order-status_controller">
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="all" name="order_status">
                                        <label class="custom-control-label" for="all">
                                            {{translate('messages.All')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status1" name="order_status">
                                        <label class="custom-control-label" for="order-status1">
                                            {{translate('messages.Scheduled')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status2" name="order_status">
                                        <label class="custom-control-label" for="order-status2">
                                            {{translate('messages.Pending')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status3" name="order_status">
                                        <label class="custom-control-label" for="order-status3">
                                            {{translate('messages.Accepted')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status4" name="order_status">
                                        <label class="custom-control-label" for="order-status4">
                                            {{translate('messages.Processing')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status5" name="order_status">
                                        <label class="custom-control-label" for="order-status5">
                                            {{translate('messages.On the Way')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status6" name="order_status">
                                        <label class="custom-control-label" for="order-status6">
                                            {{translate('messages.Delivered')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status7" name="order_status">
                                        <label class="custom-control-label" for="order-status7">
                                            {{translate('messages.Canceled')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status8" name="order_status">
                                        <label class="custom-control-label" for="order-status8">
                                            {{translate('messages.Payment Failed')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status9" name="order_status">
                                        <label class="custom-control-label" for="order-status9">
                                            {{translate('messages.Refunded')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status10" name="order_status">
                                        <label class="custom-control-label" for="order-status10">
                                            {{translate('messages.Dine In')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-auto">
                                <div class="form-group m-0">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="order-status11" name="order_status">
                                        <label class="custom-control-label" for="order-status11">
                                            {{translate('messages.Offline Payment')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center offcanvas-footer p-3 position-sticky">
        <button type="button" class="btn w-100 btn--reset offcanvas-close">Reset</button>
        <button type="submit" class="btn w-100 btn--primary">Apply</button>
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

$(document).ready(function () {
    let controller = $(".order-status_controller");
    // All" checkbox change event
    controller.on("change", "#all", function () {
        let isChecked = $(this).prop("checked");
        controller.find(".custom-control-input").not(this).prop("checked", isChecked);
    });

    // single checkbox change event
    controller.on("change", ".custom-control-input:not(#all)", function () {
        let total = controller.find(".custom-control-input:not(#all)").length;
        let checked = controller.find(".custom-control-input:not(#all):checked").length;

        $("#all").prop("checked", total === checked);
    });
});

</script>
@endpush