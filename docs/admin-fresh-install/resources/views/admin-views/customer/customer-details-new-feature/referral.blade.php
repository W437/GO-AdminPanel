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
                <a class="nav-link " href="javascript:void(0)">Coupon</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0)">Referral Summary</a>
            </li>
        </ul>
    </div>
    <div class="card mb-10px">
        <div class="card-body p-10px">
            <div class="row g-1">                                
                <div class="col-sm-6 col-md-6 col-lg-3">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg3" href="javascript:void(0)">
                        <div class="find-copy-text d-flex gap-3 justify-content-between">                           
                            <div>
                                <h3 class="copy-this text-danger font-bold mb-1 align-items-center">
                                    H9FJ8F7KJ
                                </h3>
                                <span class="fs-14 font-normal text-title">Reefer Code</span>
                            </div>
                            <button type="button" class="btn p-0 w-30px h-30px d-center bg-white rounded-circle text-primary copy-btn">
                                <i class="tio-copy"></i>
                            </button>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg1" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">                            
                            <div>
                                <h3 class="font-bold mb-1 align-items-center" data-text-color="#3C76F1">
                                  20
                                </h3>
                                <span class="fs-14 font-normal text-title">Total Referred	</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg4" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">                            
                            <div>
                                <h3 class="font-bold mb-1 align-items-center" data-text-color="#E6A832">
                                  6
                                </h3>
                                <span class="fs-14 font-normal text-title">Joined via Code	</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3">
                    <a class="order--card justify-content-start gap-3 h-100 card-bg2" href="javascript:void(0)">
                        <div class="d-flex align-items-center gap-3">                            
                            <div>
                                <h3 class="font-bold mb-1 align-items-center" data-text-color="#019463">
                                  $15.00
                                </h3>
                                <span class="fs-14 font-normal text-title">Referral Earned	</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header py-xl-20 flex-wrap gap-2 border-0">
            <h5 class="card-header-title">{{ translate('messages.Referred Users') }}
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
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Name')}}</th>
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Joined Date')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.Orders')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-right">{{translate('messages.Order Value')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.Status')}}</th>                
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="text-title">
                               Rifat Hossain	
                            </div>
                        </td>
                        <td>
                            <div class="text-title">
                                28 Dec 2024 
                            </div>
                            <div class="text-title">
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">
                                $88.50
                            </div>
                       </td>
                        <td class="text-center">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>
                            <div class="text-title">
                               Rifat Hossain	
                            </div>
                        </td>
                        <td>
                            <div class="text-title">
                                28 Dec 2024 
                            </div>
                            <div class="text-title">
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">
                                $88.50
                            </div>
                       </td>
                        <td class="text-center">
                            <span class="badge badge-soft-danger min-w--50 px-2">
                                Inactive
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>
                            <div class="text-title">
                               Rifat Hossain	
                            </div>
                        </td>
                        <td>
                            <div class="text-title">
                                28 Dec 2024 
                            </div>
                            <div class="text-title">
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">
                                $88.50
                            </div>
                       </td>
                        <td class="text-center">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>
                            <div class="text-title">
                               Rifat Hossain	
                            </div>
                        </td>
                        <td>
                            <div class="text-title">
                                28 Dec 2024 
                            </div>
                            <div class="text-title">
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">
                                $88.50
                            </div>
                       </td>
                        <td class="text-center">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>
                            <div class="text-title">
                               Rifat Hossain	
                            </div>
                        </td>
                        <td>
                            <div class="text-title">
                                28 Dec 2024 
                            </div>
                            <div class="text-title">
                                11:09 pm
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-title">
                                5
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="text-title">
                                $88.50
                            </div>
                       </td>
                        <td class="text-center">
                            <span class="badge badge-soft-success min-w--50 px-2">
                                Active
                            </span>
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