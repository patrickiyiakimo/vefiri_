<?php

namespace App\Models;

use App\Enums\VendorApplicationStatus;
use Database\Factories\VendorApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'business_name',
    'store_name',
    'business_email',
    'phone',
    'address',
    'city',
    'state',
    'business_registration_number',
    'description',
    'product_categories',
    'status',
    'admin_note',
    'reviewed_by',
    'reviewed_at',
])]
class VendorApplication extends Model
{
    /** @use HasFactory<VendorApplicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'product_categories' => 'array',
            'status' => VendorApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', VendorApplicationStatus::Pending);
    }

    public function isPending(): bool
    {
        return $this->status === VendorApplicationStatus::Pending;
    }
}
