@extends('layouts.admin.app')

@section('title', translate('Food_List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-auto mb-md-0 mb-3 mr-auto">
                    <h1 class="page-header-title"> {{ translate('messages.food_list') }}<span
                            class="badge badge-soft-dark ml-2" id="foodCount">{{ $foods->total() }}</span></h1>
                </div>
                @if ($toggle_veg_non_veg)
                    <div class="col-md-auto mb-3 mb-md-0">
                        <select name="type" data-url="{{ url()->full() }}" data-filter="type"
                            data-placeholder="{{ translate('messages.all') }}" class="form-control set-filter">
                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ translate('messages.all') }}</option>
                            <option value="veg" {{ $type == 'veg' ? 'selected' : '' }}>{{ translate('messages.veg') }}</option>
                            <option value="non_veg" {{ $type == 'non_veg' ? 'selected' : '' }}>{{ translate('messages.non_veg') }}
                            </option>
                        </select>
                    </div>
                @endif
                <div class="col-md-auto mb-3 mb-md-0 min-240">
                    <select name="restaurant_id" id="restaurant"
                            data-url="{{ url()->full() }}" data-filter="restaurant_id"
                        data-placeholder="{{ translate('messages.select_restaurant') }}"
                        class="js-data-example-ajax form-control set-filter" title="Select Restaurant"
                        oninvalid="this.setCustomValidity('{{ translate('messages.please_select_restaurant') }}')">
                        @if ($restaurant)
                            <option value="{{ $restaurant->id }}" selected>{{ $restaurant->name }}</option>
                        @else
                            <option value="all" selected>{{ translate('messages.all_restaurants') }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-auto mb-3 mb-md-0 min-240">
                    <div class="hs-unfold w-100">
                        <select name="category_id" id="category"
                                data-url="{{ url()->full() }}" data-filter="category_id"
                            data-placeholder="{{ translate('messages.select_category') }}"
                            class="js-data-example-ajax form-control set-filter">
                            @if ($category)
                                <option value="{{ $category->id }}" selected>{{ $category->name }}
                                    ({{ $category->position == 0 ? translate('messages.main') : translate('messages.sub') }})
                                </option>
                            @else
                                <option value="all" selected>{{ translate('messages.all_categories') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header border-0 py-2">
                        <!-- Bulk Actions Bar -->
                        <div id="bulk-actions-bar" class="d-none mb-3 p-3 bg-light rounded">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span id="selected-count" class="font-weight-bold">0</span> {{ translate('messages.items_selected') }}
                                </div>
                                <div class="btn--container">
                                    <button type="button" class="btn btn-sm btn--primary" id="bulk-duplicate-btn">
                                        <i class="tio-copy"></i> {{ translate('messages.duplicate') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn--danger" id="bulk-delete-btn">
                                        <i class="tio-delete"></i> {{ translate('messages.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="search--button-wrapper">
                            <h5 class="card-title d-none d-xl-block"></h5>
                            <form id="search-form">


                                <div class="input--group input-group input-group-merge input-group-flush">
                                    <input id="datatableSearch" name="search" type="search" value="{{ request()?->search ?? null }}" class="form-control"
                                        placeholder="{{translate('Search_by_name')}}"
                                        aria-label="{{ translate('messages.search_here') }}">
                                    <button type="submit" class="btn btn--secondary">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                                <!-- End Search -->
                            </form>

                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40" href="javascript:;"
                                    data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                                    <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="{{ route('admin.food.export', ['type' => 'excel', request()->getQueryString()]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="{{ route('admin.food.export', ['type' => 'csv', request()->getQueryString()]) }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>

                                </div>
                            </div>
                            <!-- Unfold -->
                            <!-- Unfold -->

                            <!-- End Unfold -->
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom" id="table-div">
                        <table id="datatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                    "columnDefs": [{
                                        "targets": [],
                                        "width": "5%",
                                        "orderable": false
                                    }],
                                    "order": [],
                                    "info": {
                                    "totalQty": "#datatableWithPaginationInfoTotalQty"
                                    },

                                    "entries": "#datatableEntries",

                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging":false
                                }'>
                            <thead class="thead-light">
                                <tr>
                                    <th class="w-40px">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="datatableCheckAll">
                                            <label class="form-check-label" for="datatableCheckAll"></label>
                                        </div>
                                    </th>
                                    <th class="w-60px">{{ translate('messages.sl') }}</th>
                                    <th class="w-100px">{{ translate('messages.name') }}</th>
                                    <th class="w-120px">{{ translate('messages.category') }}</th>
                                    <th class="w-120px">{{ translate('messages.restaurant') }}</th>
                                    <th class="w-100px">{{ translate('messages.price') }}</th>
                                     @if ($productWiseTax)
                                    <th  class="w-100px">{{ translate('messages.Vat/Tax') }}</th>
                                    @endif
                                    <th class="w-100px">{{ translate('messages.status') }}</th>

                                    <th class="w-120px text-center">
                                        {{ translate('messages.action') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                                @foreach ($foods as $key => $food)
                                @php( $stock_out = null)

                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input food-checkbox" type="checkbox" value="{{ $food->id }}" id="foodCheck{{ $food->id }}">
                                                <label class="form-check-label" for="foodCheck{{ $food->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $key + $foods->firstItem() }}</td>
                                        <td>
                                            <a class="media align-items-center"
                                                href="{{ route('admin.food.view', [$food['id']]) }}">
                                                <img class="avatar avatar-lg mr-3 onerror-image"
                                                     src="{{ $food['image_full_url'] }}"
                                                     alt="{{ $food->name }} image">
                                                <div class="media-body">
                                                    <h5 class="text-hover-primary mb-0">{{ Str::limit($food['name'], 20, '...') }}
                                                        @if ($food->stock_type != 'unlimited' &&  $food->item_stock <= 0 )
                                                        @php( $stock_out = true)

                                                        <span class="badge badge-soft-warning badge-pill font-medium">{{ translate('Out Of Stock') }}</span>
                                                        <span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.Your_main_stock_is_out_of_stock.')}}"><i class="tio-info-outined"></i></span>
                                                        @else

                                                        <?php

                                                            if(isset($food->variations)){
                                                                foreach (json_decode($food->variations,true) as $item) {
                                                                    if (isset($item['values']) && is_array($item['values'])) {
                                                                        foreach ($item['values'] as $value) {
                                                                            if(isset($value['stock_type']) && $value['stock_type'] != 'unlimited' &&   $value['current_stock'] <= 0){
                                                                                $stock_out = true;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            @if($stock_out)
                                                            <span class="input-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('messages.One_or_more_variations_are_out_of_stock.')}}"><i class="tio-info-outined"></i></span>

                                                            @endif
                                                        @endif

                                                    </h5>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            {{ Str::limit(($food?->category?->parent ? $food?->category?->parent?->name : $food?->category?->name )  ?? translate('messages.uncategorize')
                                            , 20, '...') }}
                                        </td>
                                        <td>
                                            @if ($food->restaurant)
                                                <a class="text--title" href="{{route('admin.restaurant.view',['restaurant'=>$food->restaurant_id])}}" title="{{translate('view_restaurant')}}">
                                                    {{ Str::limit($food->restaurant->name, 20, '...') }}
                                                </a>
                                            @else
                                                <span class="text--danger text-capitalize">{{ Str::limit( translate('messages.Restaurant_deleted!'), 20, '...') }}<span>
                                            @endif
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($food['price']) }}</td>

                                            @if ($productWiseTax)
                                            <td>
                                                <span class="">
                                                    @forelse ($food?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $tax)
                                                        <span> {{ $tax }} : <span class="font-bold">
                                                                ({{ $key }}%)
                                                            </span> </span>
                                                        <br>
                                                    @empty
                                                        <span> {{ translate('messages.N/A') }} </span>
                                                    @endforelse
                                                </span>
                                            </td>
                                            @endif


                                        <td>
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="stocksCheckbox{{ $food->id }}">
                                                <input type="checkbox"
                                                    data-url="{{ route('admin.food.status', [$food['id'], $food->status ? 0 : 1]) }}"
                                                    class="toggle-switch-input redirect-url" id="stocksCheckbox{{ $food->id }}"
                                                    {{ $food->status ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                @if($stock_out)
                                                    <a class="btn btn-sm btn--primary btn-outline-primary action-btn " href="#update-stock{{ $food['id'] }}" title="{{ translate('update_stock') }}" data-toggle="modal">
                                                        <i class="tio-autorenew"></i>
                                                    </a>
                                                @endif
                                                    <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                                        href="{{ route('admin.food.edit', [$food['id']]) }}"
                                                        title="{{ translate('messages.edit_food') }}"><i
                                                            class="tio-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn--warning btn-outline-warning action-btn form-alert" href="javascript:"
                                                        data-id="food-{{ $food['id'] }}" data-message="{{ translate('messages.Want_to_delete_this_item') }}"
                                                        title="{{ translate('messages.delete_food') }}"><i
                                                            class="tio-delete-outlined"></i>
                                                    </a>

                                            </div>
                                            <form action="{{ route('admin.food.delete', [$food['id']]) }}" method="post"
                                                id="food-{{ $food['id'] }}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>




                                    <div class="modal fade" id="update-stock{{ $food['id'] }}">
                                        <div class="modal-dialog max-w-450px">
                                            <div class="modal-content">
                                                <div class="modal-header px-2 pt-2">
                                                    <div></div>
                                                    <button type="button" data-dismiss="modal" class="btn p-0">
                                                        <i class="tio-clear fs-24"></i>
                                                    </button>
                                                </div>
                                                <div class="modal-body pt-2">
                                                    <div class="table-rest-info mb-30 align-items-start">
                                                        <img src="{{ $food['image_full_url'] }}" class="w-80px">
                                                        <div class="info fs-12 text-body">
                                                            <span class="d-block text-title fs-15 mb-2">
                                                            {{ $food['name'] }}
                                                                <span class="rating">
                                                                    ({{ round($food->avg_rating,2) }}/5)
                                                                </span>
                                                                @if ($food->veg == 1)

                                                                <span class="badge badge-soft-success rounded-pill">{{ translate('Veg') }}</span>
                                                                @else
                                                                <span class="badge badge-soft-danger rounded-pill">{{ translate('Non_Veg') }}</span>
                                                                @endif
                                                            </span>
                                                            <div>
                                                                {{ translate('Price') }} : <span class="font-medium">{{ \App\CentralLogics\Helpers::format_currency($food['price'])  }}</span> | {{ translate('Discount') }} : <span class="font-medium"> {{ $food->discount_type == 'percent' ?  $food->discount . ' %' :  \App\CentralLogics\Helpers::format_currency($food['discount'])   }}</span>
                                                            </div>
                                                            <div>
                                                                <?php
                                                                    $addonIds = json_decode($food->add_ons, true) ?? [];
                                                                    $addonNames = collect($addonIds)->map(fn($id) => $addons[$id] ?? null)->filter();
                                                                    ?>

                                                                {{ translate('Addons') }}: <span class="font-medium">
                                                                   @if($addonNames->isNotEmpty())
                                                                        {{ $addonNames->join(', ') }}.
                                                                    @else
                                                                        {{ translate('No_addons_found.') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form action="{{ route('admin.food.updateStock') }}" method="POST" >
                                                        @method("post")
                                                        @csrf
                                                        <input type="hidden" value="{{ $food->id }}"  name="food_id">
                                                        <div class="__bg-F8F9FC-card text-left">
                                                            <label class="input-label">
                                                                {{ translate('Main_Stock') }}
                                                            </label>
                                                            <input type="number" step="1" name="item_stock" value="{{ $food->item_stock }}" required min="1" max="99999999999" class="form-control" placeholder="Ex : 50">
                                                        </div>

                                                        <div class="__bg-F8F9FC-card text-left">
                                                    @if (isset($food->variations) && count(json_decode($food->variations,true)) >0 )

                                                            <div class="row g-2">
                                                                <div class="col-6">
                                                                    <h5>{{ translate('Variation') }}</h5>
                                                                </div>
                                                                <div class="col-6">
                                                                    <h5>{{ translate('Stock') }}</h5>
                                                                </div>
                                                            </div>
                                                    @foreach (json_decode($food->variations,true) as $item)
                                                        <div class="row g-1 mb-3">

                                                            <div class="col-12">
                                                                <h6 class="m-0">
                                                                    {{ $item['name'] }}
                                                                </h6>
                                                            </div>

                                                            @if (isset($item['values']) && is_array($item['values']))
                                                                @foreach ($item['values'] as $value)
                                                                    @if (isset($value['option_id']))
                                                                    <div class="col-12">
                                                                        <div class="row g-1 align-items-center">
                                                                            <span class="col-6">{{  $value['label']  }} :</span>
                                                                            <div class="col-6">
                                                                                <input class="form-control" required value="{{ $value['current_stock'] }}" type="number" min="1" step="1" max="999999999" name="option[{{ $value['option_id'] }}]"  placeholder="Ex : 50">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @endif
                                                                @endforeach

                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif





                                                        </div>

                                                        <div class="d-flex justify-content-end gap-3 mt-3">
                                                            <button type="submit" class="btn btn--primary">{{ translate('Update') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(count($foods) === 0)
                    <div class="empty--data">
                        <i class="tio-inbox" style="font-size: 64px; color: #d1d5db;"></i>
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                    <div class="page-area px-4 pb-3">
                        <div class="d-flex align-items-center justify-content-end">
                            <div>
                                {!! $foods->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>














@endsection

@push('script_2')
    <script>
        "use strict";
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                        '<i class="tio-inbox d-block mb-3" style="font-size: 64px; color: #d1d5db;"></i>' +
                        '<p class="mb-0 text-muted">{{ translate('No_data_to_show') }}</p>' +
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

            $('#toggleColumn_index').change(function(e) {
                datatable.columns(0).visible(e.target.checked)
            })
            $('#toggleColumn_name').change(function(e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_type').change(function(e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_vendor').change(function(e) {
                datatable.columns(3).visible(e.target.checked)
            })

            $('#toggleColumn_status').change(function(e) {
                datatable.columns(5).visible(e.target.checked)
            })
            $('#toggleColumn_price').change(function(e) {
                datatable.columns(4).visible(e.target.checked)
            })
            $('#toggleColumn_action').change(function(e) {
                datatable.columns(6).visible(e.target.checked)
            })

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#restaurant').select2({
            ajax: {
                url: '{{ url('/') }}/admin/restaurant/get-restaurants',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        all: true,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        $('#category').select2({
            ajax: {
                url: '{{ route('admin.category.get-all') }}',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        all: true,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        // Multi-select functionality
        const updateBulkActionsBar = () => {
            const checkedBoxes = $('.food-checkbox:checked');
            const count = checkedBoxes.length;

            if (count > 0) {
                $('#bulk-actions-bar').removeClass('d-none');
                $('#selected-count').text(count);
            } else {
                $('#bulk-actions-bar').addClass('d-none');
            }
        };

        // Select all checkbox
        $('#datatableCheckAll').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.food-checkbox').prop('checked', isChecked);
            updateBulkActionsBar();
        });

        // Individual checkbox change
        $(document).on('change', '.food-checkbox', function() {
            const totalCheckboxes = $('.food-checkbox').length;
            const checkedCheckboxes = $('.food-checkbox:checked').length;

            $('#datatableCheckAll').prop('checked', totalCheckboxes === checkedCheckboxes);
            updateBulkActionsBar();
        });

        // Get selected IDs
        const getSelectedIds = () => {
            return $('.food-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
        };

        // Bulk delete
        $('#bulk-delete-btn').on('click', function() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                toastr.warning('{{ translate('messages.please_select_items_to_delete') }}');
                return;
            }

            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.you_want_to_delete_selected_items') }}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: '#363636',
                confirmButtonText: '{{ translate('messages.yes_delete') }}',
                cancelButtonText: '{{ translate('messages.cancel') }}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('admin.food.bulk-delete') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('{{ translate('messages.an_error_occurred') }}');
                        },
                        complete: function() {
                            $('#loading').hide();
                        }
                    });
                }
            });
        });

        // Bulk duplicate
        $('#bulk-duplicate-btn').on('click', function() {
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                toastr.warning('{{ translate('messages.please_select_items_to_duplicate') }}');
                return;
            }

            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: '{{ translate('messages.you_want_to_duplicate_selected_items') }}',
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: '#363636',
                confirmButtonText: '{{ translate('messages.yes_duplicate') }}',
                cancelButtonText: '{{ translate('messages.cancel') }}'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route('admin.food.bulk-duplicate') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                location.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('{{ translate('messages.an_error_occurred') }}');
                        },
                        complete: function() {
                            $('#loading').hide();
                        }
                    });
                }
            });
        });

    </script>
@endpush
