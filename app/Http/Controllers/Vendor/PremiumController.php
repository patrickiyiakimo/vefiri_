<?php

namespace App\Http\Controllers\Vendor;

use App\Exceptions\PaystackException;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorPremiumPaymentRequest;
use App\Models\Vendor;
use App\Services\VendorPremiumService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PremiumController extends Controller
{
    use ProvidesStorefrontData;

    public function show(Request $request): View
    {
        $vendor = $this->vendor($request);

        return view('vendor.premium.show', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'idempotencyKey' => (string) Str::uuid(),
            'unitPrice' => (int) config('vendor.product_slots.price'),
            'bundles' => (array) config('vendor.product_slots.bundles', []),
            'currency' => (string) config('vendor.product_slots.currency', 'NGN'),
            'freeProductLimit' => $vendor->freeProductLimit(),
            'productSlotCount' => $vendor->product_slots,
            'productLimit' => $vendor->productLimit(),
            'productCount' => $vendor->products()->count(),
            'latestPayment' => $vendor->premiumPayments()->latest()->first(),
        ]);
    }

    public function store(StoreVendorPremiumPaymentRequest $request, VendorPremiumService $premium): RedirectResponse
    {
        try {
            $payment = $premium->initialize(
                $this->vendor($request),
                (string) $request->validated('idempotency_key'),
                (int) $request->validated('quantity'),
            );
        } catch (PaystackException $exception) {
            report($exception);

            return redirect()
                ->route('vendor.premium.show')
                ->withErrors(['premium' => 'We could not start the Paystack payment. Please try again.']);
        }

        if ($payment->authorization_url === null) {
            throw ValidationException::withMessages([
                'premium' => 'This payment is still being prepared. Please try again.',
            ]);
        }

        return redirect()->away($payment->authorization_url);
    }

    public function callback(Request $request, VendorPremiumService $premium): View
    {
        $vendor = $this->vendor($request);
        $reference = $request->string('reference')->toString()
            ?: $request->string('trxref')->toString();

        $payment = null;

        if ($reference !== '') {
            $payment = $vendor->premiumPayments()
                ->where('reference', $reference)
                ->first();
        }

        if ($payment !== null) {
            try {
                $premium->confirm($payment);
            } catch (PaystackException $exception) {
                report($exception);
            }

            $payment->refresh();
            $vendor->refresh();
        }

        return view('vendor.premium.callback', [
            ...$this->sharedData(),
            'vendor' => $vendor,
            'payment' => $payment,
            'reference' => $reference,
        ]);
    }

    private function vendor(Request $request): Vendor
    {
        return $request->user()->vendor()->firstOrFail();
    }
}
