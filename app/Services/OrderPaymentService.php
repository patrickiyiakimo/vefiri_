<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    public function __construct(private readonly CartService $cart) {}

    public function confirm(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()
                ->whereKey($order->getKey())
                ->lockForUpdate()
                ->first();

            if ($lockedOrder === null) {
                return;
            }

            if ($lockedOrder->status !== OrderStatus::Paid) {
                $lockedOrder->update([
                    'status' => OrderStatus::Paid,
                    'paid_at' => now(),
                ]);

                $this->cart->removePurchasedItems($lockedOrder);
            }
        });

        $order->refresh();
    }
}
