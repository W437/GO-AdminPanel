@extends('layouts.admin.app')

@section('title', translate('messages.Notification Channels'))
@section('notification_setup')
active
@endsection

@section('content')
    <div class="content container-fluid">




        <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-code"></i>
                </span>
                <span>
                    {{translate('messages.Notification Channels Setup')}}
                </span>
            </h1>
            <div class="text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#notiifcation-how-it-works">
                <strong class="mr-2">{{translate('how_it_works!')}}</strong>
