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
                    {{ translate('messages.Customer Overview Report') }} 
                </h1>
            </div>
        </div>
    </div>
    <div class="mb-2">
        <div class="row g-2">
            <div class="col-lg-6">
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="h-100 customer__card">
                            <div class="card-body">
                                <div class="d-flex flex-sm-column gap-lg-20 gap--16">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#9F62DD">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/total-spent.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">$524.90</h2>
                                        <h5 class="m-0 font-regular">Total Spent</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="h-100 customer__card">
                            <div class="card-body">
                                <div class="d-flex flex-sm-column gap-lg-20 gap--16">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#FFA654">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/order-done.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">54,515</h2>
                                        <h5 class="m-0 font-regular">Total Orders</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="h-100 customer__card">
                            <div class="card-body d-flex flex-column gap-lg-20 gap--16">
                                <div class="d-flex align-items-center gap-xxl-16 gap--10">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#0177CD">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/customer-group.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">21,500</h2>
                                        <h5 class="m-0 font-regular">Total Customers</h5>
                                    </div>
                                </div>
                                <div class="border-bottom"></div>
                                <div class="d-flex align-items-center gap-xxl-16 gap--10">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#14B19E">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/new-customer.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">1200</h2>
                                        <h5 class="m-0 font-regular">New Customers </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="h-100 customer__card">
                            <div class="card-body d-flex flex-column gap-lg-20 gap--16">
                                <div class="d-flex align-items-center gap-xxl-16 gap--10">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#7FD9B9">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/order-list-value.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">$ 80.00</h2>
                                        <h5 class="m-0 font-regular"> Avg. Order Value</h5>
                                    </div>
                                </div>
                                <div class="border-bottom"></div>
                                <div class="d-flex align-items-center gap-xxl-16 gap--10">
                                    <div class="icon w-48 h-48 min-w-48" data-bg-color="#EF5B5B">
                                        <img width="24" height="24" src="{{dynamicAsset('/public/assets/admin/img/customer-report/last-order.png')}}" alt="img" class="object--contain">
                                    </div>
                                    <div>
                                        <h2 class="mb-1 fs-24 text-title">12 min ago</h2>
                                        <h5 class="m-0 font-regular"> Last Order Placed </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-1">
                            <h4 class="m-0 text-title">Order Statistics </h4>
                            <select name="chart-overview" id="" class="custom-select w-auto">
                                <option value="">
                                    Overall
                                </option>
                                <option value="">
                                    This Year
                                </option>
                                <option value="">
                                    This Month
                                </option>
                                <option value="">
                                    This Week
                                </option>
                                <option value="">
                                    Today
                                </option>
                            </select>
                        </div>
                        <div class="customer-overview-chart">
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <div class="row g-2">
            <div class="col-lg-6 col-xl-7 col-xxl-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-sm-4 mb-3">
                            <h4 class="m-0 text-title">Customer Joining  Statistics  </h4>
                            <select name="chart-overview" id="" class="custom-select w-auto">
                                <option value="">
                                    Yearly 
                                </option>
                                <option value="">
                                    This Year
                                </option>
                                <option value="">
                                    This Month
                                </option>
                                <option value="">
                                    This Week
                                </option>
                                <option value="">
                                    Today
                                </option>
                            </select>
                        </div>
                        <div id="chart2"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-5 col-xxl-4">
                <div class="h-100 card">
                    <div class="card-header border-0 p-3 top-customer-header">
                        <h4 class="text-title m-0">Top Customers</h4>
                    </div>
                    <div class="card-body p-3">                        
                        <div class="row g-2">
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 229</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 220</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 210</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 230</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 100</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-6">                            
                                <div class="top-customer-card text-center">
                                    <a href="javascript:void(0)" class="d-flex flex-column gap-1 align-items-center">
                                        <div class="w-40 h-40 rounded-circle">
                                            <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                        </div>
                                        <div class="mb-1">
                                            <div class="mb-1 fs-14 text-title">Jhone Doe</div>
                                            <h5 class="m-0 font-semibold text--primary rounded py-1 px-2 primary-border-opacity5">Orders : 190</h5>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header py-xl-20 flex-wrap gap-2 border-0">
            <h5 class="card-header-title">{{ translate('messages.Customer List') }}</h5>
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
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Customer info')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.Total Order')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-right">{{translate('messages.Total Spent')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-right">{{translate('messages.AOV')}}</th>
                        <th class="py-3 fs-14 text-capitalize text-center">{{translate('messages.Last Purchase')}}</th>                
                        <th class="py-3 fs-14 text-capitalize">{{translate('messages.Joining Date')}}</th>
                    </tr>
                </thead>
                <tbody class="space-32">
                    <tr>
                        <td>1</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40 rounded-circle">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Robert Henri</h5>
                                        <span class="fs-12 gray-dark">Abcd@gmail.com</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                            100
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 1,402.49
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 20.00
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                           2 days ago
                        </td>
                        <td>
                           <div class="text-uppercase text-title fs-14">
                                28 Dec 2024 
                           </div>
                           <div class="text-uppercase text-title fs-14">                                
                                11:09 pm
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40 rounded-circle">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Robert Henri</h5>
                                        <span class="fs-12 gray-dark">Abcd@gmail.com</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                            100
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 1,402.49
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 20.00
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                           2 days ago
                        </td>
                        <td>
                           <div class="text-uppercase text-title fs-14">
                                28 Dec 2024 
                           </div>
                           <div class="text-uppercase text-title fs-14">                                
                                11:09 pm
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40 rounded-circle">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Robert Henri</h5>
                                        <span class="fs-12 gray-dark">Abcd@gmail.com</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                            100
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 1,402.49
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 20.00
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                           2 days ago
                        </td>
                        <td>
                           <div class="text-uppercase text-title fs-14">
                                28 Dec 2024 
                           </div>
                           <div class="text-uppercase text-title fs-14">                                
                                11:09 pm
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40 rounded-circle">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Robert Henri</h5>
                                        <span class="fs-12 gray-dark">Abcd@gmail.com</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                            100
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 1,402.49
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 20.00
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                           2 days ago
                        </td>
                        <td>
                           <div class="text-uppercase text-title fs-14">
                                28 Dec 2024 
                           </div>
                           <div class="text-uppercase text-title fs-14">                                
                                11:09 pm
                           </div>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>
                            <a href="javascript:void(0)" class="d-flex text-dark align-items-sm-center gap-10">
                                <div class="w-40 min-w-40 rounded-circle">
                                    <img width="40" height="40" src="{{dynamicAsset('/public/assets/admin/img/160x160/img2.jpg')}}" alt="img" class="rounded-circle">
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 flex-grow-1">
                                    <div>
                                        <h5 class="mb-0 font-normal text-color font-medium">Robert Henri</h5>
                                        <span class="fs-12 gray-dark">Abcd@gmail.com</span>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                            100
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 1,402.49
                        </td>
                        <td class="text-uppercase text-title text-right fs-14">
                            $ 20.00
                        </td>
                        <td class="text-uppercase text-title text-center fs-14">
                           2 days ago
                        </td>
                        <td>
                           <div class="text-uppercase text-title fs-14">
                                28 Dec 2024 
                           </div>
                           <div class="text-uppercase text-title fs-14">                                
                                11:09 pm
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

<script src="{{dynamicAsset('public/assets/admin/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/view-pages/apex-charts.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
            series: [23442, 3812, 2730, 10903, 13628], // 👉 your real values
            labels: ['Delivered', 'Canceled', 'Returned', 'Pending', 'Ongoing'],
            chart: {
                type: 'donut',
                height: 315
            },
            colors: ['#04BB7B', '#FF4040', '#C368EE', '#3C76F1', '#FFBB38'], // match Google-style colors
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Order',
                                fontSize: '16px',
                                fontWeight: 600,
                                color: '#333',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '14px',
                labels: {
                    colors: '#333'
                },
                markers: {
                    radius: 12
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0) + "%"; // show % inside
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString();
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    });
</script>
<!-- Customer Joining Chart -->
<script>
    var options = {
          series: [{
          name: 'Sales',
          data: [4, 3, 10, 9, 29, 19, 22, 9, 12, 7, 19, 5, 13, 9, 17, 2, 7, 5]
        }],
          chart: {
          height: 390,
          type: 'line',
            toolbar: {
                show: false,
            },
        },
        forecastDataPoints: {
          count: 0
        },
        stroke: {
          width: 2,
          curve: 'smooth'
        },
        xaxis: {
          type: 'datetime',
          categories: ['1/11/2000', '2/11/2000', '3/11/2000', '4/11/2000', '5/11/2000', '6/11/2000', '7/11/2000', '8/11/2000', '9/11/2000', '10/11/2000', '11/11/2000', '12/11/2000', '1/11/2001', '2/11/2001', '3/11/2001','4/11/2001' ,'5/11/2001' ,'6/11/2001'],
          tickAmount: 10,
          labels: {
            formatter: function(value, timestamp, opts) {
              return opts.dateFormatter(new Date(timestamp), 'dd MMM')
            }
          }
        },
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'dark',
            gradientToColors: [ '#019463'],
            shadeIntensity: 1,
            type: 'horizontal',
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 0, 0, 0]
          },
        },
        grid: {
            show: true,
            xaxis: {
                lines: {
                    show: true 
                }
            },
            strokeDashArray: 4 
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart2"), options);
    chart.render();
</script>
@endpush