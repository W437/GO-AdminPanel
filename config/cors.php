<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'v1/*', 'v2/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://hopa.delivery',
        'https://www.hopa.delivery',
        'https://api.hopa.delivery',
        'https://hq-secure-panel-1337.hopa.delivery',
        'https://admin.hopa.delivery', // Keep for backward compatibility during transition
        'http://localhost:3000', // React development
        'http://localhost:3001', // Alternative React dev port
        'http://localhost:8000', // Laravel development
        'http://127.0.0.1:8000', // Laravel development
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];