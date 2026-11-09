<?php

$productSlotPrice = (int) env('VENDOR_PRODUCT_SLOT_PRICE', 100_000);

$productSlotBundles = array_values(array_unique(array_filter(
    array_map('intval', explode(',', (string) env('VENDOR_PRODUCT_SLOT_BUNDLES', '10,20,30,40'))),
    fn (int $quantity): bool => $quantity > 0,
)));

if ($productSlotBundles === []) {
    $productSlotBundles = [10, 20, 30, 40];
}

sort($productSlotBundles);

return [

    /*
    |--------------------------------------------------------------------------
    | Vendor product limits
    |--------------------------------------------------------------------------
    |
    | Every vendor can publish the free product allowance. Vendors buy extra,
    | permanent product slots in bundles. Their total limit is the free
    | allowance plus the number of slots they have purchased.
    |
    */

    'free_product_limit' => (int) env('VENDOR_FREE_PRODUCT_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | Product slots
    |--------------------------------------------------------------------------
    |
    | Price is stored in the currency subunit (kobo) to match Paystack. Slots
    | never expire and are sold in the bundles listed below.
    |
    */

    'product_slots' => [
        'price' => $productSlotPrice,
        'bundles' => $productSlotBundles,
        'currency' => env('VENDOR_PRODUCT_SLOT_CURRENCY', env('PAYSTACK_CURRENCY', 'NGN')),
    ],

];
