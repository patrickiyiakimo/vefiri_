<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'delivery_address',
    'delivery_address_line_2',
    'delivery_city',
    'delivery_state',
    'delivery_postal_code',
    'delivery_notes',
    'subtotal',
    'discount_total',
    'taxable_amount',
    'tax_total',
    'shipping_total',
    'tax_rate',
    'tax_after_discount',
    'coupon_id',
    'coupon_code',
    'total',
    'payment_reference',
    'status',
    'idempotency_key',
    'checkout_url',
    'metadata',
    'paid_at',
    'delivered_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'discount_total' => 'integer',
            'taxable_amount' => 'integer',
            'tax_total' => 'integer',
            'shipping_total' => 'integer',
            'tax_rate' => 'decimal:2',
            'tax_after_discount' => 'boolean',
            'total' => 'integer',
            'status' => OrderStatus::class,
            'metadata' => 'array',
            'paid_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, [OrderStatus::Paid, OrderStatus::Delivered], true);
    }

    public function isDelivered(): bool
    {
        return $this->status === OrderStatus::Delivered;
    }
}
