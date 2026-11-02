@props([
    'searchCategories' => [],
    'searchSuggestions' => [],
    'searchValue' => '',
])

<header class="sticky top-0 z-40 border-b border-line bg-white/95 shadow-[0_1px_0_rgba(0,0,0,0.02)] backdrop-blur-md">
    <x-ui.container class="py-3">
        <div class="flex items-center gap-2 sm:gap-4">
            <button
                type="button"
                aria-label="Open menu"
                data-drawer-open="mobile-menu"
                class="flex size-9 shrink-0 items-center justify-center rounded-full text-ink transition hover:bg-surface md:hidden"
            >
                <x-ui.icon name="menu" class="size-5" />
            </button>

            <x-ui.logo class="shrink-0" />

            <div class="hidden min-w-0 flex-1 md:block">
                <x-inputs.search
                    id="desktop-search"
                    :categories="$searchCategories"
                    :suggestions="$searchSuggestions"
                    :value="$searchValue"
                    :selected-category="request('category', 'All categories')"
                    class="mx-auto max-w-[860px]"
                />
            </div>

            <div class="ml-auto flex shrink-0 items-center gap-0.5 sm:gap-1.5">
                <a
                    href="{{ route('search') }}"
                    class="hidden items-center gap-2 rounded-xl px-2.5 py-1.5 transition hover:bg-surface xl:flex"
                >
                    <x-ui.icon name="location" class="size-5" />
                    <span class="flex flex-col text-left">
                        <span class="text-2xs text-muted">Deliver to</span>
                        <span class="text-xs font-semibold">Nationwide</span>
                    </span>
                </a>

                @auth
                    <div class="relative hidden md:block" data-dropdown>
                        <button
                            type="button"
                            data-dropdown-trigger
                            aria-expanded="false"
                            class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-surface"
                        >
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                                <x-ui.icon name="user" class="size-4" />
                            </span>
                            <span class="hidden flex-col text-left lg:flex">
                                <span class="text-2xs text-muted">Hello,</span>
                                <span class="max-w-28 truncate text-xs font-semibold">{{ str(auth()->user()->name)->limit(16) }}</span>
                            </span>
                            <x-ui.icon name="chevron-down" class="hidden size-3.5 text-muted lg:block" />
                        </button>

                        <div
                            data-dropdown-panel
                            class="absolute top-[calc(100%+10px)] right-0 z-50 hidden w-64 overflow-hidden rounded-2xl border border-line bg-white p-4 shadow-float mega-menu-enter"
                        >
                            <p class="text-sm font-bold text-ink">Your Vefiri account</p>
                            <p class="mt-1 truncate text-xs text-muted">{{ auth()->user()->email }}</p>

                            <div class="mt-3 space-y-1 border-t border-line pt-3">
                                <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs text-ink-soft hover:bg-surface">
                                    <x-ui.icon name="orders" class="size-4" />
                                    My orders
                                </a>
                                <a href="{{ route('favourites.index') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs text-ink-soft hover:bg-surface">
                                    <x-ui.icon name="heart" class="size-4" />
                                    My wishlist
                                </a>
                                <a href="{{ route('vendors.apply') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs text-ink-soft hover:bg-surface">
                                    <x-ui.icon name="store" class="size-4" />
                                    Become a vendor
                                </a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-lg px-2 py-2 text-xs text-ink-soft hover:bg-surface">
                                        <x-ui.icon name="shield" class="size-4" />
                                        Admin dashboard
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="mt-3 border-t border-line pt-3">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50"
                                >
                                    <x-ui.icon name="arrow-right" class="size-4 rotate-180" />
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <button
                        type="button"
                        data-auth-open="login"
                        class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-surface"
                    >
                        <span class="flex size-8 items-center justify-center rounded-full bg-surface text-ink-soft">
                            <x-ui.icon name="user" class="size-4.5" />
                        </span>
                        <span class="hidden flex-col text-left lg:flex">
                            <span class="text-2xs text-muted">Welcome</span>
                            <span class="text-xs font-semibold">Sign in / Register</span>
                        </span>
                        <x-ui.icon name="chevron-down" class="hidden size-3.5 text-muted lg:block" />
                    </button>
                @endauth

                <a
                    href="{{ route('home') }}"
                    class="hidden items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-surface lg:flex"
                >
                    <x-ui.icon name="orders" class="size-5" />
                    <span class="flex flex-col text-left">
                        <span class="text-2xs text-muted">My</span>
                        <span class="text-xs font-semibold">Orders</span>
                    </span>
                </a>

                <button
                    type="button"
                    data-drawer-open="cart-drawer"
                    aria-label="Open shopping cart"
                    class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-surface"
                >
                    <span class="relative">
                        <x-ui.icon name="cart" class="size-5.5" />
                        <span
                            data-cart-count
                            class="absolute -top-1.5 -right-2 flex min-w-4 items-center justify-center rounded-full bg-brand-600 px-1 py-0.5 text-[9px] font-bold leading-none text-white ring-2 ring-white"
                        >{{ $cartCount ?? 0 }}</span>
                    </span>
                    <span class="hidden flex-col text-left sm:flex">
                        <span class="text-2xs text-muted">My</span>
                        <span class="text-xs font-semibold">Cart</span>
                    </span>
                </button>
            </div>
        </div>

        <div class="mt-2.5 md:hidden">
            <x-inputs.search
                id="mobile-search"
                :categories="$searchCategories"
                :suggestions="$searchSuggestions"
                :value="$searchValue"
                :selected-category="request('category', 'All categories')"
            />
        </div>
    </x-ui.container>
</header>
