<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ProvidesStorefrontData;

    public function __construct(private readonly CartService $cart) {}

    public function index(Request $request): CartResource|View
    {
        $summary = $this->cart->summary($request->user());

        if ($request->expectsJson()) {
            return new CartResource($summary);
        }

        return view('cart.index', [
            ...$this->sharedData(),
            'cart' => $summary,
        ]);
    }

    public function store(StoreCartItemRequest $request): CartResource
    {
        $data = $request->validated();

        $this->cart->add(
            $request->user(),
            $data['product_id'],
            (int) ($data['quantity'] ?? 1),
        );

        return new CartResource($this->cart->summary($request->user()));
    }

    public function update(UpdateCartItemRequest $request, string $product): CartResource|RedirectResponse
    {
        $this->cart->updateQuantity(
            $request->user(),
            $product,
            (int) $request->validated('quantity'),
        );

        if ($request->expectsJson()) {
            return new CartResource($this->cart->summary($request->user()));
        }

        return redirect()->route('cart.index')->with('status', 'Your cart has been updated.');
    }

    public function destroy(Request $request, string $product): CartResource|RedirectResponse
    {
        $this->cart->remove($request->user(), $product);

        if ($request->expectsJson()) {
            return new CartResource($this->cart->summary($request->user()));
        }

        return redirect()->route('cart.index')->with('status', 'Item removed from your cart.');
    }
}
