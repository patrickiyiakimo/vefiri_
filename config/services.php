<?php

$cloudinaryUrl = env('CLOUDINARY_URL');
$cloudinaryParts = is_string($cloudinaryUrl) && $cloudinaryUrl !== '' ? parse_url($cloudinaryUrl) : [];
$cloudinaryParts = is_array($cloudinaryParts) ? $cloudinaryParts : [];

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cloudinary' => [
        'cloud' => env('CLOUDINARY_CLOUD_NAME', (string) ($cloudinaryParts['host'] ?? '')),
        'key' => env('CLOUDINARY_API_KEY', rawurldecode((string) ($cloudinaryParts['user'] ?? ''))),
        'secret' => env('CLOUDINARY_API_SECRET', rawurldecode((string) ($cloudinaryParts['pass'] ?? ''))),
        'api_base' => env('CLOUDINARY_API_BASE', 'https://api.cloudinary.com'),
        'public_id_prefix' => env('CLOUDINARY_PUBLIC_ID_PREFIX', 'cloudinary/vendor-products'),
        'delivery_transformations' => env('CLOUDINARY_DELIVERY_TRANSFORMATIONS', 'f_auto,q_auto,c_limit,w_1200'),
        'signature_ttl_minutes' => (int) env('CLOUDINARY_SIGNATURE_TTL_MINUTES', 55),
        'connect_timeout' => (int) env('CLOUDINARY_CONNECT_TIMEOUT', 3),
        'timeout' => (int) env('CLOUDINARY_TIMEOUT', 15),
    ],

];
