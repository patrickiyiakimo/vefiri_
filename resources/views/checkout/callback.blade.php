@extends('layouts.storefront')

@section('title', 'Payment status | Vefiri')
@section('description', 'Check the status of your Vefiri order payment.')

@php
    $isPaid = $order?->isPaid() ?? false;
    $isPending = $order?->status === \App\Enums\OrderStatus::Pending;
    $isFailed = $order?->status === \App\Enums\OrderStatus::Failed;
@endphp

@section('content')
    <section class="py-10 sm:py-16">
        <x-ui.container>
            <div class="mx-auto max-w-2xl">
                <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-1.5 text-2xs text-muted">
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                    <x-ui.icon name="chevron-right" class="size-3" />
                    <span class="text-ink-soft">Payment status</span>
                </nav>

                <div class="mt-4 rounded-2xl border border-black/[0.04] bg-white p-8 text-center shadow-card sm:p-12">
                    @if ($isPaid)
                        <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-[#e7f8f2] text-choice-dark">
                            <x-ui.icon name="check" class="size-10" />
                        </span>
                        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">Payment confirmed</h1>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-muted">
                            Thank you for your order. We have notified your vendors and your items are being prepared for delivery.
                        </p>
                    @elseif ($isPending)
                        <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-[#fff7e6] text-[#b45309]">
                            <x-ui.icon name="clock" class="size-10" />
                        </span>
                        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">Payment is being confirmed</h1>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-muted">
                            We are waiting for Paystack to confirm your payment. This usually takes a few seconds, so refresh this page shortly.
                        </p>
                    @elseif ($isFailed)
                        <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-[#fef2f2] text-[#b91c1c]">
                            <x-ui.icon name="x" class="size-10" />
                        </span>
                        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">Payment was not completed</h1>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-muted">
                            Your payment did not go through. No money was taken; you can return to your cart and try again.
                        </p>
                    @else
                        <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-surface text-muted">
                            <x-ui.icon name="x" class="size-10" />
                        </span>
                        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">We could not find this payment</h1>
                        <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-muted">
                            The payment reference is missing or does not belong to your account. If you were charged, please contact support with your reference.
                        </p>
                    @endif

                    @if ($order !== null)
                        <dl class="mx-auto mt-6 grid max-w-md gap-2 rounded-xl border border-line bg-surface p-4 text-left text-xs">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Reference</dt>
                                <dd class="truncate font-mono text-[11px] font-semibold text-ink">{{ $order->payment_reference }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Items</dt>
                                <dd class="font-semibold text-ink">{{ $order->items_count }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Total</dt>
                                <dd class="font-bold text-brand-600">₦{{ number_format($order->total / 100) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Status</dt>
                                <dd class="font-semibold text-ink">{{ $order->status->label() }}</dd>
                            </div>
                        </dl>
                    @elseif ($reference !== '')
                        <p class="mx-auto mt-6 max-w-md rounded-xl border border-line bg-surface p-4 text-xs text-muted">
                            Reference: <span class="font-mono text-[11px] font-semibold text-ink">{{ $reference }}</span>
                        </p>
                    @endif

                    <div class="mt-6 flex flex-col justify-center gap-2 sm:flex-row">
                        @if ($isPending)
                            <x-buttons.primary :href="route('checkout.callback', ['reference' => $reference])">
                                <x-ui.icon name="rotate" class="size-4" />
                                Refresh status
                            </x-buttons.primary>
                        @elseif ($isPaid)
                            <x-buttons.primary href="{{ route('home') }}">
                                Continue shopping
                                <x-ui.icon name="arrow-right" class="size-4" />
                            </x-buttons.primary>
                        @else
                            <x-buttons.primary :href="route('cart.index')">
                                Back to cart
                                <x-ui.icon name="arrow-right" class="size-4" />
                            </x-buttons.primary>
                        @endif

                        <x-buttons.secondary :href="route('account.show')">View my account</x-buttons.secondary>
                    </div>

                    <p class="mt-5 flex items-center justify-center gap-1.5 text-[10px] text-muted">
                        <x-ui.icon name="shield" class="size-3" />
                        Need help? Contact support with your payment reference.
                    </p>
                </div>
            </div>
        </x-ui.container>
    </section>
@endsection
