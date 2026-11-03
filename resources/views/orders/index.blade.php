@extends('layouts.storefront')

@section('title', 'My orders | Vefiri')
@section('description', 'Track and review the orders you have placed with Vefiri vendors.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">My orders</span>
            </nav>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">My orders</h1>
                    <p class="mt-1 text-xs text-muted">
                        {{ number_format($orders->total()) }} {{ $orders->total() === 1 ? 'order' : 'orders' }} placed with your account.
                    </p>
                </div>

                <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    Continue shopping
                    <x-ui.icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            @if ($orders->isEmpty())
                <div class="mt-4 rounded-2xl border border-black/[0.03] bg-white px-6 py-16 text-center shadow-card">
                    <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-surface text-muted">
                        <x-ui.icon name="orders" class="size-9" />
                    </span>
                    <h2 class="mt-4 text-lg font-bold text-ink">You have not placed an order yet</h2>
                    <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-muted">
                        When you complete a checkout, your order and delivery details will appear here.
                    </p>
                    <x-buttons.primary href="{{ route('search') }}" class="mt-5">
                        Start shopping
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </x-buttons.primary>
                </div>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($orders as $order)
                        <article class="overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                            <header class="flex flex-col gap-3 border-b border-line px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate font-mono text-xs font-bold text-ink">{{ $order->payment_reference }}</p>
                                        <x-orders.status-badge :status="$order->status" />
                                    </div>
                                    <p class="mt-1 text-2xs text-muted">
                                        Placed {{ $order->created_at?->format('M j, Y \a\t g:i A') }} · {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}
                                    </p>
                                </div>

                                <div class="sm:text-right">
                                    <p class="text-2xs text-muted">Order total</p>
                                    <p class="text-lg font-black text-brand-600">₦{{ number_format($order->total / 100) }}</p>
                                </div>
                            </header>

                            <div class="divide-y divide-line">
                                @foreach ($order->items->take(2) as $item)
                                    <div class="flex items-center gap-3 px-5 py-3">
                                        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-surface text-muted">
                                            <x-ui.icon name="package" class="size-5" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="line-clamp-1 text-xs font-semibold text-ink">{{ $item->product_name }}</p>
                                            <p class="mt-0.5 truncate text-2xs text-muted">
                                                {{ $item->vendor?->store_name ?? 'Vefiri vendor' }} · Qty {{ $item->quantity }} · ₦{{ number_format($item->unit_price / 100) }} each
                                            </p>
                                        </div>
                                        <span class="shrink-0 text-xs font-bold text-ink">₦{{ number_format($item->line_total / 100) }}</span>
                                    </div>
                                @endforeach

                                @if ($order->items->count() > 2)
                                    <p class="px-5 py-2 text-2xs font-medium text-muted">
                                        + {{ $order->items->count() - 2 }} more {{ $order->items->count() - 2 === 1 ? 'item' : 'items' }}
                                    </p>
                                @endif
                            </div>

                            <footer class="flex flex-col gap-2 border-t border-line bg-[#fafafa] px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <p class="flex items-center gap-1.5 text-2xs text-muted">
                                    <x-ui.icon name="truck" class="size-3.5" />
                                    Deliver to {{ $order->delivery_city }}, {{ $order->delivery_state }}
                                </p>

                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                                    View order details
                                    <x-ui.icon name="chevron-right" class="size-3.5" />
                                </a>
                            </footer>
                        </article>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $orders->links() }}
                </div>
            @endif
        </x-ui.container>
    </section>
@endsection
