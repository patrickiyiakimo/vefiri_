<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderPaymentService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly OrderCalculator $calculator,
    ) {}

    public function confirm(Order $order): bool
    {
        $confirmed = DB::transaction(function () use ($order): bool {
            $lockedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->first();

            if ($lockedOrder === null) {
                return false;
            }

            if (in_array($lockedOrder->status, [OrderStatus::Paid, OrderStatus::Delivered], true)) {
                return true;
            }

            if (filled($lockedOrder->coupon_code) && ! $this->couponStillMatches($lockedOrder)) {
                return false;
            }

            $metadata = $lockedOrder->metadata ?? [];
            unset($metadata['coupon_revalidation_failed']);

            $lockedOrder->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
                'metadata' => $metadata,
            ]);

            $this->cart->removePurchasedItems($lockedOrder);

            return true;
        });

        $order->refresh();

        return $confirmed;
    }

    private function couponStillMatches(Order $order): bool
    {
        try {
            $totals = $this->calculator->forOrder($order, lockCoupon: true);
        } catch (ValidationException $exception) {
            $this->recordRevalidationFailure(
                $order,
                'coupon',
                $exception->validator->errors()->first('coupon_code')
                    ?: 'This coupon is no longer valid.',
            );

            return false;
        }

        if (! $this->calculator->matchesOrder($order, $totals)) {
            $this->recordRevalidationFailure(
                $order,
                'totals',
                'The order total no longer matches the coupon terms.',
            );

            return false;
        }

        return true;
    }

    private function recordRevalidationFailure(Order $order, string $reason, string $message): void
    {
        $metadata = $order->metadata ?? [];
        $metadata['coupon_revalidation_failed'] = [
            'reason' => $reason,
            'message' => $message,
            'checked_at' => now()->toIso8601String(),
        ];

        $order->update(['metadata' => $metadata]);

        Log::warning('Order coupon revalidation failed.', [
            'order_id' => $order->getKey(),
            'payment_reference' => $order->payment_reference,
            'reason' => $reason,
            'message' => $message,
        ]);
    }
}
