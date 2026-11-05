@extends('layouts.storefront')

@section('title', 'Order '.$order->payment_reference.' | Vefiri admin')
@section('description', 'Review the customer, items, payment status and delivery note for this Vefiri order.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.orders.index') }}" class="transition hover:text-ink">Orders</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="truncate font-mono text-ink-soft">{{ $order->payment_reference }}</span>
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
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="font-mono text-xl font-black tracking-tight text-ink sm:text-2xl">{{ $order->payment_reference }}</h1>
                        <x-orders.status-badge :status="$order->status" />
                    </div>
                    <p class="mt-1 text-xs text-muted">
                        Placed {{ $order->created_at?->format('M j, Y \a\t g:i A') }} · {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if ($order->status === \App\Enums\OrderStatus::Paid)
                        <form method="POST" action="{{ route('admin.orders.delivered', $order) }}" onsubmit="return confirm('Mark this order as delivered and email the customer?')">
                            @csrf
                            @method('PATCH')
                            <x-buttons.primary type="submit" size="sm">
                                <x-ui.icon name="check" class="size-3.5" />
                                Mark as delivered
                            </x-buttons.primary>
                        </form>
                    @endif

                    <x-buttons.secondary href="{{ route('admin.orders.index') }}" size="sm">
                        <x-ui.icon name="arrow-left" class="size-3.5" />
                        Back to orders
                    </x-buttons.secondary>
                </div>
            </div>

            <div class="mt-4 grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Items in this order</h2>
                        <p class="mt-0.5 text-2xs text-muted">Prices are the amounts captured when the customer checked out.</p>
                    </header>

                    <div class="divide-y divide-line">
                        @forelse ($order->items as $item)
                            <div class="flex gap-3 p-5">
                                <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-surface text-muted">
                                    <x-ui.icon name="package" class="size-6" />
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs leading-5 font-semibold text-ink">{{ $item->product_name }}</p>
                                    <p class="mt-0.5 truncate text-2xs text-muted">{{ $item->vendor?->store_name ?? 'Vefiri vendor' }}</p>
                                    <p class="mt-1 text-2xs text-muted">
                                        ₦{{ number_format($item->unit_price / 100) }} × {{ $item->quantity }}
                                    </p>
                                </div>

                                <p class="shrink-0 text-sm font-bold text-ink">₦{{ number_format($item->line_total / 100) }}</p>
                            </div>
                        @empty
                            <p class="px-5 py-10 text-center text-xs text-muted">This order has no items recorded.</p>
                        @endforelse
                    </div>
                </section>

                <div class="space-y-4">
                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <h2 class="text-sm font-bold text-ink">Payment summary</h2>

                        <div class="mt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Items</span>
                                <span class="font-semibold text-ink">₦{{ number_format($order->total / 100) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Delivery</span>
                                <span class="font-semibold text-choice-dark">Free</span>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between border-t border-line pt-3">
                            <span class="text-xs font-semibold text-ink">Total</span>
                            <span class="text-xl font-black text-brand-600">₦{{ number_format($order->total / 100) }}</span>
                        </div>

                        <div class="mt-4 space-y-2 rounded-xl bg-surface p-3 text-2xs">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-muted">Status</span>
                                <x-orders.status-badge :status="$order->status" />
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-muted">Reference</span>
                                <span class="truncate font-mono text-[10px] font-semibold text-ink">{{ $order->payment_reference }}</span>
                            </div>
                            @if ($order->paid_at)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-muted">Paid on</span>
                                    <span class="font-semibold text-ink">{{ $order->paid_at->format('M j, Y g:i A') }}</span>
                                </div>
                            @endif
                            @if ($order->delivered_at)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-muted">Delivered on</span>
                                    <span class="font-semibold text-ink">{{ $order->delivered_at->format('M j, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <h2 class="text-sm font-bold text-ink">Customer and delivery</h2>

                        <div class="mt-3 flex items-start gap-3">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                                <x-ui.icon name="location" class="size-4" />
                            </span>
                            <div class="min-w-0 text-xs leading-5">
                                <p class="font-semibold text-ink">{{ $order->customer_name }}</p>
                                <p class="text-muted">{{ $order->delivery_address }}@if ($order->delivery_address_line_2), {{ $order->delivery_address_line_2 }}@endif</p>
                                <p class="text-muted">
                                    {{ $order->delivery_city }}, {{ $order->delivery_state }}@if ($order->delivery_postal_code) {{ $order->delivery_postal_code }}@endif
                                </p>
                                @if ($order->customer_phone)
                                    <p class="mt-1 text-muted">{{ $order->customer_phone }}</p>
                                @endif
                                <p class="text-muted">{{ $order->customer_email }}</p>
                            </div>
                        </div>

                        @if (filled($order->delivery_notes))
                            <div class="mt-4 rounded-xl border border-[#fed7aa] bg-[#fff7ed] p-3">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-[#9a3412]">
                                    Delivery note from checkout
                                </p>
                                <p class="mt-1.5 text-2xs leading-5 text-[#7c2d12]">{{ $order->delivery_notes }}</p>
                            </div>
                        @else
                            <p class="mt-4 rounded-xl bg-surface px-3 py-2 text-2xs text-muted">The customer did not leave a delivery note.</p>
                        @endif
                    </section>
                </div>
            </div>
        </x-ui.container>
    </section>
@endsection
