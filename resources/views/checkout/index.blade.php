@extends('layouts.storefront')

@section('title', 'Secure checkout | Vefiri')
@section('description', 'Complete your Vefiri order securely with Paystack and pay every vendor in one checkout.')

@php
    $checkoutItems = collect($cart['items'])
        ->map(fn (array $item): array => [
            'product_id' => $item['id'],
            'quantity' => $item['quantity'],
        ])
        ->values()
        ->all();
@endphp

@section('content')
    <section class="pt-4 pb-10">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('cart.index') }}" class="transition hover:text-ink">Shopping cart</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Checkout</span>
            </nav>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Secure checkout</h1>
                    <p class="mt-1 text-xs text-muted">
                        Confirm your details and pay every vendor in a single transaction.
                    </p>
                </div>

                <div class="flex items-center gap-1.5 text-2xs font-semibold text-choice-dark">
                    <x-ui.icon name="shield" class="size-4" />
                    Protected by Vefiri buyer protection
                </div>
            </div>

            <form
                id="checkout-form"
                method="POST"
                action="{{ route('checkout.store') }}"
                data-checkout-form
                data-checkout-url="{{ route('checkout.store') }}"
                data-checkout-items="{{ json_encode($checkoutItems) }}"
                data-idempotency-key="{{ $idempotencyKey }}"
                class="mt-4 grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_380px]"
            >
                @csrf

                <div
                    data-checkout-errors
                    hidden
                    role="alert"
                    class="rounded-xl border border-[#fecaca] bg-[#fef2f2] px-4 py-3 text-xs text-[#991b1b] lg:col-span-2"
                >
                    <p class="font-semibold">We could not start your payment.</p>
                    <ul data-checkout-error-list class="mt-1 list-disc space-y-1 pl-4"></ul>
                </div>

                <div class="space-y-4">
                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <div class="flex items-center gap-3">
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-50 text-xs font-black text-brand-600">1</span>
                            <div>
                                <h2 class="text-sm font-bold text-ink">Contact details</h2>
                                <p class="text-2xs text-muted">We will use these details for your order updates.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="checkout-name" class="text-2xs font-semibold text-ink-soft">Full name</label>
                                <input
                                    id="checkout-name"
                                    name="customer_name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    value="{{ old('customer_name', auth()->user()->name) }}"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div>
                                <label for="checkout-email" class="text-2xs font-semibold text-ink-soft">Email address</label>
                                <input
                                    id="checkout-email"
                                    name="customer_email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    value="{{ old('customer_email', auth()->user()->email) }}"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div>
                                <label for="checkout-phone" class="text-2xs font-semibold text-ink-soft">Phone number <span class="font-normal text-muted">(optional)</span></label>
                                <input
                                    id="checkout-phone"
                                    name="customer_phone"
                                    type="tel"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    value="{{ old('customer_phone') }}"
                                    placeholder="080 1234 5678"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <div class="flex items-center gap-3">
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-50 text-xs font-black text-brand-600">2</span>
                            <div>
                                <h2 class="text-sm font-bold text-ink">Delivery address</h2>
                                <p class="text-2xs text-muted">Where should we deliver your order?</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="checkout-address" class="text-2xs font-semibold text-ink-soft">Street address</label>
                                <input
                                    id="checkout-address"
                                    name="delivery_address"
                                    type="text"
                                    required
                                    autocomplete="address-line1"
                                    value="{{ old('delivery_address') }}"
                                    placeholder="House number and street name"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div class="sm:col-span-2">
                                <label for="checkout-address-2" class="text-2xs font-semibold text-ink-soft">Apartment, suite or landmark <span class="font-normal text-muted">(optional)</span></label>
                                <input
                                    id="checkout-address-2"
                                    name="delivery_address_line_2"
                                    type="text"
                                    autocomplete="address-line2"
                                    value="{{ old('delivery_address_line_2') }}"
                                    placeholder="Apartment, floor, landmark or delivery instruction"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div>
                                <label for="checkout-city" class="text-2xs font-semibold text-ink-soft">City</label>
                                <input
                                    id="checkout-city"
                                    name="delivery_city"
                                    type="text"
                                    required
                                    autocomplete="address-level2"
                                    value="{{ old('delivery_city') }}"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div>
                                <label for="checkout-state" class="text-2xs font-semibold text-ink-soft">State</label>
                                <input
                                    id="checkout-state"
                                    name="delivery_state"
                                    type="text"
                                    required
                                    autocomplete="address-level1"
                                    value="{{ old('delivery_state') }}"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div>
                                <label for="checkout-postal-code" class="text-2xs font-semibold text-ink-soft">Postal code <span class="font-normal text-muted">(optional)</span></label>
                                <input
                                    id="checkout-postal-code"
                                    name="delivery_postal_code"
                                    type="text"
                                    autocomplete="postal-code"
                                    inputmode="numeric"
                                    value="{{ old('delivery_postal_code') }}"
                                    class="mt-1 h-10 w-full rounded-xl border border-line bg-white px-3 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                            </div>

                            <div class="sm:col-span-2">
                                <label for="checkout-notes" class="text-2xs font-semibold text-ink-soft">Delivery notes <span class="font-normal text-muted">(optional)</span></label>
                                <textarea
                                    id="checkout-notes"
                                    name="delivery_notes"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Anything the rider should know?"
                                    class="mt-1 w-full resize-y rounded-xl border border-line bg-white px-3 py-2 text-xs font-medium text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >{{ old('delivery_notes') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <div class="flex items-center gap-3">
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-50 text-xs font-black text-brand-600">3</span>
                            <div>
                                <h2 class="text-sm font-bold text-ink">Payment</h2>
                                <p class="text-2xs text-muted">Pay securely through Paystack. Your card details never touch Vefiri.</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-line bg-surface p-3">
                            <div class="flex items-center gap-3">
                                <span class="flex size-10 items-center justify-center rounded-full bg-white text-brand-600 shadow-sm">
                                    <x-ui.icon name="lock" class="size-5" />
                                </span>
                                <div>
                                    <p class="text-xs font-bold text-ink">Paystack</p>
                                    <p class="text-2xs text-muted">Cards, bank transfer and USSD</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-[#e7f8f2] px-2.5 py-1 text-[10px] font-bold text-choice-dark">Selected</span>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <x-ui.trust-item icon="shield" title="Encrypted payment" description="Every payment is processed on Paystack's secure checkout." />
                            <x-ui.trust-item icon="badge-check" title="Buyer protection" description="Your order is covered from payment to delivery." />
                        </div>
                    </section>
                </div>

                <aside class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card lg:sticky lg:top-28">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-ink">Order summary</h2>
                        <a href="{{ route('cart.index') }}" class="text-2xs font-semibold text-brand-600 transition hover:text-brand-700">Edit cart</a>
                    </div>

                    <div class="mt-4 max-h-72 divide-y divide-line overflow-y-auto pr-1">
                        @foreach ($cart['items'] as $item)
                            <div class="flex gap-3 py-3 first:pt-0 last:pb-0">
                                <img
                                    src="{{ $item['image'] }}"
                                    alt="{{ $item['title'] }}"
                                    width="96"
                                    height="96"
                                    class="size-14 shrink-0 rounded-lg bg-surface object-cover"
                                >
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-2xs leading-4 font-semibold text-ink">{{ $item['title'] }}</p>
                                    <p class="mt-1 truncate text-[10px] text-muted">{{ $item['store'] }} · Qty {{ $item['quantity'] }}</p>
                                    <p class="mt-1 text-xs font-bold text-ink">₦{{ number_format($item['line_total']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 space-y-2 border-t border-line pt-4 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Subtotal</span>
                            <span class="font-semibold text-ink">₦{{ number_format($cart['subtotal']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Delivery</span>
                            <span class="font-semibold text-choice-dark">Free</span>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-line pt-3">
                        <span class="text-xs font-semibold text-ink">Total</span>
                        <span class="text-xl font-black text-brand-600">₦{{ number_format($cart['subtotal']) }}</span>
                    </div>

                    <x-buttons.primary type="submit" data-checkout-submit class="mt-4 w-full">
                        <x-ui.icon name="lock" class="size-4" data-checkout-submit-icon />
                        <span data-checkout-submit-label>Pay ₦{{ number_format($cart['subtotal']) }}</span>
                    </x-buttons.primary>

                    <p class="mt-2 text-center text-[10px] leading-4 text-muted">
                        You will be redirected to Paystack to complete your payment. Your order is confirmed once payment succeeds.
                    </p>
                </aside>
            </form>
        </x-ui.container>
    </section>
@endsection
