<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_TRANSLATION_MODEL', 'gpt-4o-mini'),
        'parallel_workers' => env('OPENAI_PARALLEL_WORKERS', 20),
        'batch_size' => env('OPENAI_BATCH_SIZE', 200),
        'items_per_iteration' => env('OPENAI_ITEMS_PER_ITERATION') ?: (env('OPENAI_PARALLEL_WORKERS', 20) * env('OPENAI_BATCH_SIZE', 200)),
        'timeout' => env('OPENAI_TIMEOUT', 60),
        'max_retries' => env('OPENAI_MAX_RETRIES', 2),
        'retry_delay_ms' => env('OPENAI_RETRY_DELAY_MS', 500),
    ],

];
