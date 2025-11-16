@extends('layouts.admin.app')

@section('title', translate('Restaurant_setup'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-wrap justify-content-between align-items-start">
                <h1 class="page-header-title mr-3">
                    <span class="page-header-icon">
                        <i class="tio-business"></i>
                    </span>
                    <span>
                        {{ translate('messages.business_setup') }}
                    </span>
                </h1>
                <div class="d-flex flex-wrap justify-content-end align-items-center flex-grow-1">
