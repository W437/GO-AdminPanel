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
                <div class="blinkings active lg-top">
                    <i class="tio-info-outined"></i>
                    <div class="business-notes">
                        <h6><i class="tio-settings-outlined"></i> {{translate('Note')}}</h6>
                        <div>
                            {{translate('Without_configuring_this_section_map_functionality_will_not_work_properly._Thus_the_whole_system_will_not_work_as_it_planned')}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert--primary d-flex" role="alert">
                    <div class="alert--icon">
                        <i class="tio-info"></i>
                    </div>
                    <div>
                        {{translate('messages.map_api_hint_map_api_hint_2')}}
                    </div>
                </div>
                <div class="py-1"></div>

                {{-- API Keys Explanation --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="text-dark mb-2">🌐 <strong>Client Key</strong> (Public)</h6>
                                <small class="text-muted">
                                    <strong>Used for:</strong> Browser maps (admin panel)<br>
                                    <strong>Restriction:</strong> HTTP referrers<br>
                                    <strong class="text-primary">Add:</strong> https://{{ env('ADMIN_DOMAIN', 'hq-secure-panel-1337.hopa.delivery') }}/*<br>
                                    <strong class="text-muted">Also add (for transition):</strong> https://admin.hopa.delivery/*<br>
                                    <strong>Enable APIs:</strong>
                                    <ul class="mb-0 pl-3">
                                        <li>Maps JavaScript API</li>
                                        <li>Places API</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="text-dark mb-2">🔐 <strong>Server Key</strong> (Private)</h6>
                                <small class="text-muted">
                                    <strong>Used for:</strong> Backend calculations<br>
                                    <strong>Restriction:</strong> IP addresses<br>
                                    <strong class="text-primary">Add:</strong> 138.197.188.120<br>
                                    <strong>Enable APIs:</strong>
                                    <ul class="mb-0 pl-3">
                                        <li>Places API</li>
                                        <li>Geocoding API</li>
                                        <li>Distance Matrix API</li>
                                        <li>Routes API</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-1"></div>
                <form action="{{route('admin.business-settings.config-update')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="map_api_key" class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.client')}})</label>
                                <input type="text" id="map_api_key" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.client')}})" class="form-control" name="map_api_key"
                                    value="{{$map_api_key??''}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="map_api_key_server" class="input-label">{{translate('messages.map_api_key')}} ({{translate('messages.server')}})</label>
                                <input type="text"  id="map_api_key_server" placeholder="{{translate('messages.map_api_key')}} ({{translate('messages.server')}})" class="form-control" name="map_api_key_server"
                                    value="{{$map_api_key_server??''}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('messages.save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

