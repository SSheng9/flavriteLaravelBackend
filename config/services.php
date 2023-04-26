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

    'google' => [
        'client_id' => '489828660540-0ek0uad92j7t76f4b8fq1rqjkr4htbem.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => 'https://api.flavrite.com/callback/g',
        // 'redirect' => 'http://127.0.0.1:8000/callback/g',
    ],

    'facebook' => [
        'client_id' => '695914695054077',
        'client_secret' => 'd1cecb6dda33717394daa6b8fc461974',
        'redirect' => 'https://api.flavrite.com/callback/g',
        // 'redirect' => 'http://127.0.0.1:8000/callback/f',
    ],

];
