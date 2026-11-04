@extends('layouts.storefront')

@section('title', $product['title'].' | Vefiri')
@section('description', 'Buy '.$product['title'].' from '.$product['store'].' on Vefiri with nationwide delivery, buyer protection and easy returns.')

@section('content')
    <section class="pt-4">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 overflow-hidden text-2xs text-muted">
                <a href="{{ route('home') }}" class="shrink-0 transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3 shrink-0" />
                <a href="{{ route('search', ['category' => $category]) }}" class="shrink-0 transition hover:text-ink">{{ $category }}</a>
                <x-ui.icon name="chevron-right" class="size-3 shrink-0" />
                <span class="truncate text-ink-soft">{{ $product['title'] }}</span>
            </nav>

            <div class="mt-3 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
                <div class="rounded-2xl border border-black/[0.04] bg-white p-3 shadow-card sm:p-5">
                    <div class="relative aspect-square overflow-hidden rounded-xl bg-[#f6f6f6]">
                        <img
                            src="{{ asset($product['image']) }}"
                            alt="{{ $product['title'] }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                            width="720"
                            height="720"
                            class="h-full w-full object-cover"
                        >
                        @if ($product['discount'] > 0)
                            <x-badges.discount :percentage="$product['discount']" class="absolute top-3 left-3" />
                        @endif
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach ([
                            ['icon' => 'shield', 'title' => 'Buyer protection', 'copy' => 'Money back guarantee'],
                            ['icon' => 'truck', 'title' => 'Nationwide delivery', 'copy' => 'Tracked shipping'],
                            ['icon' => 'rotate', 'title' => 'Easy returns', 'copy' => 'Within 7-15 days'],
                        ] as $benefit)
                            <div class="rounded-xl bg-surface p-3">
                                <x-ui.icon :name="$benefit['icon']" class="size-4 text-choice-dark" />
                                <p class="mt-2 text-2xs font-bold text-ink">{{ $benefit['title'] }}</p>
                                <p class="mt-0.5 text-[10px] leading-4 text-muted">{{ $benefit['copy'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($product['badge'])
                            <x-badges.label tone="yellow">{{ $product['badge'] }}</x-badges.label>
                        @endif
                        @if ($product['choice'])
                            <x-badges.choice />
                        @endif
                        @if ($product['free_shipping'])
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f8f2] px-2 py-1 text-2xs font-bold text-choice-dark">
                                <x-ui.icon name="truck" class="size-3" />
                                Free shipping
                            </span>
                        @endif
                    </div>

                    <h1 class="mt-3 text-xl leading-7 font-black tracking-tight text-ink sm:text-2xl sm:leading-8">{{ $product['title'] }}</h1>

                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        @if ($product['rating'] > 0)
                            <x-ui.rating :rating="$product['rating']" :sold="$product['sold']" />
                        @else
                            <span class="text-2xs font-semibold text-choice-dark">New arrival</span>
                        @endif
                        @if ($product['discount'] > 0)
                            <span class="text-2xs text-muted">{{ $product['discount'] }}% off today</span>
                        @endif
                    </div>

                    <div class="mt-5 rounded-2xl bg-gradient-to-r from-[#fff1e8] via-[#fff8f3] to-white p-4">
                        <x-ui.price :amount="$product['price']" :old="$product['old_price']" size="lg" />
                        <p class="mt-1 text-2xs text-choice-dark">
                            Inclusive of all taxes.@if ($product['discount'] > 0) Save {{ $product['discount'] }}% today.@endif
                        </p>
                    </div>

                    <div data-product-purchase class="mt-5">
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="quantity" class="text-xs font-semibold text-ink">Quantity</label>
                            <select
                                id="quantity"
                                name="quantity"
                                data-quantity-select
                                class="h-10 rounded-xl border border-line bg-white px-3 text-xs font-semibold text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                            >
                                @for ($quantity = 1; $quantity <= 10; $quantity++)
                                    <option value="{{ $quantity }}">{{ $quantity }}</option>
                                @endfor
                            </select>
                            @if ($product['sold'])
                                <span class="text-2xs text-muted">{{ $product['sold'] }} sold</span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-buttons.primary
                                type="button"
                                size="lg"
                                data-add-to-cart
                                data-product-id="{{ $product['id'] }}"
                                class="flex-1"
                            >
                                <x-ui.icon name="cart" class="size-4" />
                                Add to cart
                            </x-buttons.primary>

                            <button
                                type="button"
                                aria-label="Add {{ $product['title'] }} to wishlist"
                                aria-pressed="false"
                                data-wishlist
                                data-product-id="{{ $product['id'] }}"
                                class="flex size-12 shrink-0 items-center justify-center rounded-full border border-line bg-white text-ink-soft transition hover:border-brand-300 hover:text-brand-600"
                            >
                                <x-ui.icon name="heart" class="size-5" />
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 space-y-2 border-t border-line pt-4 text-xs text-ink-soft">
                        <p class="flex items-center justify-between gap-3">
                            <span class="text-muted">Sold by</span>
                            <span class="flex items-center gap-1.5 font-semibold text-ink">
                                {{ $product['store'] }}
                                @if (! empty($product['is_vendor_product']))
                                    <x-ui.icon name="badge-check" class="size-3.5 text-choice" />
                                    <span class="text-2xs font-semibold text-choice-dark">Verified vendor</span>
                                @endif
                            </span>
                        </p>
                        <p class="flex items-center justify-between gap-3">
                            <span class="text-muted">Category</span>
                            <a href="{{ route('search', ['category' => $category]) }}" class="font-semibold text-brand-600 transition hover:text-brand-700">{{ $category }}</a>
                        </p>
                        <p class="flex items-center justify-between gap-3">
                            <span class="text-muted">Delivery</span>
                            <span class="font-semibold text-ink">Nationwide, 2-7 business days</span>
                        </p>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    @if (! empty($product['description']))
        <section class="pt-4" aria-labelledby="product-description-heading">
            <x-ui.container>
                <div class="rounded-2xl border border-black/[0.03] bg-white px-5 py-4 shadow-card sm:px-7 sm:py-5">
                    <h2 id="product-description-heading" class="text-xl font-black tracking-tight text-ink">Product details</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-7 whitespace-pre-line text-ink-soft">{{ $product['description'] }}</p>
                </div>
            </x-ui.container>
        </section>
    @endif

    <section class="pt-5 pb-2" aria-labelledby="related-products-heading">
        <x-ui.container>
            <div class="rounded-2xl border border-black/[0.03] bg-white px-3 py-4 shadow-card sm:px-5 sm:py-5" data-load-more-scope>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="related-products-heading" class="text-xl font-black tracking-tight text-ink">More products for sale</h2>
                        <p class="mt-1 text-xs text-muted">Explore more deals from {{ $category }} and other popular categories.</p>
                    </div>
                    <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                        Browse all products
                        <x-ui.icon name="arrow-right" class="size-3.5" />
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-6">
                    @foreach ($relatedProducts as $related)
                        <x-cards.product
                            :product="$related"
                            data-more-item
                            :class="$loop->index >= 12 ? 'hidden' : null"
                        />
                    @endforeach
                </div>

                @if (count($relatedProducts) > 12)
                    <div data-load-more-controls class="mt-5 flex justify-center">
                        <x-buttons.secondary type="button" data-load-more class="px-7">
                            Show more products
                            <x-ui.icon name="chevron-down" class="size-4" />
                        </x-buttons.secondary>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
@endsection
