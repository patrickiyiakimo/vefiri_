<?php

namespace App\Services;

use App\Exceptions\PaystackException;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PaystackSplitPaymentService
{
    /**
     * Register a vendor's settlement bank account with Paystack.
     *
     * The returned subaccount_code is permanent and can be reused for every
     * future split payment that includes this vendor.
     *
     * @param  array<string, mixed>  $vendorData
     */
    public function createSubaccount(array $vendorData): string
    {
        $data = Validator::make($vendorData, [
            'business_name' => ['required', 'string', 'max:255'],
            'bank_code' => ['required', 'string', 'max:20'],
            'account_number' => ['required', 'string', 'max:20'],
            'percentage_charge' => ['required', 'numeric', 'between:0,100'],
            'description' => ['nullable', 'string', 'max:255'],
            'primary_contact_email' => ['nullable', 'email', 'max:255'],
            'primary_contact_name' => ['nullable', 'string', 'max:255'],
            'primary_contact_phone' => ['nullable', 'string', 'max:32'],
        ])->validate();

        $payload = [
            'business_name' => $data['business_name'],
            'settlement_bank' => $data['bank_code'],
            'account_number' => $data['account_number'],
            'percentage_charge' => (float) $data['percentage_charge'],
        ];

        foreach (['description', 'primary_contact_email', 'primary_contact_name', 'primary_contact_phone'] as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $payload[$field] = $data[$field];
            }
        }

        $response = $this->send('post', '/subaccount', $payload);

        $this->ensureSuccessful($response, 'Paystack could not create the vendor subaccount.');

        $subaccountCode = $response->json('data.subaccount_code');

        if (! is_string($subaccountCode) || $subaccountCode === '') {
            throw new PaystackException('Paystack returned an invalid subaccount response.');
        }

        return $subaccountCode;
    }

    /**
     * Initialize a Paystack transaction with a dynamic multi-vendor split.
     *
     * Each vendor receives a flat share equal to their order line total minus
     * their proportional allocation of the platform commission. Paystack pays
     * the subaccounts immediately, while the remaining commission stays in the
     * platform's main Paystack account.
     *
     * @param  array<int, array<string, mixed>>  $cartItems
     */
    public function initializeMultiSplitCheckout(Order $order, array $cartItems, string $idempotencyKey): string
    {
        $items = collect($cartItems);

        if ($items->isEmpty()) {
            throw new InvalidArgumentException('An order must contain at least one item before payment can be initialized.');
        }

        if (! is_string($order->payment_reference) || $order->payment_reference === '') {
            throw new PaystackException('The order is missing its Paystack payment reference.');
        }

        // Group item line totals by vendor. This is the source of truth for the
        // dynamic split, so it must equal the aggregate order total exactly.
        $vendorTotals = $items
            ->groupBy('vendor_id')
            ->map(fn (Collection $group): int => (int) $group->sum('line_total'));

        $amount = (int) $vendorTotals->sum();

        if ($amount !== (int) $order->total) {
            throw new PaystackException('The order total does not match the sum of its vendor line totals.');
        }

        $vendors = Vendor::query()
            ->whereKey($vendorTotals->keys()->all())
            ->get()
            ->keyBy('id');

        $commission = $this->platformCommission($amount);

        if ($commission >= $amount) {
            throw new PaystackException('The platform commission must be less than the order total.');
        }

        [$subaccounts, $breakdown] = $this->buildSplitBreakdown($vendorTotals, $vendors, $commission, $amount);

        $payload = [
            'email' => $order->customer_email,
            'amount' => $order->total,
            'currency' => config('paystack.currency', 'NGN'),
            'reference' => $order->payment_reference,
            'metadata' => [
                'order_id' => $order->getKey(),
                'idempotency_key' => $idempotencyKey,
                'platform_commission' => $commission,
                'vendors' => $breakdown,
            ],
            /*
             * Paystack's dynamic split object. Because the split type is "flat",
             * every "share" value is an exact amount in the currency subunit
             * (kobo for NGN). The sum of all vendor shares is intentionally less
             * than the transaction amount; the difference is the platform fee
             * that remains in the main account.
             */
            'split' => [
                'type' => config('paystack.split.type', 'flat'),
                'bearer_type' => config('paystack.split.bearer_type', 'account'),
                'subaccounts' => $subaccounts,
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

    /**
     * Calculate the platform commission in the currency subunit.
     */
    private function platformCommission(int $amount): int
    {
        $type = (string) config('paystack.commission.type', 'percentage');
        $value = (float) config('paystack.commission.value', 0);

        $commission = match ($type) {
            'percentage' => (int) round($amount * $value / 100),
            'flat' => (int) round($value),
            default => throw new InvalidArgumentException("Unsupported Paystack commission type [{$type}]."),
        };

        return max(0, min($commission, $amount));
    }

    /**
     * Allocate the commission proportionally across vendors and build the
     * subaccount array used by Paystack.
     *
     * @param  Collection<int|string, int>  $vendorTotals
     * @param  Collection<int, Vendor>  $vendors
     * @return array{0: array<int, array{subaccount: string, share: int}>, 1: array<int, array<string, mixed>>}
     */
    private function buildSplitBreakdown(
        Collection $vendorTotals,
        Collection $vendors,
        int $commission,
        int $amount,
    ): array {
        $subaccounts = [];
        $breakdown = [];
        $allocatedCommission = 0;
        $vendorIds = $vendorTotals->keys()->values();
        $lastIndex = $vendorIds->count() - 1;

        foreach ($vendorIds as $index => $vendorId) {
            $vendorTotal = (int) $vendorTotals->get($vendorId);
            $vendor = $vendors->get($vendorId);

            if (! $vendor instanceof Vendor) {
                throw new PaystackException('The order references a vendor that no longer exists.');
            }

            if (! is_string($vendor->subaccount_code) || $vendor->subaccount_code === '') {
                throw ValidationException::withMessages([
                    'items' => "{$vendor->store_name} is not set up to receive split payments yet.",
                ]);
            }

            // Allocate using integer division for every vendor except the last,
            // which receives the remainder. This guarantees that the vendor
            // shares plus the platform commission equal the order total exactly.
            $vendorCommission = $index === $lastIndex
                ? $commission - $allocatedCommission
                : intdiv($vendorTotal * $commission, $amount);

            $allocatedCommission += $vendorCommission;
            $share = $vendorTotal - $vendorCommission;

            if ($share <= 0) {
                throw new PaystackException('A vendor share in this order is too small to split.');
            }

            $subaccounts[] = [
                'subaccount' => $vendor->subaccount_code,
                'share' => $share,
            ];

            $breakdown[] = [
                'vendor_id' => $vendor->getKey(),
                'store_name' => $vendor->store_name,
                'subaccount' => $vendor->subaccount_code,
                'line_total' => $vendorTotal,
                'commission' => $vendorCommission,
                'share' => $share,
            ];
        }

        return [$subaccounts, $breakdown];
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
