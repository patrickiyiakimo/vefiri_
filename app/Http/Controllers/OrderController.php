<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ProvidesStorefrontData;

    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with('items.vendor:id,store_name')
            ->latest()
            ->paginate(10);

        return view('orders.index', [
            ...$this->sharedData(),
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load('items.vendor:id,store_name');

        return view('orders.show', [
            ...$this->sharedData(),
            'order' => $order,
        ]);
    }
}
