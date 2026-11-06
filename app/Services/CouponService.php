<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function resolve(?string $code, bool $lockForUpdate = false): ?Coupon
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        return Coupon::query()
            ->active()
            ->code($code)
            ->when(
                $lockForUpdate,
                fn (Builder $query): Builder => $query->lockForUpdate(),
            )
            ->first();
    }

    public function requireActive(?string $code, bool $lockForUpdate = false): ?Coupon
    {
        if (trim((string) $code) === '') {
            return null;
        }

        $coupon = $this->resolve($code, $lockForUpdate);

        if (! $coupon instanceof Coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon is invalid or has expired.',
            ]);
        }

        return $coupon;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lineItems
     */
    public function eligibleSubtotal(Coupon $coupon, array $lineItems): int
    {
        return (int) collect($lineItems)
            ->filter(fn (array $item): bool => $coupon->vendor_id === null
                || (int) ($item['vendor_id'] ?? 0) === (int) $coupon->vendor_id)
            ->sum('line_total');
    }

    /**
     * @param  array<int, array<string, mixed>>  $lineItems
     */
    public function ensureApplicable(Coupon $coupon, array $lineItems): void
    {
        if ($this->eligibleSubtotal($coupon, $lineItems) <= 0) {
            throw ValidationException::withMessages([
                'coupon_code' => 'This coupon does not apply to any items in your cart.',
            ]);
        }
    }
}
