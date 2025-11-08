@extends('layouts.admin.app')

@section('title', translate('messages.withdraw_method_list'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <div class="page-title-wrap d-flex justify-content-between flex-wrap align-items-center gap-3 mb-3">
                <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                    {{ translate('messages.withdraw_method_list')}}
                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1"> {{ $withdrawal_methods->total() }}</span>
                </h2>
                {{--<a href="{{route('admin.business-settings.withdraw-method.create')}}" class="btn btn--primary">+ {{ translate('messages.Add_method')}}</a>--}}
            </div>
        </div>
        <!-- End Page Title -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="p-3">
                        <div class="row gy-1 align-items-center justify-content-between">
                            {{--<div class="col-auto">
                                <h5>
                                {{  translate('messages.methods')}}
                                    <span class="badge badge-soft-dark radius-50 fz-12 ml-1"> {{ $withdrawal_methods->total() }}</span>
                                </h5>
                            </div>--}}
                            <div class="col-auto">
                                <form  class="search-form">
                                    <!-- Search -->
                                    <div class="input-group input--group border rounded">
                                        <input id="datatableSearch" name="search" type="search" value="{{ $search }}" class="form-control border-0 h--40px" placeholder="{{ translate('messages.Search_Method_Name')}}" aria-label="{{translate('messages.search_here')}}">
                                        <button type="submit" class="btn btn--reset w-auto px-2 py-2 h-40px min-w-35px"><i class="tio-search"></i></button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <div class="d-flex flex-wrap gap-lg-20 gap--10">
                                <div class="hs-unfold">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn--reset dropdown-toggle min-height-40" href="javascript:;"
                                        data-hs-unfold-options='{
                                                "target": "#usersExportDropdown",
                                                "type": "css-animation"
                                            }'>
                                        <i class="tio-download-from-cloud mr-1 fs-16"></i> {{ translate('messages.export') }}
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
                                <a href="{{route('admin.business-settings.withdraw-method.create')}}" class="btn px-lg-4 px-3 h-40 py-2 d-flex align-items-center justify-content-center fs-12 btn--primary"><i class="tio-add-circle mr-1"></i> {{ translate('messages.Add_method')}}</a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive pt-0">
                        <table id="datatable"
                                class="table table-hover table-borderless table-thead-borderedless table-nowrap table-align-middle card-table w-100">
                            <thead class="global-bg-box thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('messages.sl')}}</th>
                                <th>{{ translate('messages.Payment_method_name')}}</th>
                                <th>{{  translate('messages.method_fields') }}</th>
                                <th class="text-center">{{ translate('messages.active_status')}}</th>
                                <th class="text-center">{{ translate('messages.default_method')}}</th>
                                <th class="text-center">{{ translate('messages.action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($withdrawal_methods as $key=>$withdrawal_method)
                                <tr>
                                    <td class="p-3">{{$withdrawal_methods->firstitem()+$key}}</td>
                                    <td class="p-3">{{$withdrawal_method['method_name']}}</td>


                                    <td class="p-3">

                                            <div class="more-withdraw-list">
                                                <div class="more-withdraw-inner">
                                                    @foreach(array_slice($withdrawal_method['method_fields'], 0, 3) as $key=>$method_field)
                                                        <span class="text-title more-withdraw-item fs-14">
                                                            <span class="mb-1 d-inline-block px-1">
                                                                <span>{{ translate('messages.Name')}}:</span> <span class="gray-dark">{{ translate($method_field['input_name'])}} </span>

                                                            </span>

                                                            <span class="mb-1 d-inline-block px-1">
                                                                <span>{{ translate('messages.Type')}}:</span> <span class="gray-dark">{{ translate($method_field['input_type']) }} </span>
                                                            </span>
                                                            <span class="mb-1 d-inline-block px-1">
                                                                <span>{{ translate('messages.Placeholder')}}:</span> <span class="gray-dark">{{ $method_field['placeholder'] }} </span>
                                                            </span>
                                                            <span class="btn fs-10 py-1 px-2 lh-1 {{ $method_field['is_required'] ? 'bg-danger-opacity5' : 'badge--info' }}">
                                                                {{ $method_field['is_required'] ? translate('messages.Required') :  translate('messages.Optional') }}
                                                            </span>
                                                        </span><br/>
                                                    @endforeach
                                                </div>
                                                @if(count($withdrawal_method['method_fields']) > 3)
                                                    <button type="button"
                                                            class="see__more btn p-0 border-0 bg-transparent text-primary fs-12 font-medium offcanvas-trigger"
                                                            data-target="#withdraw_method-offcanvas"
                                                            data-id="{{ $withdrawal_method->id }}"
                                                            data-name="{{ $withdrawal_method['method_name'] }}"
                                                            data-is_default="{{ $withdrawal_method['is_default'] == 1 ? 'Default' : 'Not Default' }}"
                                                            data-is_active="{{ $withdrawal_method['is_active'] }}"
                                                            data-action="{{ route('admin.business-settings.withdraw-method.edit',$withdrawal_method->id) }}"
                                                            data-fields='@json($withdrawal_method['method_fields'])'>
                                                        {{translate('See More')}}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>



                                    <td class="text-center p-3">
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input class="toggle-switch-input status featured-status"
                                                   data-id="{{$withdrawal_method->id}}"
                                                   type="checkbox" {{$withdrawal_method->is_active?'checked':''}}>
                                                   <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                        </label>
                                    </td>
                                    <td class="text-center p-3">
                                        <label class="toggle-switch mx-auto toggle-switch-sm">
                                            <input type="checkbox" class="default-method toggle-switch-input"
                                            id="{{$withdrawal_method->id}}" {{$withdrawal_method->is_default == 1?'checked':''}}>
                                                   <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                        </label>
                                    </td>



                                    <td class="p-3">
                                        <div class="btn--container justify-content-center">
                                            <a href="{{route('admin.business-settings.withdraw-method.edit',[$withdrawal_method->id])}}"
                                               class="btn btn-sm btn--primary btn-outline-primary action-btn">
                                                <i class="tio-edit"></i>
                                            </a>

                                            @if(!$withdrawal_method->is_default)
                                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert" href="javascript:"
                                                   title="{{ translate('messages.Delete')}}"
                                                   data-id="delete-{{$withdrawal_method->id}}" data-message="{{ translate('Want to delete this item') }}">
                                                    <i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{route('admin.business-settings.withdraw-method.delete',[$withdrawal_method->id])}}"
                                                      method="post" id="delete-{{$withdrawal_method->id}}">
                                                    @csrf @method('delete')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if(count($withdrawal_methods)==0)
                            <div class="empty--data">
                                <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                            </div>
                       @endif
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            <!-- Pagination -->
                            {{$withdrawal_methods->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    <!-- Saved Address Offcanvas -->
    <div id="withdraw_method-offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between" style="--offcanvas-width: 500px">
        <div>
            <div class="custom-offcanvas-header d-flex justify-content-between align-items-center">
                <div class="px-3 py-3 d-flex justify-content-between w-100">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h2 class="mb-0 fs-18 text-title font-medium">{{ translate('Payment Method Information') }}</h2>
                    </div>
                    <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;
                    </button>
                </div>
            </div>

            <div class="custom-offcanvas-body p-20">
                <div class="d-flex flex-column gap-20px">
                    <div class="global-bg-box p-10px rounded mb-3">
                        <div class="d-flex align-items-cetner justify-content-between gap-2 flex-wrap mb-10px">
                            <h5 class="text-title m-0">
                                {{ translate('Method Name') }} : <span id="method-title"></span> (<span id="method-is-default"></span>)
                            </h5>
                            <span id="method-status"></span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column gap-20px" id="method-fields-container">
                </div>
            </div>
        </div>

        <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center offcanvas-footer p-3 position-sticky">
            <a href="#" id="editMethodBtn" class="btn w-100 btn--primary">{{ translate('Edit Method') }}</a>
        </div>
    </div>

    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
@endsection


@push('script_2')
  <script>
      "use strict";
      $(document).on('change', '.default-method', function () {
          let id = $(this).attr("id");
          let status = $(this).prop("checked") === true ? 1:0;

          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

              }
          });
          $.ajax({
              url: "{{route('admin.business-settings.withdraw-method.default-status-update')}}",
              method: 'POST',
              data: {
                  id: id,
                  status: status
              },
              success: function (data) {
                  if(data.success == true) {
                      toastr.success('{{ translate('messages.Default_Method_updated_successfully')}}');
                      setTimeout(function(){
                          location.reload();
                      }, 1000);
                  }
                  else if(data.success == false) {
                      toastr.error('{{ translate('messages.Default_Method_updated_failed.')}}');
                      setTimeout(function(){
                          location.reload();
                      }, 1000);
                  }
              }
          });
      });

      $(document).on('click', '.featured-status', function() {
          let id = $(this).data('id');
          $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
              url: "{{route('admin.business-settings.withdraw-method.status-update')}}",
              method: 'POST',
              data: {
                  id: id
              },
              success: function (data) {
                  toastr.success('{{ translate('messages.status_updated_successfully')}}');
              }
          });
      })

      $(document).on('click', '.see__more', function() {
          let methodId = $(this).data('id');
          let methodName = $(this).data('name');
          let isActive = $(this).data('is_active');
          let isDefault = $(this).data('is_default');
          let action = $(this).data('action');
          let fields = $(this).data('fields');

          function formatText(text) {
              if (!text) return '';
              return text
                  .replace(/_/g, ' ')
                  .toLowerCase()
                  .replace(/\b\w/g, char => char.toUpperCase());
          }

          let statusBadge = isActive
              ? '<span class="btn fs-10 py-1 px-2 lh-1 badge--success">' + "Active" + '</span>'
              : '<span class="btn fs-10 py-1 px-2 lh-1 bg-danger-opacity5">' + "Inactive" + '</span>';

          $('#method-status').html(statusBadge);
          $('#method-title').text(formatText(methodName));
          $('#method-is-default').text(formatText(isDefault));


          $('#method-fields-container').empty();

          $.each(fields, function(index, field) {
              let inputName = formatText(field.input_name);
              let inputType = formatText(field.input_type);
              let placeholder = formatText(field.placeholder);

              let requiredBadge = field.is_required
                  ? '<span class="btn fs-10 py-1 px-2 lh-1 bg-danger-opacity5">' + "Required" + '</span>'
                  : '<span class="btn fs-10 py-1 px-2 lh-1 badge--success">' + "Optional" + '</span>';

              let fieldHtml = `
            <div class="global-bg-box p-10px rounded">
                <div class="d-flex align-items-cetner justify-content-between gap-2 flex-wrap mb-10px">
                    <h5 class="text-title m-0 d-flex gap-2">${inputName} <span class="gap-2">${requiredBadge}</span></h5>
                </div>
                <div class="bg-white rounded p-10px d-flex flex-column gap-1">
                    <div class="d-flex gap-2">
                        <span class="before-info w-90px min-w-90 gray-dark fs-12">{{translate('Type')}}</span>
                        <span class="fs-14 text-title">${inputType}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="before-info w-90px min-w-90 gray-dark fs-12">Placeholder</span>
                        <span class="fs-14 text-title">${placeholder}</span>
                    </div>
                </div>
            </div>
        `;

              $('#method-fields-container').append(fieldHtml);
          });

          $('#editMethodBtn').attr('href', action);

          $('#withdraw_method-offcanvas').addClass('show');
      });

  </script>
@endpush
