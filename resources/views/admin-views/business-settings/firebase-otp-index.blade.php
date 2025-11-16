@extends('layouts.admin.app')

@section('title', translate('Firebase OTP Verification'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-key"></i>
                </span>
                <span>
                    {{translate('Firebase OTP Verification')}}
                </span>
            </h1>
            @include('admin-views.business-settings.partials.third-party-links')
                <div class="">
                    <div class="text--primary-2  mx-4 d-flex flex-wrap justify-content-end align-items-center" type="button" data-toggle="modal" data-target="#instructionsModal">
                        <strong class="mr-2">{{translate('How it Works')}}</strong>
