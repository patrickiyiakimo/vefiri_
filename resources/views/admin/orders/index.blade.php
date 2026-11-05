@extends('layouts.storefront')

@section('title', 'Orders | Vefiri admin')
@section('description', 'Review every order placed on Vefiri, including customer delivery notes.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Orders</span>
            </nav>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-medium text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mt-4 rounded-xl bg-[#fff1f0] px-4 py-3 text-xs font-medium text-[#b42318]">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">All orders</h1>
                    <p class="mt-1 text-xs text-muted">
                        {{ number_format($orders->total()) }} {{ $orders->total() === 1 ? 'order' : 'orders' }} placed on Vefiri, with the delivery notes customers left at checkout.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    <x-ui.icon name="arrow-left" class="size-3.5" />
                    Back to dashboard
                </a>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1120px] text-left text-xs">
                        <thead class="border-b border-line bg-[#fafafa] text-2xs text-muted uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Order</th>
                                <th class="px-4 py-3 font-semibold">Customer</th>
                                <th class="px-4 py-3 font-semibold">Items</th>
                                <th class="px-4 py-3 font-semibold">Total</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Placed</th>
                                <th class="px-4 py-3 font-semibold">Delivery note</th>
                                <th class="px-4 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($orders as $order)
                                <tr class="hover:bg-[#fafafa]">
                                    <td class="px-4 py-3">
                                        <p class="font-mono text-[11px] font-bold text-ink">{{ $order->payment_reference }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">
                                            @if ($order->delivered_at)
                                                Delivered {{ $order->delivered_at->format('M j, Y') }}
                                            @elseif ($order->paid_at)
                                                Paid {{ $order->paid_at->format('M j, Y') }}
                                            @else
                                                Not paid yet
                                            @endif
                                        </p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-ink">{{ $order->customer_name }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ $order->customer_email }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-ink-soft">
                                        {{ number_format($order->items_count) }} {{ $order->items_count === 1 ? 'item' : 'items' }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-ink">₦{{ number_format($order->total / 100) }}</td>
                                    <td class="px-4 py-3">
                                        <x-orders.status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $order->created_at?->format('M j, Y') }}</td>
                                    <td class="px-4 py-3">
                                        @if (filled($order->delivery_notes))
                                            <p class="line-clamp-2 max-w-[220px] text-ink-soft" title="{{ $order->delivery_notes }}">{{ $order->delivery_notes }}</p>
                                        @else
                                            <span class="text-muted">No note left</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-lg border border-line px-2 py-1 text-[10px] font-semibold text-ink-soft transition hover:border-ink hover:text-ink">
                                            View
                                            <x-ui.icon name="chevron-right" class="size-3" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-muted">No orders have been placed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $orders->links() }}</div>
        </x-ui.container>
    </section>
@endsection
