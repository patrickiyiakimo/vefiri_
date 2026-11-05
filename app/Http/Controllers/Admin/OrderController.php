<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ProvidesStorefrontData;
use App\Http\Controllers\Controller;
use App\Mail\DeliveryConfirmationMail;
use App\Models\Order;
use App\Services\OrderDeliveryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

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

    public function markDelivered(Order $order, OrderDeliveryService $deliveries): RedirectResponse
    {
        if (! $deliveries->markDelivered($order)) {
            return back()->with('error', 'Only paid orders can be marked as delivered.');
        }

        Mail::to($order->customer_email)->queue(new DeliveryConfirmationMail($order));

        return back()->with('status', "Order {$order->payment_reference} has been marked as delivered and the customer has been notified.");
    }
}
