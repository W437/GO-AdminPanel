@extends('layouts.admin.app')

@section('title','Create Advertisement')

@section('advertisement')
active
@endsection
@section('advertisement_create')
active
@endsection

@push('css_or_js')
    <link rel="stylesheet" type="text/css" href="{{dynamicAsset('public/assets/admin/css/daterangepicker.css')}}"/>
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <h1 class="page-header-title mb-3">
        {{ translate('Create_Advertisement') }}
    </h1>

    <div class="card">
        <div class="card-body">
            <form id="create-add-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method("POST")

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Multi-Language Title & Description -->
                        <div class="js-nav-scroller hs-nav-scroller-horizontal mb-3">
                            <ul class="nav nav-tabs border-0">
                                <li class="nav-item">
                                    <a class="nav-link lang_link active" href="#" id="default-link">
                                        {{translate('messages.default')}}
                                    </a>
                                </li>
                                @if ($language)
                                    @foreach ($language as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link" href="#" id="{{ $lang }}-link">
                                                {{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <!-- Default Language Form -->
                        <div class="lang_form" id="default-form">
                            <div class="mb-3">
                                <label class="form-label">{{ translate('Advertisement_Title') }} ({{ translate('Default') }}) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title[]"
                                    value="{{ old('title.0') }}"
                                    placeholder="{{ translate('Enter advertisement title') }}"
                                    maxlength="255" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ translate('Short_Description') }} ({{ translate('Default') }})</label>
                                <textarea class="form-control" name="description[]" rows="3"
                                    placeholder="{{ translate('Enter short description') }}"
                                    maxlength="1000">{{ old('description.0') }}</textarea>
                                <small class="text-muted">{{ translate('Maximum 1000 characters') }}</small>
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                        </div>

                        <!-- Other Language Forms -->
                        @if ($language)
                            @foreach ($language as $lang)
                                <div class="d-none lang_form" id="{{ $lang }}-form">
                                    <div class="mb-3">
                                        <label class="form-label">{{ translate('Advertisement_Title') }} ({{ strtoupper($lang) }})</label>
                                        <input type="text" class="form-control" name="title[]"
                                            value="{{ old('title.' . ($loop->index + 1)) }}"
                                            placeholder="{{ translate('Enter advertisement title') }}"
                                            maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">{{ translate('Short_Description') }} ({{ strtoupper($lang) }})</label>
                                        <textarea class="form-control" name="description[]" rows="3"
                                            placeholder="{{ translate('Enter short description') }}"
                                            maxlength="1000">{{ old('description.' . ($loop->index + 1)) }}</textarea>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                </div>
                            @endforeach
                        @endif

                        <!-- Restaurant Selection -->
                        <div class="mb-3">
                            <label class="form-label">{{ translate('messages.Select_Restautant') }} <span class="text-danger">*</span></label>
                            <select name="restaurant_id" id="restaurant_id"
                                data-placeholder="{{ translate('messages.select_restaurant') }}"
                                class="js-data-example-ajax form-control" required>
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Validity') }} <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="tio-calendar-month icon-absolute-on-right"></i>
                                <input type="text" class="form-control bg-transparent" name="dates"
                                    placeholder="{{ translate('messages.Select_Date') }}" required>
                            </div>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Select_Priority') }}</label>
                            <select class="form-control js-select2-custom" name="priority">
                                <option value="1" selected>1 ({{ translate('Highest') }})</option>
                                @for ($i = 2; $i <= $total_adds; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                                <option value="">{{ translate('messages.N/A') }}</option>
                            </select>
                            <small class="text-muted">{{ translate('Lower numbers appear first') }}</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">{{ translate('Status') }}</label>
                            <select class="form-control" name="status">
                                <option value="approved" selected>{{ translate('Approved') }}</option>
                                <option value="pending">{{ translate('Pending') }}</option>
                            </select>
                        </div>

                        <!-- Hidden field for advertisement type -->
                        <input type="hidden" name="advertisement_type" value="restaurant_promotion">

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('admin.advertisement.index') }}" class="btn btn-secondary">
                                {{ translate('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ translate('Create_Advertisement') }}
                            </button>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="col-lg-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="mb-3">{{ translate('How it Works') }}</h5>
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2">
                                        <i class="tio-checkmark-circle text-success mt-1"></i>
                                        <p class="mb-0 small">{{ translate('Select the restaurant you want to promote') }}</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <i class="tio-checkmark-circle text-success mt-1"></i>
                                        <p class="mb-0 small">{{ translate('Add a catchy title and description') }}</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <i class="tio-checkmark-circle text-success mt-1"></i>
                                        <p class="mb-0 small">{{ translate('Set the date range for the promotion') }}</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <i class="tio-checkmark-circle text-success mt-1"></i>
                                        <p class="mb-0 small">{{ translate('The app will display the restaurant card with all its images and details') }}</p>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="alert alert-soft-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="tio-info-outined mr-2"></i>
                                        <span class="small">{{ translate('No need to upload images - the system will use the restaurant\'s existing photos, ratings, and reviews') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src="{{dynamicAsset('public/assets/admin/js/view-pages/common.js')}}"></script>
<script src="{{dynamicAsset('public/assets/admin/js/daterangepicker.min.js')}}"></script>

<script>
    "use strict";

    // Date Range Picker
    $('input[name="dates"]').daterangepicker({
        startDate: moment(),
        endDate: moment().add(7, 'days'),
        locale: {
            format: 'MM/DD/YYYY'
        }
    });

    // Restaurant Search AJAX
    $('#restaurant_id').select2({
        ajax: {
            url: '{{ url('/') }}/admin/restaurant/get-restaurants',
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            __port: function () {
                var params = {};
                var searchTerm = $('.select2-search__field').val();
                if (searchTerm) {
                    params.search = searchTerm;
                }
                return params;
            }
        }
    });

    // Language Tab Switching
    $('.lang_link').on('click', function(e) {
        e.preventDefault();
        $('.lang_link').removeClass('active');
        $(this).addClass('active');

        let lang = $(this).attr('id').replace('-link', '');
        $('.lang_form').addClass('d-none');
        $('#' + lang + '-form').removeClass('d-none');
    });

    // Form Submission
    $('#create-add-form').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: '{{ route('admin.advertisement.store') }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' +
                    '{{ translate('Creating') }}...'
                );
            },
            success: function(response) {
                toastr.success(response.message || '{{ translate('Advertisement created successfully') }}');
                setTimeout(function() {
                    window.location.href = '{{ route('admin.advertisement.index') }}';
                }, 1000);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function(error) {
                        toastr.error(Array.isArray(error) ? error[0] : error);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || '{{ translate('Something went wrong') }}');
                }
                $('button[type="submit"]').prop('disabled', false).html('{{ translate('Create_Advertisement') }}');
            }
        });
    });
</script>
@endpush
