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

    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),

    'currency' => env('PAYSTACK_CURRENCY', 'NGN'),

    'timeout' => (int) env('PAYSTACK_TIMEOUT', 30),

    'callback_url' => env('PAYSTACK_CALLBACK_URL', env('APP_URL').'/checkout/callback'),

    /*
    |--------------------------------------------------------------------------
    | Marketplace commission
    |--------------------------------------------------------------------------
    |
    | Percentage commissions are expressed as a percentage of the order total.
    | Flat commissions must be provided in the currency subunit (kobo for NGN).
    |
    */

    'commission' => [
        'type' => env('PAYSTACK_PLATFORM_COMMISSION_TYPE', 'percentage'),
        'value' => (float) env('PAYSTACK_PLATFORM_COMMISSION_VALUE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic split configuration
    |--------------------------------------------------------------------------
    |
    | Paystack's "account" bearer type means the platform's main account bears
    | the Paystack transaction fee. The platform commission remains in the main
    | account after each vendor's flat share has been settled.
    |
    */

    'split' => [
        'type' => 'flat',
        'bearer_type' => env('PAYSTACK_SPLIT_BEARER_TYPE', 'account'),
    ],

];
