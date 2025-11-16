@extends('layouts.admin.app')

@section('title',translate('FCM Settings'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-notifications"></i>
                </span>
                <span>{{translate('messages.firebase_push_notification_setup')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-header card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 gap-2">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="{{ route('admin.business-settings.fcm-index') }}" class="nav-link pb-2 px-0 pb-sm-3 active" data-slide="1">
                                <img src="{{dynamicAsset('/public/assets/admin/img/notify.png')}}" alt="">
                                <span>{{translate('Push Notification')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business-settings.fcm-config') }}" class="nav-link pb-2 px-0 pb-sm-3" data-slide="2">
                                <img src="{{dynamicAsset('/public/assets/admin/img/firebase2.png')}}" alt="">
                                <span>{{translate('Firebase Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="tab--content">
                            <div class="item show text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#push-notify-modal">
                                <strong class="mr-2">{{translate('Read Documentation')}}</strong>
