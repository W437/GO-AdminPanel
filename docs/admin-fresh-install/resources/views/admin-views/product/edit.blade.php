@extends('layouts.admin.app')

@section('title', translate('Update_Food'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ dynamicAsset('public/assets/admin/css/AI/animation/product/ai-sidebar.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-edit"></i>
                {{ translate('messages.food_update') }}
            </h1>
        </div>



  @php($openai_config=\App\CentralLogics\Helpers::get_business_settings('openai_config'))

        <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
            <input type="hidden" id="request_type" value="admin">
            @csrf
            <input type="hidden" id="removedVariationIDs" name="removedVariationIDs" value="">
            <input type="hidden" id="removedVariationOptionIDs" name="removedVariationOptionIDs" value="">
            <div class="row g-2">


                @includeif('admin-views.product.partials._title_and_discription')
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center gap-3">
                                <p class="mb-0">{{ translate('Food_Image') }} </p>
                                <div class="image-box">
                                    <label for="image-input"
                                        class="d-flex flex-column align-items-center justify-content-center h-100 cursor-pointer gap-2">
                                        <img class="upload-icon initial-26" src="{{ $product['image_full_url'] }}"
                                            alt="Upload Icon">
                                        <img src="#" alt="Preview Image" class="preview-image">
                                    </label>
                                    <button type="button" class="delete_image">
                                        <i class="tio-delete"></i>
                                    </button>
                                    <input type="file" id="image-input" name="image" accept="image/*" hidden>
                                </div>

                                <p class="opacity-75 max-w220 mx-auto text-center">
                                    {{ translate('Image format - jpg png jpeg gif Image Size -maximum size 2 MB Image Ratio - 1:1') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @includeif('admin-views.product.partials._category_and_general')
                @includeif('admin-views.product.partials._price_and_stock')

                <div class="col-lg-12">
                    <div class="general_wrapper">
                        <div class="outline-wrapper">
                            <div class="card shadow--card-2 border-0 bg-animate">
                                <div class="card-header flex-wrap">
                                    <h5 class="card-title">
                                        <span class="card-header-icon mr-2">
                                            <i class="tio-canvas-text"></i>
                                        </span>
                                        <span>{{ translate('messages.food_variations') }}</span>
                                    </h5>
                                    <div>
                                        <a class="btn text--primary-2" id="add_new_option_button">
                                            {{ translate('add_new_variation') }}
                                            <i class="tio-add"></i>
                                        </a>
                                        @if (isset($openai_config) && data_get($openai_config, 'status') == 1)
                                        <button type="button" class="btn bg-white text-primary opacity-1 generate_btn_wrapper variation_setup_auto_fill"
                                            id="variation_setup_auto_fill"
                                            data-route="{{ route('admin.product.variation-setup-auto-fill') }}" data-lang="en">
                                            <div class="btn-svg-wrapper">
                                                <img width="18" height="18" class="" src="{{ dynamicAsset('public/assets/admin/img/svg/blink-right-small.svg') }}"
                                                    alt="">
                                            </div>
                                            <span class="ai-text-animation d-none" role="status">
                                                {{ translate('Just_a_second') }}
                                            </span>
                                            <span class="btn-text">{{ translate('Generate') }}</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                <input type="hidden" name="remove_all_old_variations" value="0" id="remove_all_old_variations">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-12">
                                            <div id="add_new_option">
                                                @if (isset($product->variations))
                                                    @foreach (json_decode($product->variations, true) as $key_choice_options => $item)
                                                        @if (isset($item['price']))
                                                            @break

                                                        @else
                                                            @include(
                                                                'admin-views.product.partials._new_variations',
                                                                ['item' => $item, 'key' => $key_choice_options + 1]
                                                            )
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @includeif('admin-views.product.partials._ai_sidebar')
                <div class="col-lg-12">
                    @includeif('admin-views.product.partials._seo-section-edit')
                </div>



                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('script')
@endpush

@push('script_2')

<script>
            var count = {{ isset($product->variations) ? count(json_decode($product->variations, true)) : 0 }};
</script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/tags-input.min.js"></script>

    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/product-title-autofill.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/product-description-autofill.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/general-setup-autofill.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/product-others-autofill.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/variation-setup-auto-fill.js') }}"></script>
    {{-- <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/seo-section-autofill.js') }}"></script> --}}

    <script src="{{ dynamicAsset('public/assets/admin/js/AI/products/ai-sidebar.js') }}"></script>

    <script src="{{ dynamicAsset('/public/assets/admin/js/AI/products/compressor/image-compressor.js')}}"></script>
    <script src="{{ dynamicAsset('/public/assets/admin/js/AI/products/compressor/compressor.min.js')}}"></script>


    <script>
        "use strict";



        $('#stock_type').on('change', function() {
            if ($(this).val() == 'unlimited') {
                $('.stock_disable').prop('readonly', true).prop('required', false).attr('placeholder',
                    '{{ translate('Unlimited') }}').val('');
                $('.hide_this').addClass('d-none');
            } else {
                $('.stock_disable').prop('readonly', false).prop('required', true).attr('placeholder',
                    '{{ translate('messages.Ex:_100') }}');
                $('.hide_this').removeClass('d-none');
            }
        });

        updatestockCount();

        function updatestockCount() {
            if ($('#stock_type').val() == 'unlimited') {
                $('.stock_disable').prop('readonly', true).prop('required', false).attr('placeholder',
                    '{{ translate('Unlimited') }}').val('');
                $('.hide_this').addClass('d-none');
            } else {
                $('.stock_disable').prop('readonly', false).prop('required', true).attr('placeholder',
                    '{{ translate('messages.Ex:_100') }}');
                $('.hide_this').removeClass('d-none');
            }
        }


        $('#restaurant_id').on('change', function() {
            let route = '{{ url('/') }}/admin/restaurant/get-addons?data[]=0&restaurant_id=';
            let restaurant_id = $(this).val();
            let id = 'add_on';
            getRestaurantData(route, restaurant_id, id);

        });
        $('.get-request').on('change', function() {
            let route = '{{ url('/') }}/admin/food/get-categories?parent_id=' + $(this).val();
            let id = 'sub-categories';
            getRequest(route, id);
        });

        function getRestaurantData(route, restaurant_id, id) {
            $.get({
                url: route + restaurant_id,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
            $('#image-viewer-section').show(1000)
        });

        $(document).ready(function() {
            setTimeout(function() {
                let category = $("#category-id").val();
                let sub_category = '{{ count($product_category) >= 2 ? $product_category[1]->id : '' }}';
                let sub_sub_category =
                    '{{ count($product_category) >= 3 ? $product_category[2]->id : '' }}';
                getRequest('{{ url('/') }}/admin/food/get-categories?parent_id=' + category +
                    '&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{ url('/') }}/admin/food/get-categories?parent_id=' + sub_category +
                    '&sub_category=' + sub_sub_category, 'sub-sub-categories');

            }, 1000)

            @if (count(json_decode($product['add_ons'], true)) > 0)
                getRestaurantData(
                    '{{ url('/') }}/admin/restaurant/get-addons?@foreach (json_decode($product['add_ons'], true) as $addon)data[]={{ $addon }}& @endforeach restaurant_id=',
                    '{{ $product['restaurant_id'] }}', 'add_on');
            @else
                getRestaurantData('{{ url('/') }}/admin/restaurant/get-addons?data[]=0&restaurant_id=',
                    '{{ $product['restaurant_id'] }}', 'add_on');
            @endif
        });

        $(document).on('ready', function() {
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ url('/') }}/admin/restaurant/get-restaurants',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        function show_min_max(data) {
            $('#min_max1_' + data).removeAttr("readonly");
            $('#min_max2_' + data).removeAttr("readonly");
            $('#min_max1_' + data).attr("required", "true");
            $('#min_max2_' + data).attr("required", "true");
        }

        function hide_min_max(data) {
            $('#min_max1_' + data).val(null).trigger('change');
            $('#min_max2_' + data).val(null).trigger('change');
            $('#min_max1_' + data).attr("readonly", "true");
            $('#min_max2_' + data).attr("readonly", "true");
            $('#min_max1_' + data).attr("required", "false");
            $('#min_max2_' + data).attr("required", "false");
        }
        $(document).on('change', '.show_min_max', function() {
            let data = $(this).data('count');
            show_min_max(data);
        });

        $(document).on('change', '.hide_min_max', function() {
            let data = $(this).data('count');
            hide_min_max(data);
        });







        $(document).ready(function() {
            $("#add_new_option_button").click(function() {
                    add_new_option_button();
            });
        });



        function add_new_option_button() {
            $('#empty-variation').hide();
                count++;
                let add_option_view = `
                        <div class="__bg-F8F9FC-card view_new_option mb-2">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <label class="form-check form--check">
                                        <input id="options[` + count + `][required]" name="options[` + count + `][required]"
                                            class="form-check-input" type="checkbox">
                                        <span class="form-check-label">{{ translate('Required') }}</span>
                                    </label>
                                    <div>
                                        <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                            title="{{ translate('Delete') }}">
                                            <i class="tio-add-to-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-xl-4 col-lg-6">
                                        <label for="">{{ translate('name') }}&nbsp;<span class="form-label-secondary text-danger"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="{{ translate('messages.Required.') }}"> *
                                            </span></label>
                                        <input required name=options[` + count + `][name] class="form-control new_option_name" type="text"
                                            data-count="` +  count + `">
                                    </div>

                                    <div class="col-xl-4 col-lg-6">
                                        <div>
                                            <label class="input-label text-capitalize d-flex align-items-center"><span
                                                    class="line--limit-1">{{ translate('messages.Variation_Selection_Type') }} </span>
                                            </label>
                                            <div class="resturant-type-group px-0">
                                                <label class="form-check form--check mr-2 mr-md-4">
                                                    <input class="form-check-input show_min_max" data-count="` + count + `" type="radio"
                                                        value="multi" name="options[` + count + `][type]" id="type` + count + `" checked>
                                                    <span class="form-check-label">
                                                        {{ translate('Multiple Selection') }}
                                                    </span>
                                                </label>

                                                <label class="form-check form--check mr-2 mr-md-4">
                                                    <input class="form-check-input hide_min_max" data-count="` + count + `" type="radio"
                                                        value="single" name="options[` + count + `][type]" id="type` + count +  `">
                                                    <span class="form-check-label">
                                                        {{ translate('Single Selection') }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label for="">{{ translate('Min') }}</label>
                                                <input id="min_max1_` + count + `" required name="options[` + count + `][min]"
                                                    class="form-control" type="number" min="1">
                                            </div>
                                            <div class="col-6">
                                                <label for="">{{ translate('Max') }}</label>
                                                <input id="min_max2_` + count + `" required name="options[` + count + `][max]"
                                                    class="form-control" type="number" min="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="option_price_` + count + `">
                                    <div class="bg-white border rounded p-3 pb-0 mt-3">
                                        <div id="option_price_view_` + count + `">
                                            <div class="row g-3 add_new_view_row_class mb-3">
                                                <div class="col-md-3 col-sm-6">
                                                    <label for="">{{ translate('Option_name') }} &nbsp;<span
                                                            class="form-label-secondary text-danger" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('messages.Required.') }}"> *
                                                        </span></label>
                                                    <input class="form-control" required type="text"
                                                        name="options[` + count +  `][values][0][label]" id="">
                                                </div>
                                                <div class="col-md-3 col-sm-6">
                                                    <label for="">{{ translate('Additional_price') }}
                                                        ({{ \App\CentralLogics\Helpers::currency_symbol() }})&nbsp;<span
                                                            class="form-label-secondary text-danger" data-toggle="tooltip"
                                                            data-placement="right"
                                                            data-original-title="{{ translate('messages.Required.') }}"> *
                                                        </span></label>
                                                    <input class="form-control" required type="number" min="0" step="0.01"
                                                        name="options[` + count + `][values][0][optionPrice]" id="">
                                                </div>
                                                <div class="col-md-3 col-sm-6 hide_this">
                                                    <label for="">{{ translate('Stock') }}</label>
                                                    <input class="form-control stock_disable count_stock" required type="number"
                                                        min="0" max="9999999" name="options[` +  count + `][values][0][total_stock]"
                                                        id="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3 p-3 mr-1 d-flex " id="add_new_button_` + count + `">
                                            <button type="button" class="btn btn--primary btn-outline-primary add_new_row_button"
                                                data-count="` + count + `">{{ translate('Add_New_Option') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                `;

                $("#add_new_option").append(add_option_view);
                updatestockCount();
        }


        function new_option_name(value, data) {
            $("#new_option_name_" + data).empty();
            $("#new_option_name_" + data).text(value)

        }

        function removeOption(e) {
            var element = $(e);
            element.parents('.view_new_option').remove();
        }

        function deleteRow(e) {
            var element = $(e);
            element.parents('.add_new_view_row_class').remove();
        }


        function add_new_row_button(data) {
            var countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            let add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-3 col-sm-5">
                        <label for="">{{ translate('Option_name') }}  &nbsp;<span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.') }}"> *
                                </span></label>
                        <input class="form-control" required type="text" name="options[` + data + `][values][` +
                countRow + `][label]" id="">
                    </div>
                    <div class="col-md-3 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}  &nbsp;<span class="form-label-secondary text-danger"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ translate('messages.Required.') }}"> *
                                </span></label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` +
                data +
                `][values][` + countRow +
                `][optionPrice]" id="">
                    </div>
                    <div class="col-md-3 col-sm-5 hide_this">
                        <label for="">{{ translate('Stock') }}  </label>
                        <input class="form-control stock_disable count_stock"  required type="number" min="0" max="99999999"  name="options[` +
                data +
                `][values][` + countRow + `][total_stock]" id="">
                    </div>

                    <input type="hidden" hidden name="options[` +
                data +
                `][values][` + countRow + `][option_id]" value="null" >

                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm deleteRow"
                                title="{{ translate('Delete') }}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
            $('#option_price_view_' + data).append(add_new_row_view);
            updatestockCount();

        }
        $(document).on('click', '.delete_input_button', function() {
            let e = $(this);
            removeOption(e);
            updatestockCount();
        });

        let removedVariationIDs = [];
        let removedVariationOptionIDs = [];

        $(document).on('click', '.remove_variation', function() {
            removedVariationIDs.push($(this).data('id'));
            $('#removedVariationIDs').val(removedVariationIDs.join(','));
        });
        $(document).on('click', '.remove_variation_option', function() {
            removedVariationOptionIDs.push($(this).data('id'));
            $('#removedVariationOptionIDs').val(removedVariationOptionIDs.join(','));
        });


        $(document).on('click', '.deleteRow', function() {
            let e = $(this);
            deleteRow(e);
        });
        $(document).on('click', '.add_new_row_button', function() {
            let data = $(this).data('count');
            add_new_row_button(data);
        });
        $(document).on('keyup', '.new_option_name', function() {
            let data = $(this).data('count');
            let value = $(this).val();
            new_option_name(value, data);
        });

      function validateImageSize(inputSelector,imageType="Image", maxSizeMB = 2) {
          let fileInput = $(inputSelector)[0];
          if (fileInput && fileInput.files.length > 0) {
              let fileSize = fileInput.files[0].size;
              if (fileSize > maxSizeMB * 1024 * 1024) {
                  toastr.error(`${imageType} size should not exceed ${maxSizeMB}MB`, {
                      CloseButton: true,
                      ProgressBar: true
                  });
                  return false;
              }
          }
          return true;
       }

        $('#product_form').on('submit', function() {
            if (!validateImageSize('#image-input',"Food image") || !validateImageSize('#image-input2',"SEO image")) {
               return;
            }
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('admin.food.update', [$product['id']]) }}',
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{ translate('product_updated_successfully!') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        // location.reload(true);
                        setTimeout(function() {
                            location.href =
                                '{{ \Request::server('HTTP_REFERER') ?? route('admin.food.list') }}';
                        }, 2000);
                    }
                }
            });
        });


        $('#reset_btn').click(function() {
            location.reload(true);
        })
    </script>
@endpush
