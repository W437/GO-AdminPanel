@extends('layouts.admin.app')

@section('title', translate('messages.third_party_apis'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-code"></i>
                </span>
                <span>
                    {{translate('messages.third_party_apis')}}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.third-party-links')
        </div>
        <div class="card">
            @php($map_api_key=\App\Models\BusinessSetting::where(['key'=>'map_api_key'])->first())
            @php($map_api_key=$map_api_key?$map_api_key->value:null)

            @php($map_api_key_server=\App\Models\BusinessSetting::where(['key'=>'map_api_key_server'])->first())
            @php($map_api_key_server=$map_api_key_server?$map_api_key_server->value:null)
            <div class="card-header card-header-shadow border-0 align-items-center">
                <h5 class="card-title align-items-center text--title">
                    {{translate('Google Map API Setup')}}
                </h5>
