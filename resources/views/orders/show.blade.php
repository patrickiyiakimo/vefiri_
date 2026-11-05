@extends('layouts.storefront')

@section('title', 'Order '.$order->payment_reference.' | Vefiri')
@section('description', 'Review the items, payment status and delivery details for your Vefiri order.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('orders.index') }}" class="transition hover:text-ink">My orders</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="truncate font-mono text-ink-soft">{{ $order->payment_reference }}</span>
            </nav>

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

                <x-buttons.secondary href="{{ route('orders.index') }}" size="sm">
                    <x-ui.icon name="arrow-left" class="size-3.5" />
                    Back to orders
                </x-buttons.secondary>
            </div>

            @if ($order->status === \App\Enums\OrderStatus::Pending)
                <div class="mt-4 flex items-start gap-3 rounded-xl bg-[#fff7e6] px-4 py-3 text-xs text-[#8a5a00]">
                    <x-ui.icon name="clock" class="mt-0.5 size-4 shrink-0" />
                    <p>
                        We have not received confirmation of this payment yet. If you have just paid, refresh the
                        <a href="{{ route('checkout.callback', ['reference' => $order->payment_reference]) }}" class="font-bold underline underline-offset-2">payment status page</a>
                        in a moment.
                    </p>
                </div>
            @elseif ($order->status === \App\Enums\OrderStatus::Failed)
                <div class="mt-4 flex items-start gap-3 rounded-xl bg-brand-50 px-4 py-3 text-xs text-brand-700">
                    <x-ui.icon name="x" class="mt-0.5 size-4 shrink-0" />
                    <p>This payment was not completed. You can return to your cart to try the checkout again.</p>
                </div>
            @endif

            <div class="mt-4 grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Items in this order</h2>
                        <p class="mt-0.5 text-2xs text-muted">Prices shown are the amounts captured when you checked out.</p>
                    </header>

                    <div class="divide-y divide-line">
                        @foreach ($order->items as $item)
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
                        @endforeach
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
                                    <span class="font-semibold text-ink">{{ $order->paid_at->format('M j, Y') }}</span>
                                </div>
                            @endif
                            @if ($order->delivered_at)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-muted">Delivered on</span>
                                    <span class="font-semibold text-ink">{{ $order->delivered_at->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <h2 class="text-sm font-bold text-ink">Delivery details</h2>

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

                        @if ($order->delivery_notes)
                            <p class="mt-3 rounded-xl bg-surface px-3 py-2 text-2xs leading-5 text-ink-soft">
                                <span class="font-semibold text-ink">Delivery note:</span> {{ $order->delivery_notes }}
                            </p>
                        @endif
                    </section>

                    <div class="flex flex-wrap gap-2">
                        <x-buttons.primary href="{{ route('search') }}" size="sm">
                            Continue shopping
                            <x-ui.icon name="arrow-right" class="size-3.5" />
                        </x-buttons.primary>
                        <x-buttons.secondary href="{{ route('account.show') }}" size="sm">My account</x-buttons.secondary>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
@endsection
