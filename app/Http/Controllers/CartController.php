<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(Request $request): CartResource
    {
        return new CartResource($this->cart->summary($request->user()));
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

    public function update(UpdateCartItemRequest $request, string $product): CartResource
    {
        $this->cart->updateQuantity(
            $request->user(),
            $product,
            (int) $request->validated('quantity'),
        );

        return new CartResource($this->cart->summary($request->user()));
    }

    public function destroy(Request $request, string $product): CartResource
    {
        $this->cart->remove($request->user(), $product);

        return new CartResource($this->cart->summary($request->user()));
    }
}
