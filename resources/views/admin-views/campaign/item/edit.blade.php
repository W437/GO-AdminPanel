@extends('layouts.admin.app')

@section('title',translate('Update_campaign'))

@push('css_or_js')
    <link href="{{dynamicAsset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <div class="page-header-icon">
                            <i class="tio-edit"></i>
                        </div>
                        {{translate('messages.update_campaign')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <form action="javascript:" method="post" id="campaign_form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @php($language=\App\Models\BusinessSetting::where('key','language')->first())
            @php($language = $language->value ?? null)
            @php($default_lang = str_replace('_', '-', app()->getLocale()))

            <div class="row g-2">
                @if($language)
                <div class="col-12">
                    <div class="js-nav-scroller hs-nav-scroller-horizontal">
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link lang_link active" href="#" id="default-link">{{ translate('Default') }}</a>
                            </li>
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Campaign Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-info"></i></span>
                                <span>{{translate('messages.campaign_information')}}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($language)
                                @php($title_data = \App\Models\Translation::where('translationable_type','App\Models\ItemCampaign')
                                    ->where('translationable_id',$campaign->id)
                                    ->where('key','title')
                                    ->get())
                                @php($description_data = \App\Models\Translation::where('translationable_type','App\Models\ItemCampaign')
                                    ->where('translationable_id',$campaign->id)
                                    ->where('key','description')
                                    ->get())

                                <div class="lang_form" id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="default_title">{{translate('messages.title')}} (Default)<span class="text-danger">*</span></label>
                                        <input type="text" name="title[]" id="default_title" class="form-control" value="{{$title_data->where('locale','default')->first()?->value ?? $campaign->title}}" placeholder="{{translate('messages.campaign_title_placeholder')}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group">
                                        <label class="input-label" for="default_description">{{translate('messages.description')}} (Default)</label>
                                        <textarea name="description[]" id="default_description" class="form-control h--72px" placeholder="{{translate('messages.campaign_description_placeholder')}}">{{$description_data->where('locale','default')->first()?->value ?? $campaign->description}}</textarea>
                                    </div>
                                </div>
                                @foreach(json_decode($language) as $lang)
                                    <div class="d-none lang_form" id="{{$lang}}-form">
                                        <div class="form-group">
                                            <label class="input-label" for="{{$lang}}_title">{{translate('messages.title')}} ({{strtoupper($lang)}})</label>
                                            <input type="text" name="title[]" id="{{$lang}}_title" class="form-control" value="{{$title_data->where('locale',$lang)->first()?->value}}" placeholder="{{translate('messages.campaign_title_placeholder')}}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                        <div class="form-group">
                                            <label class="input-label" for="{{$lang}}_description">{{translate('messages.description')}} ({{strtoupper($lang)}})</label>
                                            <textarea name="description[]" id="{{$lang}}_description" class="form-control h--72px" placeholder="{{translate('messages.campaign_description_placeholder')}}">{{$description_data->where('locale',$lang)->first()?->value}}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="title">{{translate('messages.title')}}<span class="text-danger">*</span></label>
                                        <input type="text" name="title[]" id="title" class="form-control" value="{{$campaign->title}}" placeholder="{{translate('messages.campaign_title_placeholder')}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group">
                                        <label class="input-label" for="description">{{translate('messages.description')}}</label>
                                        <textarea name="description[]" id="description" class="form-control h--72px" placeholder="{{translate('messages.campaign_description_placeholder')}}">{{$campaign->description}}</textarea>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group mb-0">
                                <label class="input-label" for="zone_id">{{translate('messages.zone')}}<span class="text-danger">*</span></label>
                                <select name="zone_id" id="zone_id" class="form-control js-select2-custom" required>
                                    <option value="" disabled>{{translate('messages.select_zone')}}</option>
                                    @foreach(\App\Models\Zone::all() as $zone)
                                        <option value="{{$zone->id}}" {{$campaign->zone_id == $zone->id ? 'selected' : ''}}>{{$zone->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Image -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-image"></i></span>
                                <span>{{translate('messages.campaign_image')}}</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <label class="input-label">{{translate('messages.image')}} <small class="text-danger">({{translate('messages.ratio')}} 2:1)</small></label>
                            <center class="mb-4">
                                <img class="initial-14 border rounded" id="viewer"
                                    src="{{$campaign->image_full_url ?? dynamicAsset('public/assets/admin/img/upload-img.png')}}" alt="campaign image"/>
                            </center>
                            <div class="custom-file">
                                <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose')}} {{translate('messages.file')}}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Food Items Selection -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-category"></i></span>
                                <span>{{translate('messages.select_food_items')}}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="input-label" for="food_ids">{{translate('messages.food_items')}}<span class="text-danger">*</span></label>
                                <select name="food_ids[]" id="food_ids" class="form-control" multiple="multiple" required>
                                    <!-- Will be populated via AJAX -->
                                </select>
                                <small class="text-muted">{{translate('messages.select_multiple_food_items_for_this_campaign')}}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Schedule -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                <span>{{translate('messages.campaign_schedule')}}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="start_date">{{translate('messages.start_date')}}<span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{$campaign->start_date->format('Y-m-d')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="end_date">{{translate('messages.end_date')}}<span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{$campaign->end_date->format('Y-m-d')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="start_time">{{translate('messages.start_time')}}<span class="text-danger">*</span></label>
                                        <input type="time" name="start_time" id="start_time" class="form-control" value="{{$campaign->start_time->format('H:i')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="end_time">{{translate('messages.end_time')}}<span class="text-danger">*</span></label>
                                        <input type="time" name="end_time" id="end_time" class="form-control" value="{{$campaign->end_time->format('H:i')}}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <a href="{{route('admin.campaign.list', 'item')}}" class="btn btn--reset">{{translate('messages.back')}}</a>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    "use strict";

    // Pre-selected food IDs from campaign
    const selectedFoodIds = @json($campaign->foods->pluck('id')->toArray());
    const zoneId = {{$campaign->zone_id}};

    // Language switching
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');

        let lang = $(this).attr("id").split("-")[0];
        $("#"+lang+"-form").removeClass('d-none');
        if(lang == 'default')
        {
            $(".from_part_2").removeClass('d-none');
        }
        else
        {
            $(".from_part_2").addClass('d-none');
        }
    });

    // Image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function () {
        readURL(this);
    });

    // Load food items on page load
    $(document).ready(function() {
        loadFoodItems(zoneId, selectedFoodIds);
    });

    // Zone selection change
    $('#zone_id').on('change', function() {
        let newZoneId = $(this).val();
        if (newZoneId) {
            loadFoodItems(newZoneId, []);
        }
    });

    function loadFoodItems(zoneId, preselected = []) {
        $('#food_ids').prop('disabled', true).html('<option value="">{{translate('messages.loading')}}...</option>');

        $.ajax({
            url: '{{route('admin.campaign.get-zone-foods')}}',
            type: 'GET',
            data: { zone_id: zoneId },
            success: function(response) {
                $('#food_ids').html('');
                if (response.length > 0) {
                    response.forEach(function(food) {
                        $('#food_ids').append(new Option(food.display_text, food.id));
                    });
                    $('#food_ids').prop('disabled', false);

                    // Initialize Select2
                    if (!$('#food_ids').hasClass("select2-hidden-accessible")) {
                        $('#food_ids').select2({
                            placeholder: "{{translate('messages.select_food_items')}}",
                            allowClear: true
                        });
                    }

                    // Set pre-selected values
                    if (preselected.length > 0) {
                        $('#food_ids').val(preselected).trigger('change');
                    }
                } else {
                    $('#food_ids').html('<option value="">{{translate('messages.no_food_items_found_in_this_zone')}}</option>');
                    toastr.info('{{translate('messages.no_food_items_found_in_this_zone')}}');
                }
            },
            error: function(xhr) {
                toastr.error('{{translate('messages.failed_to_load_food_items')}}');
                $('#food_ids').html('<option value="">{{translate('messages.error_loading_items')}}</option>');
            }
        });
    }

    // Form submission
    $('#campaign_form').on('submit', function (e) {
        e.preventDefault();

        // Validate food selection
        let selectedFoods = $('#food_ids').val();
        if (!selectedFoods || selectedFoods.length === 0) {
            toastr.error('{{translate('messages.please_select_at_least_one_food_item')}}');
            return;
        }

        var formData = new FormData(this);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.post({
            url: '{{route('admin.campaign.update-item', $campaign->id)}}',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    toastr.success('{{translate('messages.campaign_updated_successfully')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function () {
                        location.href = '{{route('admin.campaign.list', 'item')}}';
                    }, 2000);
                }
            },
            error: function (xhr) {
                $('#loading').hide();
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.keys(errors).forEach(key => {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('{{translate('messages.failed_to_update_campaign')}}');
                }
            }
        });
    });
</script>
@endpush
