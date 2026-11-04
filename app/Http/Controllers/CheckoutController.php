<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Requests\ProcessCheckoutRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PaystackSplitPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    use ProvidesStorefrontData;

    public function show(Request $request, CartService $cart): View|RedirectResponse
    {
        $summary = $cart->summary($request->user());

        if ($summary['items'] === []) {
            return redirect()
                ->route('cart.index')
                ->with('status', 'Your cart is empty. Add an item before checking out.');
        }

        return view('checkout.index', [
            ...$this->sharedData(),
            'cart' => $summary,
            'idempotencyKey' => (string) Str::uuid(),
        ]);
    }

    public function callback(Request $request): View
    {
        $reference = $request->string('reference')->toString();

        if ($reference === '') {
            $reference = $request->string('trxref')->toString();
        }

        $order = null;

        if ($reference !== '') {
            $order = Order::query()
                ->where('payment_reference', $reference)
                ->where('user_id', $request->user()->getKey())
                ->withCount('items')
                ->first();
        }

        return view('checkout.callback', [
            ...$this->sharedData(),
            'order' => $order,
            'reference' => $reference,
        ]);
    }

    public function processCheckout(ProcessCheckoutRequest $request, PaystackSplitPaymentService $payments): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $idempotencyKey = (string) $data['idempotency_key'];

        try {
            /*
             * Everything that mutates the order is wrapped in one transaction.
             * Two identical requests cannot both create an order because the
             * unique idempotency_key index serializes them at the database
             * level. The losing request catches the unique violation below and
             * returns the winner's existing checkout URL.
             */
            $order = DB::transaction(function () use ($data, $user, $idempotencyKey, $payments): Order {
                $existing = Order::query()
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();

                if ($existing !== null) {
                    $this->ensureOrderBelongsToUser($existing, $user->getKey());

                    return $existing;
                }

                $lineItems = $this->buildLineItems($data['items']);
                $total = (int) collect($lineItems)->sum('line_total');

                $order = Order::query()->create([
                    'user_id' => $user->getKey(),
                    'customer_name' => $data['customer_name'] ?? $user->name,
                    'customer_email' => $data['customer_email'] ?? $user->email,
                    'customer_phone' => $data['customer_phone'] ?? null,
                    'delivery_address' => $data['delivery_address'],
                    'delivery_address_line_2' => $data['delivery_address_line_2'] ?? null,
                    'delivery_city' => $data['delivery_city'],
                    'delivery_state' => $data['delivery_state'],
                    'delivery_postal_code' => $data['delivery_postal_code'] ?? null,
                    'delivery_notes' => $data['delivery_notes'] ?? null,
                    'total' => $total,
                    'payment_reference' => $this->generatePaymentReference(),
                    'status' => OrderStatus::Pending,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $order->items()->createMany($lineItems);

                $order->update([
                    'checkout_url' => $payments->initializeMultiSplitCheckout($order, $lineItems, $idempotencyKey),
                ]);

                return $order;
            });
        } catch (UniqueConstraintViolationException $exception) {
            // A concurrent request committed the same idempotency key first.
            $order = Order::query()->where('idempotency_key', $idempotencyKey)->first();

            if ($order === null) {
                throw $exception;
            }

            $this->ensureOrderBelongsToUser($order, $user->getKey());
        }

        if ($order->checkout_url === null) {
            throw ValidationException::withMessages([
                'checkout' => 'This checkout is still being prepared. Please try again.',
            ]);
        }

        return response()->json([
            'order_id' => $order->getKey(),
            'reference' => $order->payment_reference,
            'checkout_url' => $order->checkout_url,
        ]);
    }

    /**
     * Resolve the submitted cart payload against current vendor products and
     * snapshot the prices in kobo so later price changes cannot alter the order.
     *
     * @param  array<int, array{product_id: string, quantity: int|string}>  $items
     * @return array<int, array<string, mixed>>
     */
    private function buildLineItems(array $items): array
    {
        return collect($items)
            ->map(function (array $item): array {
                $product = $this->findVendorProduct($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Only {$product->stock} units of {$product->name} are available.",
                    ]);
                }

                $unitPrice = $product->price * 100;

                return [
                    'vendor_id' => $product->vendor_id,
                    'product_id' => $product->getKey(),
                    'product_key' => $product->catalogKey(),
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $unitPrice * $quantity,
                ];
            })
            ->values()
            ->all();
    }

    private function findVendorProduct(string $productKey): Product
    {
        $slug = Str::startsWith($productKey, 'vendor-')
            ? Str::after($productKey, 'vendor-')
            : $productKey;

        $product = Product::query()
            ->with('vendor')
            ->whereHas('vendor.user', fn (Builder $query) => $query->notSuspended())
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($product === null) {
            throw ValidationException::withMessages([
                'items' => "The product [{$productKey}] is not available for checkout.",
            ]);
        }

        if ($product->vendor === null || blank($product->vendor->subaccount_code)) {
            $storeName = $product->vendor?->store_name ?? 'This vendor';

            throw ValidationException::withMessages([
                'items' => "{$storeName} is not set up to receive split payments yet.",
            ]);
        }

        return $product;
    }

    private function ensureOrderBelongsToUser(Order $order, int|string|null $userId): void
    {
        if ((string) $order->user_id !== (string) $userId) {
            throw ValidationException::withMessages([
                'idempotency_key' => 'This idempotency key has already been used.',
            ]);
        }
    }

    private function generatePaymentReference(): string
    {
        return 'VEF_'.Str::ulid()->toBase32();
    }
}
