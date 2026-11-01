@props([
    'menuCategories' => [],
])

<div id="mobile-menu" data-drawer class="group fixed inset-0 z-[80] hidden">
    <div
        data-drawer-close
        class="absolute inset-0 bg-black/50 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-data-[open=true]:opacity-100"
    ></div>

    <aside
        role="dialog"
        aria-modal="true"
        aria-label="Mobile navigation"
        class="absolute top-0 left-0 flex h-full w-[88%] max-w-[360px] -translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out group-data-[open=true]:translate-x-0"
    >
        <header class="flex items-center justify-between border-b border-line px-4 py-3">
            <x-ui.logo />
            <button
                type="button"
                data-drawer-close
                aria-label="Close menu"
                class="flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink"
            >
                <x-ui.icon name="close" class="size-5" />
            </button>
        </header>

        <div class="flex-1 overflow-y-auto px-4 py-4">
            <div class="rounded-2xl bg-gradient-to-br from-[#fff2e8] to-[#ffd9bf] p-4">
                <p class="text-sm font-bold">Welcome to AliExpress</p>
                <p class="mt-1 text-2xs text-ink-soft">Sign in for member prices and exclusive coupons.</p>
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <x-buttons.primary href="{{ route('home') }}" size="sm" class="w-full">Sign in</x-buttons.primary>
                    <x-buttons.secondary href="{{ route('home') }}" size="sm" class="w-full">Register</x-buttons.secondary>
                </div>
            </div>

            <nav aria-label="Quick links" class="mt-4">
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl bg-surface px-3 py-3 text-xs font-semibold">
                        <x-ui.icon name="badge-check" class="size-4 text-choice" />
                        Choice
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl bg-surface px-3 py-3 text-xs font-semibold">
                        <x-ui.icon name="flame" class="size-4 text-brand-600" />
                        SuperDeals
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl bg-surface px-3 py-3 text-xs font-semibold">
                        <x-ui.icon name="gift" class="size-4 text-muted" />
                        Bundle deals
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-xl bg-surface px-3 py-3 text-xs font-semibold">
                        <x-ui.icon name="orders" class="size-4 text-muted" />
                        My orders
                    </a>
                </div>
            </nav>

            <nav aria-label="Categories" class="mt-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold">Shop by category</h2>
                    <a href="{{ route('home') }}" class="text-2xs font-semibold text-brand-600">View all</a>
                </div>

                <div class="mt-2 divide-y divide-line">
                    @foreach ($menuCategories as $category)
                        <a href="{{ route('home') }}" class="flex items-center gap-3 py-3">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-surface text-ink-soft">
                                <x-ui.icon :name="$category['icon']" class="size-4" />
                            </span>
                            <span class="min-w-0 flex-1 truncate text-xs font-medium">{{ $category['label'] }}</span>
                            <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                        </a>
                    @endforeach
                </div>
            </nav>

            <div class="mt-5 rounded-xl border border-line bg-[#fafafa] p-3">
                <a href="{{ route('home') }}" class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2">
                        <x-ui.icon name="location" class="size-4 text-muted" />
                        Ship to: United States
                    </span>
                    <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                </a>
                <a href="{{ route('home') }}" class="mt-3 flex items-center justify-between text-xs">
                    <span class="flex items-center gap-2">
                        <x-ui.icon name="globe" class="size-4 text-muted" />
                        English / USD
                    </span>
                    <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                </a>
            </div>
        </div>

        <footer class="border-t border-line px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 text-xs font-semibold text-ink-soft">
                <x-ui.icon name="help" class="size-4" />
                Help Center
            </a>
        </footer>
    </aside>
</div>
