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
        if (! $vendor->hasReachedProductLimit()) {
            return;
        }

        $limit = $vendor->productLimit();

        throw ValidationException::withMessages([
            'limit' => "You have reached your product limit of {$limit}. Buy more product slots to add more.",
        ]);
    }
}
