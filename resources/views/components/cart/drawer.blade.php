<div id="cart-drawer" data-drawer class="group fixed inset-0 z-[80] hidden">
    <div
        data-drawer-close
        class="absolute inset-0 bg-black/50 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-data-[open=true]:opacity-100"
    ></div>

    <aside
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-drawer-title"
        class="absolute top-0 right-0 flex h-full w-full max-w-[430px] translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out group-data-[open=true]:translate-x-0"
    >
        <header class="flex items-center justify-between border-b border-line px-5 py-4">
            <div class="flex items-center gap-2">
                <h2 id="cart-drawer-title" class="text-lg font-bold">Shopping cart</h2>
                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-2xs font-bold text-brand-700">
                    <span data-cart-count>0</span> items
                </span>
            </div>

            <button
                type="button"
                data-drawer-close
                aria-label="Close shopping cart"
                class="flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink"
            >
                <x-ui.icon name="close" class="size-5" />
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-5 py-4">
            <div data-cart-empty class="flex h-full flex-col items-center justify-center text-center">
                <span class="flex size-20 items-center justify-center rounded-full bg-surface text-muted">
                    <x-ui.icon name="cart" class="size-9" />
                </span>
                <p class="mt-4 text-base font-bold text-ink">Your cart is empty</p>
                <p class="mt-1 max-w-[240px] text-xs leading-5 text-muted">
                    Add something you love and it will show up here.
                </p>
                <x-buttons.primary href="{{ route('home') }}" data-drawer-close class="mt-5">
                    Start shopping
                </x-buttons.primary>
            </div>

            <div data-cart-items class="hidden space-y-3"></div>
        </div>

        <footer data-cart-footer class="hidden border-t border-line bg-[#fafafa] px-5 py-4">
            <div class="flex items-center justify-between text-xs">
                <span class="text-muted">Subtotal</span>
                <span data-cart-subtotal class="text-xl font-black text-brand-600">₦0</span>
            </div>
            <p class="mt-1 flex items-center gap-1.5 text-2xs text-choice-dark">
                <x-ui.icon name="truck" class="size-3.5" />
                Free shipping on eligible Choice items
            </p>

            <x-buttons.primary type="button" data-cart-checkout class="mt-3 w-full">
                Checkout
                <x-ui.icon name="arrow-right" class="size-4" />
            </x-buttons.primary>

            <p class="mt-2 flex items-center justify-center gap-1 text-[10px] text-muted">
                <x-ui.icon name="lock" class="size-3" />
                Secure checkout · Buyer protection included
            </p>
        </footer>
    </aside>

    <template data-cart-item-template>
        <x-cart.item :item="[
            'id' => '',
            'title' => '',
            'image' => 'images/products/headphones.avif',
            'price' => 0,
            'quantity' => 1,
            'store' => '',
        ]" />
    </template>
</div>
