<?php

namespace App\Models;

use App\Enums\VendorPremiumPaymentStatus;
use Database\Factories\VendorPremiumPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'vendor_id',
    'reference',
    'amount',
    'currency',
    'status',
    'idempotency_key',
    'authorization_url',
    'metadata',
    'paid_at',
])]
class VendorPremiumPayment extends Model
{
    /** @use HasFactory<VendorPremiumPaymentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'status' => VendorPremiumPaymentStatus::class,
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function isPaid(): bool
    {
        return $this->status === VendorPremiumPaymentStatus::Paid;
    }
}
