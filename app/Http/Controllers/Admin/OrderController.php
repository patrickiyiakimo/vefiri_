<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    use ProvidesStorefrontData;

    public function index(): View
    {
        $orders = Order::query()
            ->withCount('items')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.orders.index', [
            ...$this->sharedData(),
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items.vendor:id,store_name');

        return view('admin.orders.show', [
            ...$this->sharedData(),
            'order' => $order,
        ]);
    }
}
