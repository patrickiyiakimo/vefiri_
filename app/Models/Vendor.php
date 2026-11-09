<?php

namespace App\Models;

use App\Enums\VendorPlan;
use Database\Factories\VendorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'business_name',
    'store_name',
    'business_email',
    'phone',
    'address',
    'city',
    'state',
    'description',
    'plan',
    'premium_started_at',
    'premium_until',
    'product_slots',
])]
class Vendor extends Model
{
    /** @use HasFactory<VendorFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'plan' => VendorPlan::class,
            'premium_started_at' => 'datetime',
            'premium_until' => 'datetime',
            'product_slots' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function premiumPayments(): HasMany
    {
        return $this->hasMany(VendorPremiumPayment::class);
    }

    public function isPremium(): bool
    {
        return $this->product_slots > 0;
    }

    public function currentPlan(): VendorPlan
    {
        return $this->isPremium() ? VendorPlan::Premium : VendorPlan::Free;
    }

    public function freeProductLimit(): int
    {
        return max(0, (int) config('vendor.free_product_limit', 10));
    }

    public function productLimit(): int
    {
        return $this->freeProductLimit() + $this->product_slots;
    }

    public function remainingProductSlots(): int
    {
        return max(0, $this->productLimit() - $this->products()->count());
    }

    public function hasReachedProductLimit(): bool
    {
        return $this->products()->count() >= $this->productLimit();
    }
}
