<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\VendorPremiumPayment;
use App\Services\OrderPaymentService;
use App\Services\VendorPremiumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaystackWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        OrderPaymentService $payments,
        VendorPremiumService $premium,
    ): JsonResponse {
        $reference = $request->input('data.reference');

        if (! is_string($reference) || $reference === '') {
            return response()->json(['status' => 'ignored', 'message' => 'Missing payment reference.']);
        }

        $event = $request->string('event')->toString();

        if ($event === 'charge.success') {
            return $this->handleSuccessfulCharge($reference, $payments, $premium);
        }

        if ($event === 'charge.failed') {
            $premiumPayment = $this->findPremiumPayment($reference);

            if ($premiumPayment !== null) {
                $premium->markFailed($premiumPayment);

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'ignored']);
    }

    private function handleSuccessfulCharge(
        string $reference,
        OrderPaymentService $payments,
        VendorPremiumService $premium,
    ): JsonResponse {
        $premiumPayment = $this->findPremiumPayment($reference);

        if ($premiumPayment !== null) {
            $confirmed = $premium->confirm($premiumPayment);

            return response()->json(['status' => $confirmed ? 'success' : 'ignored']);
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

    private function findPremiumPayment(string $reference): ?VendorPremiumPayment
    {
        return VendorPremiumPayment::query()
            ->where('reference', $reference)
            ->first();
    }
}
