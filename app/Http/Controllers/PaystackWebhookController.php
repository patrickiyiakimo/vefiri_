<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request, OrderPaymentService $payments): JsonResponse
    {
        if ($request->string('event')->toString() !== 'charge.success') {
            return response()->json(['status' => 'ignored']);
        }

        $reference = $request->input('data.reference');

        if (! is_string($reference) || $reference === '') {
            return response()->json(['status' => 'ignored', 'message' => 'Missing payment reference.']);
        }

        $order = Order::query()
            ->where('payment_reference', $reference)
            ->first();

        if ($order === null) {
            return response()->json(['status' => 'ignored', 'message' => 'Order not found.']);
        }

        $payments->confirm($order);

        return response()->json(['status' => 'success']);
    }
}
