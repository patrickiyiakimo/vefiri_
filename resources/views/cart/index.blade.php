@extends('layouts.storefront')

@section('title', 'Shopping cart | Vefiri')
@section('description', 'Review the items in your Vefiri shopping cart and update quantities before checkout.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Shopping cart</span>
            </nav>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Shopping cart</h1>
                    <p class="mt-1 text-xs text-muted">
                        {{ $cart['count'] }} {{ $cart['count'] === 1 ? 'item' : 'items' }} in your cart.
                    </p>
                </div>

                <x-buttons.secondary href="{{ route('home') }}" size="sm">Continue shopping</x-buttons.secondary>
            </div>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-semibold text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            @if (empty($cart['items']))
                <div class="mt-4 rounded-2xl border border-black/[0.04] bg-white p-8 text-center shadow-card sm:p-12">
                    <span class="mx-auto flex size-20 items-center justify-center rounded-full bg-surface text-muted">
                        <x-ui.icon name="cart" class="size-9" />
                    </span>
                    <p class="mt-4 text-base font-bold text-ink">Your cart is empty</p>
                    <p class="mt-1 text-xs leading-5 text-muted">Add something you love and it will show up here.</p>
                    <x-buttons.primary href="{{ route('home') }}" class="mt-5">
                        Start shopping
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </x-buttons.primary>
                </div>
            @else
                <div class="mt-4 grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <div class="overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                        <div class="divide-y divide-line">
                            @foreach ($cart['items'] as $item)
                                <article class="flex gap-3 p-4 sm:gap-4 sm:p-5">
                                    <a href="{{ route('products.show', $item['id']) }}" class="shrink-0">
                                        <img
                                            src="{{ $item['image'] }}"
                                            alt="{{ $item['title'] }}"
                                            onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                            width="160"
                                            height="160"
                                            class="size-20 rounded-xl bg-surface object-cover sm:size-24"
                                        >
                                    </a>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-2xs font-medium text-muted">{{ $item['store'] }}</p>
                                        <a href="{{ route('products.show', $item['id']) }}" class="mt-0.5 line-clamp-2 text-xs leading-5 font-semibold text-ink transition hover:text-brand-600">
                                            {{ $item['title'] }}
                                        </a>
                                        <p class="mt-1 text-2xs text-muted">₦{{ number_format($item['price']) }} each</p>

                                        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                                            <form method="POST" action="{{ route('cart.items.update', $item['id']) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')

                                                <label for="quantity-{{ $item['id'] }}" class="sr-only">Quantity</label>
                                                <input
                                                    id="quantity-{{ $item['id'] }}"
                                                    name="quantity"
                                                    type="number"
                                                    value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    max="99"
                                                    class="h-9 w-16 rounded-xl border border-line bg-white px-2 text-center text-xs font-semibold text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                                >
                                                <button type="submit" class="text-xs font-semibold text-muted transition hover:text-ink">Update</button>
                                            </form>

                                            <div class="flex items-center gap-3">
                                                <span class="text-sm font-bold text-brand-600">₦{{ number_format($item['line_total']) }}</span>
                                                <form method="POST" action="{{ route('cart.items.destroy', $item['id']) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold text-muted transition hover:text-brand-700">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <h2 class="text-sm font-bold text-ink">Order summary</h2>

                        <div class="mt-4 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Subtotal</span>
                                <span class="font-semibold text-ink">₦{{ number_format($cart['subtotal']) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted">Shipping</span>
                                <span class="font-semibold text-choice-dark">Calculated at checkout</span>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between border-t border-line pt-3">
                            <span class="text-xs font-semibold text-ink">Total</span>
                            <span class="text-xl font-black text-brand-600">₦{{ number_format($cart['subtotal']) }}</span>
                        </div>

                        <x-buttons.primary :href="route('checkout.show')" class="mt-4 w-full">
                            Checkout
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </x-buttons.primary>

                        <p class="mt-2 flex items-center justify-center gap-1 text-[10px] text-muted">
                            <x-ui.icon name="lock" class="size-3" />
                            Secure checkout · Buyer protection included
                        </p>
                    </aside>
                </div>
            @endif
        </x-ui.container>
    </section>
@endsection
