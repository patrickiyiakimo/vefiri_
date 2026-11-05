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
        return $this->plan === VendorPlan::Premium
            && ($this->premium_until === null || $this->premium_until->isFuture());
    }

    public function currentPlan(): VendorPlan
    {
        return $this->isPremium() ? VendorPlan::Premium : VendorPlan::Free;
    }

    public function productLimit(): ?int
    {
        if (! $this->isPremium()) {
            return (int) config('vendor.free_product_limit', 10);
        }

        $limit = config('vendor.premium_product_limit');

        return is_numeric($limit) ? (int) $limit : null;
    }

    public function hasReachedProductLimit(): bool
    {
        $limit = $this->productLimit();

        return $limit !== null && $this->products()->count() >= $limit;
    }
}
