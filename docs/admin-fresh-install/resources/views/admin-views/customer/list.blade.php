@extends('layouts.admin.app')

@section('title', translate('Customer_list'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Customer Details for 8.5 Start -->
        {{-- <div class="page-header">
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
                    <a class="nav-link active" href="javascript:void(0)">Overview</a>
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
                    <a class="nav-link " href="javascript:void(0)">Referral Summary</a>
                </li>
            </ul>
        </div>
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="single-customer-leftside-bar">
                    <div class="row g-2 mb-2">
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20">
                                <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                    <img width="22" src="{{dynamicAsset('/public/assets/admin/img/total-spent-new.png')}}" alt="img">
                                </div>
                                <div>
                                    <span class="text-gray fs-12 lh-1"> Total Spent</span>
                                    <h5 class="text-color m-0">$2.90 M</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20">
                                <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                    <img width="22" src="{{dynamicAsset('/public/assets/admin/img/last-purchase.png')}}" alt="img">
                                </div>
                                <div>
                                    <span class="text-gray fs-12 lh-1"> Last Purchase</span>
                                    <h5 class="text-color m-0">5 July , 2025</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20">
                                <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                    <img width="22" src="{{dynamicAsset('/public/assets/admin/img/order-value.png')}}" alt="img">
                                </div>
                                <div>
                                    <span class="text-gray fs-12 lh-1">  Avg. Order Value</span>
                                    <h5 class="text-color m-0">$20.00</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20">
                                <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                    <img width="22" src="{{dynamicAsset('/public/assets/admin/img/order-price-range.png')}}" alt="img">
                                </div>
                                <div>
                                    <span class="text-gray fs-12 lh-1"> Order price range</span>
                                    <h5 class="text-color m-0">$10.00 - $ 250.00</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20 h-100">
                                <div class="flex-grow-1 global-bg-box py-2 px-2 rounded h-100">
                                    <span class="text-gray fs-12 mb-2 d-block">Most Ordered Items</span>
                                    <div class="d-flex align-items-center gap-1 justify-content-between">
                                        <h4 class="theme-clr m-0">145+</h4>
                                        <a href="javascript:void(0)" class="fs-12 theme-clr font-medium offcanvas-trigger" data-target="#order_items_most">View List</a>
                                    </div>
                                </div>
                                <div class="w-60px global-bg-box py-2 px-2 rounded d-center text-center h-100">
                                    <div>
                                        <h5 class="theme-clr m-0">1.2k</h5>
                                        <span class="text-gray fs-10 d-block">Orders</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="global-shadow bg-white gap-10 d-flex rounded-8 gap-2 p--20 h-100">
                                <div class="flex-grow-1 global-bg-box py-2 px-2 rounded h-100">
                                    <span class="text-gray fs-12 mb-2 d-block">Most Ordered Restaurants</span>
                                    <div class="d-flex align-items-center gap-1 justify-content-between">
                                        <h4 class="theme-clr m-0">130+</h4>
                                        <a href="javascript:void(0)" class="fs-12 theme-clr font-medium offcanvas-trigger" data-target="#order_restaurants">View List</a>
                                    </div>
                                </div>
                                <div class="w-60px global-bg-box py-2 px-2 rounded d-center text-center h-100">
                                    <div>
                                        <h5 class="theme-clr m-0">630</h5>
                                        <span class="text-gray fs-10 d-block">Orders</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-15 global-shadow bg-white rounded-10">
                        <div class="row g-2">
                            <div class="col-lg-3 col-md-4">
                                <div class="global-shadow text-center global-bg-box gap-10 d-flex flex-column align-items-center justify-content-center rounded-8 gap-2 p--20 h-100">
                                    <div class="mb-4">
                                        <img width="35" src="{{dynamicAsset('/public/assets/admin/img/total-box-list.png')}}" alt="img">
                                    </div>
                                    <div>
                                        <h3 class="m-0" data-text-color="#3C76F1">2,500</h3>
                                        <span class="text-gray fs-14 lh-1"> Total Orders</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-8">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="global-shadow global-bg-box gap-10 d-flex rounded-8 gap-2 p-15">
                                            <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                                <img width="22" src="{{dynamicAsset('/public/assets/admin/img/delivery-new-box.png')}}" alt="img">
                                            </div>
                                            <div>
                                                <h5 class="m-0" data-text-color="#019463">200</h5>
                                                <span class="text-gray fs-12 lh-1"> Delivered</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="global-shadow global-bg-box gap-10 d-flex rounded-8 gap-2 p-15">
                                            <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                                <img width="22" src="{{dynamicAsset('/public/assets/admin/img/ongoing-new-box.png')}}" alt="img">
                                            </div>
                                            <div>
                                                <h5 class="m-0" data-text-color="#E6A832">200</h5>
                                                <span class="text-gray fs-12 lh-1"> Ongoing</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="global-shadow global-bg-box gap-10 d-flex rounded-8 gap-2 p-15">
                                            <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                                <img width="22" src="{{dynamicAsset('/public/assets/admin/img/cancel-new-box.png')}}" alt="img">
                                            </div>
                                            <div>
                                                <h5 class="m-0" data-text-color="#C33D3D">200</h5>
                                                <span class="text-gray fs-12 lh-1"> Canceled</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="global-shadow global-bg-box gap-10 d-flex rounded-8 gap-2 p-15">
                                            <div class="w-40 min-w-40 h-40 rounded-circle d-center bg-white">
                                                <img width="22" src="{{dynamicAsset('/public/assets/admin/img/refund-new.png')}}" alt="img">
                                            </div>
                                            <div>
                                                <h5 class="m-0" data-text-color="#FF4040">200</h5>
                                                <span class="text-gray fs-12 lh-1"> Refunded </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="single-customer-rightside-bar bg-white global-shadow rounded-8 p-15">
                    <div class="dropdown">
                        <button class="btn border-0 p-0 bg-transparent lh-1 fs-18 position-absolute right-0 top-0" type="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="tio-more-horizontal lh-1"></i>
                        </button>
                        <div class="dropdown-menu me-3">
                            <div class="dropdown-item d-flex align-items-center gap-2 fs-14 text-gray cursor-pointer">
                                <i class="tio-edit theme-clr"></i> Edit Details
                            </div>
                            <div class="dropdown-item d-flex align-items-center gap-2 fs-14 text-gray cursor-pointer">
                                <i class="tio-wallet theme-clr"></i> Add Fund
                            </div>
                            <div class="dropdown-item d-flex align-items-center gap-2 fs-14 text-gray cursor-pointer offcanvas-trigger" data-target="#saved-address_offcanvas">
                                <i class="tio-poi theme-clr"></i> Add Address
                            </div>
                            <div class="not-closed mt-2 px-3 d-flex justify-content-between align-items-center gap-2 fs-14 text-gray cursor-pointer">
                                <label class="toggle-switch toggle-switch-xs d-flex justify-content-between w-100">
                                    <span class="pr-1 d-flex align-items-center switch--label">
                                        <span class="line--limit-1">
                                            Active
                                        </span>
                                    </span>
                                    <input type="checkbox" class="status toggle-switch-input" value="1" name="" id="" checked="">
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-20">
                        <div class="text-center mb-3 rounded-circle">
                            <img width="60" height="60" src="{{dynamicAsset('/public/assets/admin/img/customer-profile.png')}}" alt="img" class="rounded-circle">
                        </div>
                        <div class="mb-15 text-center">
                            <h4 class="text-color mb-1 lh-1">Jhone Ali</h4>
                            <span class="text-gray fs-12">Joined on July 7, 2025</span>
                        </div>
                        <div class="social-media-group gap-3 d-flex align-items-center justify-content-center">
                            <a href="javascript:void(0)" class="d-center rounded-10">
                                <i class="tio-messages"></i>
                            </a>
                            <a href="javascript:void(0)" class="d-center rounded-10">
                                <i class="tio-sms"></i>
                            </a>
                            <a href="javascript:void(0)" class="d-center rounded-10">
                                <i class="tio-call"></i>
                            </a>
                        </div>
                    </div>
                    <div class="global-bg-box p-15 d-flex flex-column gap-2 mb-20">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-12 before-info text-gray w-60px">Phone</span>
                            <span class="fs-12 text-color">+8801798654352</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-12 before-info text-gray w-60px">Email</span>
                            <span class="fs-12 text-color">jhoneali@example.com</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-12 before-info text-gray w-60px">Address</span>
                            <div class="d-flex align-items-center gap-1 cursor-pointer">
                                <span class="fs-12 theme-clr offcanvas-trigger" data-target="#saved-address_offcanvas">2 Address saved <i class="tio-chevron-right"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-10">
                        <div class="d-flex align-items-center rounded-8 justify-content-between gap-2 p-15" data-bg-color="#F0FFF3">
                           <div>
                                <h4 class="fs-16 m-0" data-text-color="#019463">$200.00</h4>
                                <span class="fs-12" data-text-color="#656566">
                                    Wallet Balance
                                </span>
                           </div>
                            <img width="38" height="38" src="{{dynamicAsset('/public/assets/admin/img/new-wallet.png')}}" alt="img">
                        </div>
                        <div class="d-flex align-items-center rounded-8 justify-content-between gap-2 p-15" data-bg-color="#FFF9F0">
                           <div>
                                <h4 class="fs-16 m-0" data-text-color="#FFBB38">$200.00</h4>
                                <span class="fs-12" data-text-color="#656566">
                                    Loyalty point
                                </span>
                           </div>
                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/new--star.png')}}" alt="img">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Customer Details for 8.5 Start -->

        {{-- @include('admin-views.customer.customer-details-new-feature.order-list') --}}
        {{--@include('admin-views.customer.customer-details-new-feature.wishlist')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.rating-reviews')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.wallet')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.loyality-point')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.coupon')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.referral')--}}
        {{--@include('admin-views.customer.customer-details-new-feature.customer-overview-report')--}}


        <!-- Old Code Here,,, -->
        <div>
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm">
                    <h1 class="page-header-title">
                        <span class="page-header-icon"><i class="tio-group-equal"></i></span>
                        {{ translate('messages.customers') }}
                    </h1>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Order Date') }}</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" data-startDate="09/04/2024" data-endDate="09/24/2024" readonly
                                    name="order_date" value="{{ request()->get('order_date') ?? null }}"
                                    class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Customer Joining Date') }}</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" readonly name="join_date"
                                    value="{{ request()->get('join_date') ?? null }}"
                                    class="date-range-picker form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Customer status') }}</label>
                            <select name="filter" data-placeholder="{{ translate('messages.Select_Status') }}"
                                class="form-control js-select2-custom ">
                                <option value="" selected disabled> {{ translate('messages.Select_Status') }}
                                </option>
                                <option {{ request()->get('filter') == 'all' ? 'selected' : '' }} value="all">
                                    {{ translate('messages.All_Customers') }}</option>
                                <option {{ request()->get('filter') == 'active' ? 'selected' : '' }} value="active">
                                    {{ translate('messages.Active_Customers') }}</option>
                                <option {{ request()->get('filter') == 'blocked' ? 'selected' : '' }} value="blocked">
                                    {{ translate('messages.Inactive_Customers') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Sort By') }}</label>
                            <select name="order_wise"
                                data-placeholder="{{ translate('messages.Select Customer Sorting Order') }}"
                                class="form-control js-select2-custom">
                                <option value="" selected disabled>
                                    {{ translate('messages.Select Customer Sorting Order') }} </option>
                                <option {{ request()->get('order_wise') == 'top' ? 'selected' : '' }} value="top">
                                    {{ translate('messages.Sort by order count') }}</option>
                                <option {{ request()->get('order_wise') == 'order_amount' ? 'selected' : '' }}
                                    value="order_amount">{{ translate('messages.Sort by order amount') }}</option>
                                <option {{ request()->get('order_wise') == 'oldest' ? 'selected' : '' }} value="oldest">
                                    {{ translate('messages.Sort by oldest') }}</option>
                                <option {{ request()->get('order_wise') == 'latest' ? 'selected' : '' }} value="latest">
                                    {{ translate('messages.Sort by newest') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Choose First') }}</label>
                            <input type="number" min="1" name="show_limit" class="form-control"
                                value="{{ request()->get('show_limit') }}" placeholder="{{ translate('Ex : 100') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="d-md-block">&nbsp;</label>
                            <div class="btn--container justify-content-end">
                                <button type="submit" class="btn btn--primary">{{ translate('Filter') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Card -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header gap-2 flex-wrap pt-3 border-0">
                <h3 class="m-0">
                    {{ translate('messages.customer_list') }} <span class="badge badge-soft-dark ml-2"
                        id="count">{{ $customers->total() }}</span>
                </h3>
                <div class="search--button-wrapper justify-content-end">
                    <form>
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                value="{{ request()?->search ?? null }}"
                                placeholder="{{ translate('Ex:_Search_by_name') }}" aria-label="Search" required>
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <div class="d-flex flex-wrap justify-content-sm-end align-items-sm-center ml-0 mr-0">


                        <!-- Unfold -->
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle" href="javascript:;"
                                data-hs-unfold-options='{
                                    "target": "#usersExportDropdown",
                                    "type": "css-animation"
                                }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('admin.customer.export', ['type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item"
                                    href="{{ route('admin.customer.export', ['type' => 'csv', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                        alt="Image Description">
                                    .{{ translate('messages.csv') }}
                                </a>
                            </div>
                        </div>
                        <!-- End Unfold -->
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "paging":false
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">
                                {{ translate('sl') }}
                            </th>
                            <th class="table-column-pl-0 border-0">{{ translate('messages.name') }}</th>
                            <th class="border-0">{{ translate('messages.contact_information') }}</th>
                            <th class="border-0">{{ translate('messages.total_order') }}</th>
                            <th class="border-0">{{ translate('messages.total_order_amount') }}</th>
                            <th class="border-0">{{ translate('messages.Joining_date') }}</th>
                            <th class="border-0">
                                {{ translate('messages.active') }}/{{ translate('messages.inactive') }}</th>
                            <th class="border-0">{{ translate('messages.actions') }}</th>
                        </tr>
                    </thead>
                    @php
                        $count = 0;
                    @endphp
                    <tbody id="set-rows">
                        @foreach ($customers as $key => $customer)
                            <tr class="">
                                <td class="">
                                    {{ (request()->get('show_limit') ? $count++ : $key) + $customers->firstItem() }}
                                </td>
                                <td class="table-column-pl-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="rounded aspect-1-1 object-cover" width="40"
                                            data-onerror-image="{{ dynamicAsset('public/assets/admin/img/160x160/img1.jpg') }}"
                                            src="{{ $customer->image_full_url }}" alt="Image Description">
                                        <a href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                            class="text-body text-hover-primary">
                                            {{  $customer['f_name']?  $customer['f_name'] . ' ' . $customer['l_name']  : translate('Incomplete_profile') }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <a href="mailto:{{ $customer['email'] }}" class="text-body text-hover-primary">
                                            {{ $customer['email'] }}
                                        </a>
                                    </div>
                                    <div>
                                        <a href="tel:{{ $customer['phone'] }}" class="text-body text-hover-primary">
                                            {{ $customer['phone'] }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <label class="badge">
                                        {{ $customer->orders_count }}
                                    </label>
                                </td>
                                <td>
                                    <label class="badge">
                                        {{ \App\CentralLogics\Helpers::format_currency($customer->total_order_amount) }}
                                    </label>
                                </td>
                                <td>
                                    <label class="badge">
                                        {{ \App\CentralLogics\Helpers::date_format($customer->created_at) }}
                                    </label>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm ml-xl-4"
                                        for="stocksCheckbox{{ $customer->id }}">
                                        <input type="checkbox"
                                            data-url="{{ route('admin.customer.status', [$customer->id, $customer->status ? 0 : 1]) }}"
                                            data-message="{{ $customer->status ? translate('messages.you_want_to_block_this_customer') : translate('messages.you_want_to_unblock_this_customer') }}"
                                            class="toggle-switch-input status_change_alert"
                                            id="stocksCheckbox{{ $customer->id }}"
                                            {{ $customer->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <a class="btn action-btn btn--warning btn-outline-warning"
                                        href="{{ route('admin.customer.view', [$customer['id']]) }}"
                                        title="{{ translate('messages.view_customer') }}"><i
                                            class="tio-visible-outlined"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (count($customers) === 0)
                <div class="empty--data">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
            <!-- End Table -->
            <div class="page-area px-4 pb-3">
                <div class="d-flex align-items-center justify-content-end">
                    <div>
                        {!! $customers->withQueryString()->links() !!}
                    </div>
                </div>
            </div>
            <!-- End Footer -->

        </div>
        <!-- End Card -->
        </div>



    </div>

    {{-- <!-- Saved Address Offcanvas -->
    <div id="saved-address_offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between" style="--offcanvas-width: 500px">
        <div>
            <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
                <div class="px-3 py-3 d-flex justify-content-between w-100">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Saved Address') }}</h2>
                        <h3 class="page-header-title bg-white rounded-8 px-2 py-1 d-flex align-items-center gap-2">
                        </h3>
                    </div>
                    <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="d-flex flex-column gap-20px">
                    <div class="global-bg-box p-10px rounded">
                        <div class="d-flex align-items-cetner justify-content-between gap-2 flex-wrap mb-10px">
                            <h5 class="text-title m-0">
                                Home
                                <span class="gray-dark">(Shipping Address)</span>
                            </h5>
                            <button type="button" class="btn p-0 bg-transparent text-primary" data-toggle="modal"
                                            data-target="#addressEdit__modal">
                                <i class="tio-edit"></i>
                            </button>
                        </div>
                        <div class="bg-white rounded p-10px d-flex flex-column gap-1">
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Name
                                </span>
                                <span class="fs-12 text-title">
                                    Bruce
                                    (+98453324823)
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Email
                                </span>
                                <span class="fs-12 text-title">
                                    demo@example.com
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Address Type
                                </span>
                                <span class="fs-12 text-title">
                                   Permanent
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Country
                                </span>
                                <span class="fs-12 text-title">
                                    USA
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    City
                                </span>
                                <span class="fs-12 text-title">
                                    California
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Zip Code
                                </span>
                                <span class="fs-12 text-title">
                                    325648
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info align-items-start w-90px min-w-90 gray-dark fs-12">
                                    Address
                                </span>
                                <span class="fs-12 text-title">
                                    2715 Ash Dr. San Jose, South Dakota 2715 Ash Dr. San Jose, South Dakota 83475
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="global-bg-box p-10px rounded">
                        <div class="d-flex align-items-cetner justify-content-between gap-2 flex-wrap mb-10px">
                            <h5 class="text-title m-0">
                                Office
                                <span class="gray-dark">(Shipping Address)</span>
                            </h5>
                            <button type="button" class="btn p-0 bg-transparent text-primary" data-toggle="modal"
                                            data-target="#addressEdit__modal">
                                <i class="tio-edit"></i>
                            </button>
                        </div>
                        <div class="bg-white rounded p-10px d-flex flex-column gap-1">
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Name
                                </span>
                                <span class="fs-12 text-title">
                                    Bruce
                                    (+98453324823)
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info w-90px min-w-90 gray-dark fs-12">
                                    Email
                                </span>
                                <span class="fs-12 text-title">
                                    demo@example.com
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="before-info align-items-start w-90px min-w-90 gray-dark fs-12">
                                    Address
                                </span>
                                <span class="fs-12 text-title">
                                    2715 Ash Dr. San Jose, South Dakota 2715 Ash Dr. San Jose, South Dakota 83475
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center offcanvas-footer p-3 position-sticky">
            <button type="submit" class="btn w-100 btn--primary">Add New Address</button>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>


    <!-- Most Order Items -->
    <div id="order_items_most" class="custom-offcanvas d-flex flex-column justify-content-between" style="--offcanvas-width: 500px">
        <div>
            <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
                <div class="px-3 py-3 d-flex justify-content-between w-100">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Most Ordered Items') }}</h2>
                        <h3 class="page-header-title bg-white rounded-8 px-2 py-1 d-flex align-items-center gap-2">
                            <span class="font--max-sm fs-14 font-normal fs-14">148</span>
                        </h3>
                    </div>
                    <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="d-flex flex-column gap-20px">
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
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

    <!-- Most Order Items -->
    <div id="order_restaurants" class="custom-offcanvas d-flex flex-column justify-content-between" style="--offcanvas-width: 500px">
        <div>
            <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
                <div class="px-3 py-3 d-flex justify-content-between w-100">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Most Ordered Restaurants') }}</h2>
                        <h3 class="page-header-title bg-white rounded-8 px-2 py-1 d-flex align-items-center gap-2">
                            <span class="font--max-sm fs-14 font-normal fs-14">33</span>
                        </h3>
                    </div>
                    <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="d-flex flex-column gap-20px">
                    <a href="javascript:void(0)" class="d-flex align-items-sm-center gap-10 global-bg-box rounded py-sm-3 py-2 px-xxl-4 px-3">
                        <div class="w-48">
                            <img width="48" height="48" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded">
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                            <div>
                                <h5 class="mb-0 font-normal text-color">Food Fair</h5>
                            </div>
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
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
                            <h5 class="m-0 fs-12 font-regular text-gray">Ordered 20 times</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

    <!--- Address Edit Modal --->
    <div class="modal fade" id="addressEdit__modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-20">
                <div class="modal-header cmn__quick p-0">
                    <button type="button" class="close w-35px h-35px min-h-35px clear-when-done" data-dismiss="modal" aria-label="Close">
                        <span class="top-0 m-0" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0 pb-0">
                    <h3 class="modal-title flex-grow-1">{{ translate('Edit Address - Home') }}</h3>
                    <?php
                    $address = isset($address) ? $address : null;
                    ?>
                    <form action="#0" class="mt-lg-4 mt-3">
                        @csrf
                        <input type="hidden" name="address_id" value="{{ $address ? data_get($address, 'id') : '' }}">
                        <div>
                            <div class="bg-light rounded-10 p-sm-3 p-2 mb-20">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="" class="input-label" for="">
                                            {{ translate('messages.contact_person_name') }}
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input id="" type="text" class="form-control" name="" value="" placeholder="{{ translate('Ex: ,,,') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="edit-address-country">
                                            <label for="contact_person_number" class="input-label" for="">
                                                {{ translate('Phone number') }}
                                                <span class="input-label-secondary text-danger">*</span>
                                            </label>
                                            <input id="contact_person_number" type="tel" class="form-control" name="contact_person_number" value=""
                                                placeholder="{{ translate('Ex: +3264124565') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="input-label" for="">
                                            {{ translate('messages.Country') }}
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input id="" type="text" class="form-control" name="" value="" placeholder="{{ translate('Ex: ') }}">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label for="" class="input-label" for="">
                                            {{ translate('messages.City') }}
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input id="" type="text" class="form-control" name="" value="" placeholder="{{ translate('Ex: ') }}">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label for="" class="input-label" for="">
                                            {{ translate('messages.Zip') }}
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input id="" type="text" class="form-control" name="" value="" placeholder="{{ translate('Ex: ') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="address" class="input-label" for="">
                                            {{ translate('messages.Description') }}
                                        </label>
                                        <textarea id="" name="description" class="form-control" cols="30" rows="3"  placeholder="{{ translate('Ex: ') }}"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="maps position-relative">
                                <input id="pac-input" class="controls rounded overflow-hidden border-0 pac-input-Controls"
                                    title="{{ translate('messages.search_your_location_here') }}" type="text"
                                    placeholder="{{ translate('messages.search_here') }}" />
                                <div id="location_map_canvas" class="overflow-hidden rounded height-285px"></div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer border-0 py-3">
                    <div class="btn--container justify-content-end">
                        <button type="button" class="btn min-w-120 clear-when-done btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn min-w-120 btn-sm btn--primary delivery-Address-Store" type="button">
                            {{  translate('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

@endsection

@push('script_2')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places&callback=initMap"
        async defer>
    </script>
    <script>
        function initMap() {
            // Default location (Dhaka as example)
            var defaultLocation = { lat: 23.8103, lng: 90.4125 };

            // Initialize map
            var map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                center: defaultLocation,
                zoom: 13
            });

            // Add search input box to the map controls
            var input = document.getElementById("pac-input");
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Initialize SearchBox
            var searchBox = new google.maps.places.SearchBox(input);
        }
    </script>
    <script>
        "use strict";
        $('.status_change_alert').on('click', function(event) {
            let url = $(this).data('url');
            let message = $(this).data('message');
            status_change_alert(url, message, event)
        })

        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('Are_you_sure_?') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('no') }}',
                confirmButtonText: '{{ translate('yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
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


            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none'
                    },
                    {
                        extend: 'csv',
                        className: 'd-none'
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none',
                        customize: function(doc) {
                            doc.content[1].table.body.forEach(row => {
                                row.splice(4, 3);
                            });
                        }
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<img class="mb-3 w-7rem" src="{{ dynamicAsset('public/assets/admin') }}/svg/illustrations/sorry.svg" alt="Image Description">' +
                        '<p class="mb-0">{{ translate('No_data_to_show') }}</p>' +
                        '</div>'
                }
            });


            $('#datatableSearch').on('mouseup', function(e) {
                let $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function() {
                    let newValue = $input.val();

                    if (newValue == "") {
                        // Gotcha
                        datatable.search('').draw();
                    }
                }, 1);
            });

            $('#toggleColumn_name').change(function(e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_email').change(function(e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_total_order').change(function(e) {
                datatable.columns(3).visible(e.target.checked)
            })

            $('#toggleColumn_status').change(function(e) {
                datatable.columns(4).visible(e.target.checked)
            })

            $('#toggleColumn_actions').change(function(e) {
                datatable.columns(5).visible(e.target.checked)
            })

            // INITIALIZATION OF TAGIFY
            // =======================================================
            $('.js-tagify').each(function() {
                let tagify = $.HSCore.components.HSTagify.init($(this));
            });
        });
    </script>

@endpush
