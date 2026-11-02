@props([
    'product',
])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-xl border border-black/[0.04] bg-white transition duration-300 hover:-translate-y-0.5 hover:shadow-card']) }}>
    <div class="relative aspect-square overflow-hidden bg-[#f6f6f6]">
        <a href="{{ route('products.show', $product['id']) }}" class="block h-full w-full" aria-label="{{ $product['title'] }}">
            <img
                src="{{ asset($product['image']) }}"
                alt="{{ $product['title'] }}"
                width="480"
                height="480"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
            >
        </a>

        <div class="absolute top-2 left-2 flex flex-col items-start gap-1.5">
            <x-badges.discount :percentage="$product['discount']" />
            @if ($product['badge'])
                <x-badges.label tone="yellow">{{ $product['badge'] }}</x-badges.label>
            @endif
        </div>

        <button
            type="button"
            aria-label="Add {{ $product['title'] }} to wishlist"
            aria-pressed="false"
            data-wishlist
            data-product-id="{{ $product['id'] }}"
            class="absolute top-2 right-2 flex size-8 items-center justify-center rounded-full bg-white/90 text-ink-soft shadow-sm backdrop-blur transition hover:scale-105 hover:text-brand-600"
        >
            <x-ui.icon name="heart" data-wishlist-icon class="size-4" />
        </button>

        <button
            type="button"
            aria-label="Add {{ $product['title'] }} to cart"
            data-add-to-cart
            data-product-id="{{ $product['id'] }}"
            data-product-title="{{ $product['title'] }}"
            data-product-price="{{ $product['price'] }}"
            data-product-image="{{ asset($product['image']) }}"
            data-product-store="{{ $product['store'] }}"
            class="absolute right-2 bottom-2 flex h-9 items-center justify-center gap-1.5 rounded-full bg-ink/90 px-3 text-2xs font-semibold text-white shadow-lg backdrop-blur transition hover:bg-brand-600 md:translate-y-2 md:opacity-0 md:group-hover:translate-y-0 md:group-hover:opacity-100"
        >
            <x-ui.icon name="cart" class="size-3.5" />
            Add to cart
        </button>
    </div>

    <div class="flex flex-1 flex-col p-2.5">
        <a
            href="{{ route('products.show', $product['id']) }}"
            class="line-clamp-2 min-h-8 text-xs leading-4 text-ink-soft transition hover:text-brand-600"
            title="{{ $product['title'] }}"
        >
            {{ $product['title'] }}
        </a>

        <x-ui.price :amount="$product['price']" :old="$product['old_price']" size="sm" class="mt-1.5" />

        <x-ui.rating :rating="$product['rating']" :sold="$product['sold']" class="mt-1.5" />

        <div class="mt-auto flex flex-wrap items-center gap-1.5 pt-2">
            @if ($product['choice'])
                <x-badges.choice />
            @endif

            @if ($product['free_shipping'])
                <span class="inline-flex items-center gap-1 text-2xs font-medium text-choice-dark">
                    <x-ui.icon name="truck" class="size-3" />
                    Free shipping
                </span>
            @endif
        </div>
    </div>
</article>
