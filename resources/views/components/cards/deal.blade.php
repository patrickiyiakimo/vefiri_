@props([
    'product',
    'progress' => 78,
])

<article {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-xl bg-white transition duration-300 hover:-translate-y-0.5 hover:shadow-card']) }}>
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

        <x-badges.discount :percentage="$product['discount']" class="absolute top-2 left-2" />

        <span class="absolute right-2 bottom-2 inline-flex items-center gap-1 rounded-full bg-black/60 px-2 py-1 text-2xs font-semibold text-white backdrop-blur">
            <x-ui.icon name="flame" class="size-2.5 text-[#ffd60a]" />
            Almost gone
        </span>
    </div>

    <div class="flex flex-1 flex-col p-3">
        <a
            href="{{ route('products.show', $product['id']) }}"
            class="line-clamp-2 min-h-8 text-xs leading-4 text-ink-soft transition hover:text-brand-600"
            title="{{ $product['title'] }}"
        >
            {{ $product['title'] }}
        </a>

        <x-ui.price :amount="$product['price']" :old="$product['old_price']" size="md" class="mt-2" />
        <x-ui.rating :rating="$product['rating']" class="mt-1.5" compact />
        <x-ui.discount-meter :percentage="$progress" class="mt-2.5" />

        <button
            type="button"
            data-add-to-cart
            data-product-id="{{ $product['id'] }}"
            data-product-title="{{ $product['title'] }}"
            data-product-price="{{ $product['price'] }}"
            data-product-image="{{ asset($product['image']) }}"
            data-product-store="{{ $product['store'] }}"
            class="mt-3 inline-flex h-8 w-full items-center justify-center gap-1.5 rounded-full bg-brand-50 text-2xs font-bold text-brand-700 transition hover:bg-brand-600 hover:text-white"
        >
            <x-ui.icon name="cart" class="size-3.5" />
            Add to cart
        </button>
    </div>
</article>
