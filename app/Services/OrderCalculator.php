<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;

class OrderCalculator
{
    public function __construct(private readonly CouponService $coupons) {}

    /**
     * @param  array<int, array<string, mixed>>  $lineItems
     * @return array{
     *     subtotal: int,
     *     discount_total: int,
     *     taxable_amount: int,
     *     tax_total: int,
     *     shipping_total: int,
     *     total: int,
     *     tax_rate: string,
     *     tax_after_discount: bool,
     *     coupon_id: int|null,
     *     coupon_code: string|null,
     *     coupon_name: string|null,
     *     free_shipping: bool
     * }
     */
    public function calculate(
        array $lineItems,
        ?string $couponCode = null,
        bool $lockCoupon = false,
        ?string $taxRate = null,
        ?bool $taxAfterDiscount = null,
    ): array {
        $coupon = $this->coupons->requireActive($couponCode, $lockCoupon);

        if ($coupon instanceof Coupon) {
            $this->coupons->ensureApplicable($coupon, $lineItems);
        }

        $subtotal = (int) collect($lineItems)->sum('line_total');
        $discount = $coupon instanceof Coupon
            ? $this->discountFor($coupon, $lineItems, $subtotal)
            : 0;

        $shipping = $coupon?->isFreeShipping()
            ? 0
            : max(0, (int) config('checkout.shipping_fee', 0));

        $rate = max(0, (float) ($taxRate ?? config('checkout.tax_rate', 7.5)));
        $applyTaxAfterDiscount = $taxAfterDiscount ?? (bool) config('checkout.tax_after_discount', true);

        $taxableAmount = $applyTaxAfterDiscount
            ? max(0, $subtotal - $discount)
            : $subtotal;

        $tax = (int) round($taxableAmount * ($rate / 100));

        $total = $applyTaxAfterDiscount
            ? $taxableAmount + $tax + $shipping
            : max(0, $subtotal - $discount) + $tax + $shipping;

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'taxable_amount' => $taxableAmount,
            'tax_total' => $tax,
            'shipping_total' => $shipping,
            'total' => $total,
            'tax_rate' => (string) $rate,
            'tax_after_discount' => $applyTaxAfterDiscount,
            'coupon_id' => $coupon?->getKey(),
            'coupon_code' => $coupon?->code,
            'coupon_name' => $coupon?->name,
            'free_shipping' => $coupon?->isFreeShipping() ?? false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forOrder(Order $order, bool $lockCoupon = false): array
    {
        $lineItems = $order->items()
            ->get()
            ->map(fn (OrderItem $item): array => [
                'vendor_id' => $item->vendor_id,
                'line_total' => (int) $item->line_total,
            ])
            ->all();

        return $this->calculate(
            $lineItems,
            $order->coupon_code,
            $lockCoupon,
            (string) $order->tax_rate,
            (bool) $order->tax_after_discount,
        );
    }

    /**
     * @param  array<string, mixed>  $totals
     */
    public function matchesOrder(Order $order, array $totals): bool
    {
        return (int) $order->subtotal === (int) $totals['subtotal']
            && (int) $order->discount_total === (int) $totals['discount_total']
            && (int) $order->taxable_amount === (int) $totals['taxable_amount']
            && (int) $order->tax_total === (int) $totals['tax_total']
            && (int) $order->shipping_total === (int) $totals['shipping_total']
            && (int) $order->total === (int) $totals['total'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lineItems
     */
    private function discountFor(Coupon $coupon, array $lineItems, int $subtotal): int
    {
        $eligibleSubtotal = $this->coupons->eligibleSubtotal($coupon, $lineItems);

        $discount = match ($coupon->type) {
            CouponType::Fixed => (int) ($coupon->amount ?? 0),
            CouponType::Percentage => (int) round(
                $eligibleSubtotal * ((float) $coupon->percentage / 100),
            ),
            CouponType::FreeShipping => 0,
        };

        return max(0, min($discount, $eligibleSubtotal, $subtotal));
    }
}
