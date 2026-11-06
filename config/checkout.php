<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tax settings
    |--------------------------------------------------------------------------
    |
    | The rate is a percentage. Set CHECKOUT_TAX_AFTER_DISCOUNT to false to
    | calculate tax on the pre-discount subtotal instead of the discounted
    | taxable base. Order totals are always stored in the currency subunit.
    |
    */

    'tax_rate' => (float) env('CHECKOUT_TAX_RATE', 7.5),

    'tax_after_discount' => (bool) env('CHECKOUT_TAX_AFTER_DISCOUNT', true),

    /*
    |--------------------------------------------------------------------------
    | Shipping
    |--------------------------------------------------------------------------
    |
    | Vefiri currently offers free delivery, so this defaults to zero. A
    | free-shipping coupon will still force the shipping total to zero.
    |
    */

    'shipping_fee' => (int) env('CHECKOUT_SHIPPING_FEE', 0),

];
