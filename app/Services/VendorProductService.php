<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorProductService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(Vendor $vendor, array $attributes): Product
    {
        return DB::transaction(function () use ($vendor, $attributes): Product {
            $lockedVendor = Vendor::query()
                ->whereKey($vendor->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureWithinProductLimit($lockedVendor);

            return $lockedVendor->products()->create($attributes);
        });
    }

    private function ensureWithinProductLimit(Vendor $vendor): void
    {
        $limit = $vendor->productLimit();

        if ($limit === null || $vendor->products()->count() < $limit) {
            return;
        }

        throw ValidationException::withMessages([
            'limit' => $vendor->isPremium()
                ? "Your Premium plan allows up to {$limit} products."
                : "Your free plan allows up to {$limit} products. Upgrade to Premium to add more.",
        ]);
    }
}
