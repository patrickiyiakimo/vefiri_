<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;

class OrderDeliveryService
{
    public function markDelivered(Order $order): bool
    {
        $updated = Order::query()
            ->whereKey($order->getKey())
            ->where('status', OrderStatus::Paid)
            ->update([
                'status' => OrderStatus::Delivered,
                'delivered_at' => now(),
            ]);

        if ($updated === 0) {
            return false;
        }

        $order->refresh();

        return true;
    }
}
