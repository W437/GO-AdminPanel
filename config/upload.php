<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PHP Upload Configuration
    |--------------------------------------------------------------------------
    |
    | These settings control PHP's file upload limits. They are read from
    | environment variables and applied at runtime via AppServiceProvider.
    |
    | These settings override php.ini values when the application boots.
    |
    */

    'max_filesize' => env('PHP_UPLOAD_MAX_FILESIZE', '20M'),
    'post_max_size' => env('PHP_POST_MAX_SIZE', '25M'),
    'max_execution_time' => env('PHP_MAX_EXECUTION_TIME', 300),
    'max_input_time' => env('PHP_MAX_INPUT_TIME', 300),
    'memory_limit' => env('PHP_MEMORY_LIMIT', '256M'),

];
