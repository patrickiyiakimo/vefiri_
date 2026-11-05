<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack API credentials
    |--------------------------------------------------------------------------
    |
    | The secret key is used for every server-side Paystack request and for
    | validating the X-Paystack-Signature header on incoming webhooks.
    |
    */

    'secret_key' => env('PAYSTACK_SECRET_KEY'),

    'public_key' => env('PAYSTACK_PUBLIC_KEY'),

    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),

    'currency' => env('PAYSTACK_CURRENCY', 'NGN'),

    'timeout' => (int) env('PAYSTACK_TIMEOUT', 30),

    'callback_url' => env('PAYSTACK_CALLBACK_URL', env('APP_URL').'/checkout/callback'),

    'premium_callback_url' => env('PAYSTACK_PREMIUM_CALLBACK_URL', env('APP_URL').'/vendor/premium/callback'),

];
