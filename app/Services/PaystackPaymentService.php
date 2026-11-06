<?php

namespace App\Services;

use App\Exceptions\PaystackException;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorPremiumPayment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class PaystackPaymentService
{
    /**
     * Initialize a single-account Paystack transaction for the full order total.
     *
     * Every payment settles into the platform's main Paystack account. Vendor
     * line totals are attached as metadata so the admin can pay each vendor
     * manually after the order is fulfilled.
     *
     * @param  array<int, array<string, mixed>>  $cartItems
     */
    public function initializeCheckout(Order $order, array $cartItems, string $idempotencyKey): string
    {
        $items = collect($cartItems);

        if ($items->isEmpty()) {
            throw new InvalidArgumentException('An order must contain at least one item before payment can be initialized.');
        }

        if (! is_string($order->payment_reference) || $order->payment_reference === '') {
            throw new PaystackException('The order is missing its Paystack payment reference.');
        }

        $amount = (int) $order->total;

        if ($amount <= 0) {
            throw new PaystackException('The order total must be greater than zero.');
        }

        $vendorTotals = $items
            ->filter(fn (array $item): bool => filled($item['vendor_id'] ?? null))
            ->groupBy('vendor_id')
            ->map(fn (Collection $group): int => (int) $group->sum('line_total'));

        $payload = [
            'email' => $order->customer_email,
            'amount' => $order->total,
            'currency' => config('paystack.currency', 'NGN'),
            'reference' => $order->payment_reference,
            'metadata' => [
                'order_id' => $order->getKey(),
                'idempotency_key' => $idempotencyKey,
                'subtotal' => (int) $order->subtotal,
                'discount_total' => (int) $order->discount_total,
                'tax_total' => (int) $order->tax_total,
                'shipping_total' => (int) $order->shipping_total,
                'coupon_code' => $order->coupon_code,
                'vendors' => $vendorTotals->isEmpty() ? [] : $this->vendorBreakdown($vendorTotals),
            ],
        ];

        $callbackUrl = config('paystack.callback_url');

        if (is_string($callbackUrl) && $callbackUrl !== '') {
            $payload['callback_url'] = $callbackUrl;
        }

        $response = $this->send('post', '/transaction/initialize', $payload);

        $this->ensureSuccessful($response, 'Paystack could not initialize the checkout transaction.');

        $authorizationUrl = $response->json('data.authorization_url');

        if (! is_string($authorizationUrl) || $authorizationUrl === '') {
            throw new PaystackException('Paystack returned an invalid checkout URL.');
        }

        return $authorizationUrl;
    }

    public function initializePremiumPayment(Vendor $vendor, VendorPremiumPayment $payment): string
    {
        $email = (string) ($vendor->business_email ?: $vendor->user()->value('email'));

        if ($email === '') {
            throw new PaystackException('The vendor does not have an email address for payment.');
        }

        $reference = (string) $payment->reference;

        if ($reference === '') {
            throw new PaystackException('The premium payment is missing its Paystack reference.');
        }

        $payload = [
            'email' => $email,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'reference' => $reference,
            'metadata' => [
                'type' => 'vendor_premium',
                'vendor_id' => $vendor->getKey(),
                'payment_id' => $payment->getKey(),
                'idempotency_key' => $payment->idempotency_key,
            ],
        ];

        $callbackUrl = config('paystack.premium_callback_url');

        if (is_string($callbackUrl) && $callbackUrl !== '') {
            $payload['callback_url'] = $callbackUrl;
        }

        $response = $this->send('post', '/transaction/initialize', $payload);

        $this->ensureSuccessful($response, 'Paystack could not initialize the premium upgrade payment.');

        $authorizationUrl = $response->json('data.authorization_url');

        if (! is_string($authorizationUrl) || $authorizationUrl === '') {
            throw new PaystackException('Paystack returned an invalid premium checkout URL.');
        }

        return $authorizationUrl;
    }

    public function verifyCheckout(Order $order): bool
    {
        return $this->verifyTransaction(
            (string) $order->payment_reference,
            (int) $order->total,
            (string) config('paystack.currency', 'NGN'),
        );
    }

    public function verifyTransaction(string $reference, int $amount, string $currency): bool
    {
        if ($reference === '') {
            return false;
        }

        $response = $this->send('get', '/transaction/verify/'.rawurlencode($reference), []);

        if (! $response->successful() || $response->json('status') !== true) {
            return false;
        }

        $data = $response->json('data');

        if (! is_array($data)) {
            return false;
        }

        return ($data['status'] ?? null) === 'success'
            && ($data['reference'] ?? null) === $reference
            && (int) ($data['amount'] ?? 0) === $amount
            && strtoupper((string) ($data['currency'] ?? '')) === strtoupper($currency);
    }

    /**
     * @param  Collection<int|string, int>  $vendorTotals
     * @return array<int, array{vendor_id: int|string, store_name: string, line_total: int}>
     */
    private function vendorBreakdown(Collection $vendorTotals): array
    {
        $vendors = Vendor::query()
            ->whereKey($vendorTotals->keys()->all())
            ->get()
            ->keyBy('id');

        return $vendorTotals
            ->map(function (int $lineTotal, int|string $vendorId) use ($vendors): array {
                $vendor = $vendors->get($vendorId);

                if (! $vendor instanceof Vendor) {
                    throw new PaystackException('The order references a vendor that no longer exists.');
                }

                return [
                    'vendor_id' => $vendor->getKey(),
                    'store_name' => $vendor->store_name,
                    'line_total' => $lineTotal,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function send(string $method, string $path, array $payload): Response
    {
        $secretKey = (string) config('paystack.secret_key');

        if ($secretKey === '') {
            throw new PaystackException('The Paystack secret key is not configured.');
        }

        try {
            return $this->request($secretKey)->{$method}($path, $payload);
        } catch (ConnectionException $exception) {
            throw new PaystackException(
                'Paystack could not be reached. Please try again.',
                null,
                null,
                $exception,
            );
        }
    }

    private function request(string $secretKey): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('paystack.base_url', 'https://api.paystack.co'), '/'))
            ->withToken($secretKey)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('paystack.timeout', 30));
    }

    private function ensureSuccessful(Response $response, string $fallbackMessage): void
    {
        if ($response->successful() && $response->json('status') === true) {
            return;
        }

        $message = $response->json('message');

        throw new PaystackException(
            $fallbackMessage,
            $response->status(),
            is_string($message) ? $message : null,
        );
    }
}
