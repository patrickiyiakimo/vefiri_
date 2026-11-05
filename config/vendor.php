<?php

$premiumProductLimit = env('VENDOR_PREMIUM_PRODUCT_LIMIT');

return [

    /*
    |--------------------------------------------------------------------------
    | Vendor product limits
    |--------------------------------------------------------------------------
    |
    | Free vendors can publish up to the free limit. Premium vendors use the
    | premium limit; a null premium limit means unlimited products.
    |
    */

    'free_product_limit' => (int) env('VENDOR_FREE_PRODUCT_LIMIT', 10),

    'premium_product_limit' => is_numeric($premiumProductLimit) ? (int) $premiumProductLimit : null,

    /*
    |--------------------------------------------------------------------------
    | Premium plan
    |--------------------------------------------------------------------------
    |
    | Price is stored in the currency subunit (kobo) to match Paystack. Set the
    | duration to zero for a non-expiring premium entitlement.
    |
    */

    'premium' => [
        'price' => (int) env('VENDOR_PREMIUM_PRICE', 500_000),
        'currency' => env('VENDOR_PREMIUM_CURRENCY', env('PAYSTACK_CURRENCY', 'NGN')),
        'duration_days' => (int) env('VENDOR_PREMIUM_DURATION_DAYS', 30),
    ],

];
