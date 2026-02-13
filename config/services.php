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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

        'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mtn' => [
        'api_url' => env('MTN_API_BASE_URI'),
        'subscription_key' => env('MTN_COLLECTION_SUBSCRIPTION_KEY'),
        'user_id' => env('MTN_COLLECTION_ID'),
        'api_secret' => env('MTN_COLLECTION_SECRET'),
        'environment' => env('MTN_ENVIRONMENT'),
        'currency' => env('MTN_CURRENCY'),
        'party_id_type' => env('MTN_COLLECTION_PARTY_ID_TYPE','msisdn'),
        'redirect_uri' => env('MTN_COLLECTION_REDIRECT_URI'),
    ],

    'airtel' => [
        'base_url' => env('AIRTEL_API_URL'),
        'client_id' => env('AIRTEL_CLIENT_ID'),
        'client_secret' => env('AIRTEL_CLIENT_SECRET'),
    ],

];
