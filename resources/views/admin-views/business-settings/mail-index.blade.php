@extends('layouts.admin.app')

@section('title',translate('messages.settings'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-email"></i>
                </span>
                <span>{{ translate('messages.smtp_mail_setup') }}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.third-party-links')
        </div>
        <!-- End Page Header -->

        <div class="card min-h-60vh">
            <div class="card-header card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 gap-2">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="#mail-config" data-toggle="tab" class="nav-link pb-2 px-0 pb-sm-3 active">
                                <img src="{{dynamicAsset('/public/assets/admin/img/mail-config.png')}}" alt="">
                                <span>{{translate('Mail_Config')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#test-mail" data-toggle="tab" class="nav-link pb-2 px-0 pb-sm-3">
                                <img src="{{dynamicAsset('/public/assets/admin/img/test-mail.png')}}" alt="">
                                <span>{{translate('Send_Test_Mail')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#works-modal">
                            <strong class="mr-2">{{translate('How_it_Works')}}</strong>
