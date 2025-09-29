@extends('layouts.admin.app')

@section('title', translate('messages.react_site_setup'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-sm-0">
                    <h1 class="page-header-title">{{translate('React Site Setup')}}</h1>
                </div>
            </div>
        </div>
        @php($react_setup=\App\Models\BusinessSetting::where(['key'=>'react_setup'])->first())
        @php($react_setup=$react_setup?json_decode($react_setup->value, true):null)
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.react-update'):'javascript:'}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="react_license_code" class="form-label text-capitalize">{{translate('React license code')}}</label>
                                <input type="text" placeholder="{{translate('React license code')}}" class="form-control h--45px" name="react_license_code" id="react_license_code"
                                    value="{{env('APP_MODE')!='demo'?(isset($react_setup['react_license_code']) ? $react_setup['react_license_code'] : ''):''}}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="react_domain" class="form-label text-capitalize">{{translate('React Domain')}}</label>
                                <input type="text" id="react_domain" placeholder="{{translate('React Domain')}}" class="form-control h--45px" name="react_domain"
                                    value="{{env('APP_MODE')!='demo'?(isset($react_setup['react_domain']) ? $react_setup['react_domain'] : ''):''}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary call-demo">{{translate('messages.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


