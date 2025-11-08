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
                <a class="nav-link " href="javascript:void(0)">Loyalty point</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0)">Coupon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="javascript:void(0)">Referral Summary</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header py-xl-20 flex-wrap gap-2 border-0">
            <h5 class="card-header-title">{{ translate('messages.Coupon History') }}
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
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Code')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Coupon Date Range')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Coupon Type')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Total Uses')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.Status')}}</th>                
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Restaurant')}}</th>                
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-4">1</td>
                        <td class="p-4">
                            <div class="text-title">
                                free
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                11 Jun,2023  to 20 Oct, 2025
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                min purchase $250
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-center p-4">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
                        </td>
                       <td class="p-4">
                            <div class="text-title">
                                All Restaurant
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td class="p-4">2</td>
                        <td class="p-4">
                            <div class="text-title">
                                free
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                11 Jun,2023  to 20 Oct, 2025
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                Free Delivery
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-center p-4">
                            <span class="badge badge-soft-danger min-w--50 px-2">
                                Expired
                            </span>
                        </td>
                       <td class="p-4">
                            <div class="text-title">
                                Hungry Puppetsets
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td class="p-4">3</td>
                        <td class="p-4">
                            <div class="text-title">
                                free20
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                11 Jun,2023  to 20 Oct, 2025
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                20% Off
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-center p-4">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
                        </td>
                       <td class="p-4">
                            <div class="text-title">
                                All Restaurant
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td class="p-4">4</td>
                        <td class="p-4">
                            <div class="text-title">
                                4fae74af-cb8-4ae1
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                11 Jun,2023  to 20 Oct, 2025
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                Free Delivery
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-center p-4">
                            <span class="badge badge-soft-danger min-w--50 px-2">
                                Expired
                            </span>
                        </td>
                       <td class="p-4">
                            <div class="text-title">
                                Hungry Puppetsets
                            </div>
                       </td>
                    </tr>
                    <tr>
                        <td class="p-4">2</td>
                        <td class="p-4">
                            <div class="text-title">
                                free
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                11 Jun,2023  to 20 Oct, 2025
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                Free Delivery
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-center p-4">
                            <span class="badge badge-soft-danger min-w--50 px-2">
                                Expired
                            </span>
                        </td>
                       <td class="p-4">
                            <div class="text-title">
                                Hungry Puppetsets
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