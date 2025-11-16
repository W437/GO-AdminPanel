@extends('layouts.admin.app')

@section('title', translate('Admin_Landing_Page'))


@section('content')
<?php
use Illuminate\Support\Facades\File;

$filePath = resource_path('views/layouts/landing/custom/index.blade.php');

$custom_file = File::exists($filePath);
$config = \App\CentralLogics\Helpers::get_business_settings('landing_page');
?>


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
                @if ( isset($config) && $config == 0 )

                <div class="d-flex flex-wrap justify-content-end align-items-center flex-grow-1">
