<?php

namespace App\Http\Requests\Concerns;

use App\Services\ProductCatalog;
use Illuminate\Contracts\Validation\Validator;

trait RejectsOwnVendorProducts
{
    protected function rejectOwnVendorProducts(Validator $validator, string $message): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $product = app(ProductCatalog::class)->find((string) $this->input('product_id'));
        $productVendorId = $product['vendor_id'] ?? null;
        $userVendorId = $this->user()?->vendor?->getKey();

        if ($productVendorId !== null && $userVendorId !== null && (int) $productVendorId === (int) $userVendorId) {
            $validator->errors()->add('product_id', $message);
        }
    }
}
