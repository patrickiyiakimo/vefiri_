@props([
    'item',
])

<article data-cart-item data-cart-item-id="{{ $item['id'] }}" class="flex gap-3 rounded-2xl border border-line p-3">
    <img
        data-cart-item-image
        src="{{ asset($item['image']) }}"
        alt=""
        onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
        width="160"
        height="160"
        class="size-20 shrink-0 rounded-xl bg-surface object-cover"
    >

    <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
            <p data-cart-item-store class="truncate text-2xs font-medium text-muted">{{ $item['store'] }}</p>
            <button
                type="button"
                data-cart-item-remove
                aria-label="Remove item"
                class="flex size-6 shrink-0 items-center justify-center rounded-full text-muted transition hover:bg-brand-50 hover:text-brand-600"
            >
                <x-ui.icon name="trash" class="size-3.5" />
            </button>
        </div>

        <p data-cart-item-title class="mt-0.5 line-clamp-2 text-xs leading-4 font-medium text-ink">
            {{ $item['title'] }}
        </p>

        <div class="mt-2 flex items-end justify-between gap-2">
            <div class="inline-flex h-7 items-center rounded-full border border-line bg-white">
                <button
                    type="button"
                    data-cart-item-decrease
                    aria-label="Decrease quantity"
                    class="flex size-7 items-center justify-center rounded-full text-muted transition hover:text-brand-600"
                >
                    <x-ui.icon name="minus" class="size-3" />
                </button>
                <span data-cart-item-qty class="min-w-5 text-center text-2xs font-bold tabular-nums">{{ $item['quantity'] }}</span>
                <button
                    type="button"
                    data-cart-item-increase
                    aria-label="Increase quantity"
                    class="flex size-7 items-center justify-center rounded-full text-muted transition hover:text-brand-600"
                >
                    <x-ui.icon name="plus" class="size-3" />
                </button>
            </div>

            <span data-cart-item-price class="text-sm font-bold text-brand-600">
                ₦{{ number_format($item['price'] * $item['quantity']) }}
            </span>
        </div>
    </div>
</article>
