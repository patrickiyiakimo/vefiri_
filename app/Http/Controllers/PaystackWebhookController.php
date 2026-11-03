<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->string('event')->toString() !== 'charge.success') {
            return response()->json(['status' => 'ignored']);
        }

        $reference = $request->input('data.reference');

        if (! is_string($reference) || $reference === '') {
            return response()->json(['status' => 'ignored', 'message' => 'Missing payment reference.']);
        }

        $order = DB::transaction(function () use ($reference): ?Order {
            $order = Order::query()
                ->where('payment_reference', $reference)
                ->lockForUpdate()
                ->first();

            if ($order === null) {
                return null;
            }

            if ($order->status !== OrderStatus::Paid) {
                $order->update([
                    'status' => OrderStatus::Paid,
                    'paid_at' => now(),
                ]);
            }

            return $order;
        });

        if ($order === null) {
            return response()->json(['status' => 'ignored', 'message' => 'Order not found.']);
        }

        return response()->json(['status' => 'success']);
    }
}
