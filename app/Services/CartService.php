<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const MAX_QUANTITY = 99;

    public function __construct(private readonly ProductCatalog $catalog) {}

    /**
     * @return array{items: array<int, array<string, mixed>>, count: int, subtotal: int}
     */
    public function summary(User $user): array
    {
        $cart = $this->cart($user);

        if ($cart === null) {
            return ['items' => [], 'count' => 0, 'subtotal' => 0];
        }

        $items = $cart->items
            ->map(fn (CartItem $item): ?array => $this->presentItem($item))
            ->filter()
            ->values();

        return [
            'items' => $items->all(),
            'count' => (int) $items->sum('quantity'),
            'subtotal' => (int) $items->sum('line_total'),
        ];
    }

    public function add(User $user, string $productKey, int $quantity = 1): CartItem
    {
        $product = $this->productOrFail($productKey);

        return DB::transaction(function () use ($user, $productKey, $quantity, $product): CartItem {
            $cart = $this->cart($user, create: true);

            $item = $cart->items()
                ->where('product_key', $productKey)
                ->lockForUpdate()
                ->first();

            if ($item !== null) {
                $item->quantity = min(self::MAX_QUANTITY, $item->quantity + $quantity);
                $item->unit_price = $product['price'];
                $item->save();

                return $item;
            }

            return $cart->items()->create([
                'product_key' => $productKey,
                'quantity' => min(self::MAX_QUANTITY, $quantity),
                'unit_price' => $product['price'],
            ]);
        });
    }

    public function updateQuantity(User $user, string $productKey, int $quantity): CartItem
    {
        $this->productOrFail($productKey);

        return DB::transaction(function () use ($user, $productKey, $quantity): CartItem {
            $item = $this->findItem($user, $productKey);

            $item->quantity = min(self::MAX_QUANTITY, max(1, $quantity));
            $item->save();

            return $item;
        });
    }

    public function remove(User $user, string $productKey): void
    {
        $this->findItem($user, $productKey)->delete();
    }

    public function clear(User $user): void
    {
        $this->cart($user)?->items()->delete();
    }

    public function removePurchasedItems(Order $order): void
    {
        $productKeys = $order->items()->pluck('product_key')->all();

        if ($productKeys === [] || $order->user === null) {
            return;
        }

        $this->cart($order->user)?->items()->whereIn('product_key', $productKeys)->delete();
    }

    public function count(User $user): int
    {
        return (int) $this->cart($user)?->items()->sum('quantity');
    }

    private function cart(User $user, bool $create = false): ?Cart
    {
        $cart = $user->cart()->first();

        if ($cart === null && $create) {
            $cart = $user->cart()->create();
        }

        return $cart;
    }

    private function findItem(User $user, string $productKey): CartItem
    {
        $cart = $this->cart($user);

        $item = $cart?->items()->where('product_key', $productKey)->first();

        if ($item === null) {
            throw ValidationException::withMessages([
                'product_id' => 'This item is not in your cart.',
            ]);
        }

        return $item;
    }

    /**
     * @return array<string, mixed>
     */
    private function productOrFail(string $productKey): array
    {
        $product = $this->catalog->find($productKey);

        if ($product === null) {
            throw ValidationException::withMessages([
                'product_id' => 'The selected product is no longer available.',
            ]);
        }

        return $product;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function presentItem(CartItem $item): ?array
    {
        $product = $this->catalog->find($item->product_key);

        if ($product === null) {
            return null;
        }

        $unitPrice = $item->unit_price > 0 ? $item->unit_price : $product['price'];

        return [
            'id' => $item->product_key,
            'vendor_id' => $product['vendor_id'] ?? null,
            'title' => $product['title'],
            'image' => asset($product['image']),
            'store' => $product['store'],
            'price' => $unitPrice,
            'quantity' => $item->quantity,
            'line_total' => $unitPrice * $item->quantity,
        ];
    }
}
