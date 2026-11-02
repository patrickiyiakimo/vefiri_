@props([
    'slides' => [],
    'menuCategories' => [],
])

<section class="pt-3 sm:pt-4" aria-label="Featured promotions">
    <x-ui.container>
        <div class="grid gap-3 lg:grid-cols-[220px_minmax(0,1fr)] xl:grid-cols-[230px_minmax(0,1fr)_292px]">
            <aside class="hidden overflow-hidden rounded-2xl border border-black/[0.03] bg-white p-2 shadow-card lg:block">
                <p class="px-3 pt-2 pb-1 text-2xs font-bold tracking-[0.14em] text-muted uppercase">Shop by category</p>

                <nav aria-label="Product categories" class="mt-1">
                    @foreach ($menuCategories as $category)
                        <a
                            href="{{ route('home') }}"
                            class="group flex h-8 items-center gap-2 rounded-lg px-2.5 text-xs text-ink-soft transition hover:bg-brand-50 hover:text-brand-700"
                        >
                            <x-ui.icon :name="$category['icon']" class="size-4 shrink-0 text-muted transition group-hover:text-brand-500" />
                            <span class="min-w-0 flex-1 truncate">{{ $category['label'] }}</span>
                            <x-ui.icon name="chevron-right" class="size-3 shrink-0 text-[#b8b8b8] transition group-hover:text-brand-500" />
                        </a>
                    @endforeach
                </nav>

                <a
                    href="{{ route('home') }}"
                    class="mt-1 flex h-8 items-center gap-1 px-2.5 text-xs font-bold text-brand-600 transition hover:text-brand-700"
                >
                    See all categories
                    <x-ui.icon name="arrow-right" class="size-3" />
                </a>
            </aside>

            <div
                data-carousel
                class="relative h-[390px] overflow-hidden rounded-2xl bg-brand-600 text-white sm:h-[420px]"
            >
                <div data-carousel-track class="hero-track flex h-full">
                    @foreach ($slides as $slide)
                        <article
                            data-carousel-slide
                            class="relative flex h-full w-full shrink-0 items-center overflow-hidden bg-gradient-to-br px-5 py-6 sm:px-8 lg:px-10 {{ $slide['theme'] }}"
                        >
                            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                <div class="absolute -top-24 -right-20 size-72 rounded-full border-[36px] border-white/10"></div>
                                <div class="absolute -bottom-24 -left-16 size-64 rounded-full border-[28px] border-white/10"></div>
                                <div class="absolute top-8 right-8 text-[150px] leading-none font-black text-white/[0.07] sm:text-[210px]">11</div>
                            </div>

                            <div class="relative z-10 grid w-full items-center gap-5 lg:grid-cols-[minmax(0,1fr)_280px]">
                                <div class="max-w-[430px]">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-2xs font-bold tracking-[0.14em] uppercase ring-1 ring-white/25 backdrop-blur">
                                        <x-ui.icon name="sparkles" class="size-3" />
                                        {{ $slide['eyebrow'] }}
                                    </span>

                                    <h1 class="mt-3 text-[38px] leading-[0.95] font-black tracking-[-0.04em] sm:text-5xl lg:text-[56px]">
                                        {{ $slide['title'] }}
                                    </h1>

                                    <p class="mt-3 max-w-[360px] text-xs leading-5 text-white/90 sm:text-sm sm:leading-6">
                                        {{ $slide['description'] }}
                                    </p>

                                    <div class="mt-5 flex flex-wrap items-center gap-2 sm:mt-6">
                                        <x-buttons.secondary
                                            href="{{ route('home') }}"
                                            size="lg"
                                            class="border-white bg-white text-ink hover:bg-[#fff2e8] hover:text-ink"
                                        >
                                            {{ $slide['cta'] }}
                                            <x-ui.icon name="arrow-right" class="size-4" />
                                        </x-buttons.secondary>

                                        <span class="rounded-full px-3 py-2 text-2xs font-bold {{ $slide['accent'] }}">
                                            {{ $slide['code'] }}
                                        </span>
                                    </div>

                                    <div class="mt-4 hidden items-center gap-4 text-2xs font-medium text-white/80 sm:flex">
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-ui.icon name="truck" class="size-3.5" />
                                            Free shipping
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <x-ui.icon name="shield" class="size-3.5" />
                                            Buyer protection
                                        </span>
                                    </div>

                                    <div class="mt-5 flex gap-2 sm:hidden">
                                        @foreach ($slide['products'] as $product)
                                            <span class="w-14 overflow-hidden rounded-xl border border-white/40 bg-white p-0.5 shadow-md">
                                                <img
                                                    src="{{ asset($product['image']) }}"
                                                    alt=""
                                                    width="96"
                                                    height="96"
                                                    class="aspect-square w-full rounded-lg object-cover"
                                                >
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="relative hidden h-[290px] lg:block">
                                    @foreach ($slide['products'] as $product)
                                        <div
                                            @class([
                                                'absolute w-[148px] overflow-hidden rounded-2xl border border-white/50 bg-white p-1.5 shadow-[0_24px_60px_-18px_rgba(0,0,0,0.55)]',
                                                'top-3 left-0 z-20 -rotate-6' => $loop->index === 0,
                                                'top-0 right-0 z-10 rotate-3' => $loop->index === 1,
                                                'right-10 bottom-0 z-30 rotate-1' => $loop->index === 2,
                                            ])
                                        >
                                            <img
                                                src="{{ asset($product['image']) }}"
                                                alt="{{ $product['title'] }}"
                                                width="260"
                                                height="260"
                                                class="aspect-square w-full rounded-xl object-cover"
                                            >
                                            <div class="flex items-center justify-between gap-1 px-1 pt-1.5 pb-0.5">
                                                <span class="text-[11px] font-extrabold text-ink">₦{{ number_format($product['price']) }}</span>
                                                <x-badges.discount :percentage="$product['discount']" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="absolute inset-x-0 bottom-4 z-20 flex items-center justify-between px-4 sm:px-6">
                    <div class="flex items-center gap-1.5" role="tablist" aria-label="Hero slides">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                role="tab"
                                data-carousel-dot
                                data-active="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Go to slide {{ $loop->iteration }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                class="h-1.5 w-4 rounded-full bg-white/45 transition-all duration-300 data-[active=true]:w-7 data-[active=true]:bg-white"
                            ></button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-1.5">
                        <x-buttons.carousel direction="prev" tone="glass" data-carousel-prev />
                        <x-buttons.carousel direction="next" tone="glass" data-carousel-next />
                    </div>
                </div>
            </div>

            <aside class="hidden h-[420px] flex-col gap-2.5 xl:flex">
                <div class="rounded-2xl border border-black/[0.03] bg-white p-4 shadow-card">
                    <div class="flex items-center gap-2.5">
                        <span class="flex size-9 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                            <x-ui.icon name="user" class="size-4.5" />
                        </span>
                        <div>
                            @auth
                                <p class="text-xs font-bold">Hello, {{ str(auth()->user()->name)->limit(18) }}</p>
                                <p class="text-2xs text-muted">Your Vefiri account</p>
                            @else
                                <p class="text-xs font-bold">Welcome!</p>
                                <p class="text-2xs text-muted">Sign in for a better experience</p>
                            @endauth
                        </div>
                    </div>

                    @guest
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <x-buttons.primary type="button" data-auth-open="login" size="sm" class="w-full">Sign in</x-buttons.primary>
                            <x-buttons.secondary type="button" data-auth-open="register" size="sm" class="w-full">Register</x-buttons.secondary>
                        </div>
                    @else
                        <a href="{{ route('home') }}" class="mt-3 flex h-8 w-full items-center justify-center rounded-full border border-line bg-white text-xs font-semibold text-ink transition hover:border-ink">
                            My account
                        </a>
                    @endguest

                    <div class="mt-3 flex items-center justify-between text-2xs text-muted">
                        <span class="inline-flex items-center gap-1">
                            <x-ui.icon name="shield" class="size-3" />
                            Buyer protection
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-ui.icon name="rotate" class="size-3" />
                            Easy returns
                        </span>
                    </div>
                </div>

                <div class="relative flex-1 overflow-hidden rounded-2xl bg-gradient-to-br from-[#fff2e8] via-[#ffe3ce] to-[#ffc9a3] p-4">
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/70 px-2 py-0.5 text-[9px] font-bold tracking-[0.1em] text-brand-700 uppercase">
                        New user coupon
                    </span>
                    <div class="mt-2 flex items-end gap-2">
                        <span class="text-3xl leading-none font-black tracking-tight text-brand-700">₦5,000</span>
                        <span class="pb-1 text-xs font-bold text-brand-800">OFF</span>
                    </div>
                    <p class="mt-1 text-2xs text-ink-soft">On orders over ₦25,000</p>
                    <button
                        type="button"
                        data-toast="Coupon NEW5 added to your account."
                        class="absolute right-3 bottom-3 rounded-full bg-ink px-3 py-1.5 text-[10px] font-bold text-white transition hover:bg-brand-600"
                    >
                        Collect coupon NEW5
                    </button>
                </div>

                <div class="relative flex-1 overflow-hidden rounded-2xl bg-gradient-to-br from-[#e9fbf5] via-[#d2f7e9] to-[#a9eed8] p-4">
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/70 px-2 py-0.5 text-[9px] font-bold tracking-[0.1em] text-choice-dark uppercase">
                        Choice members
                    </span>
                    <p class="mt-2 text-xl leading-tight font-black text-[#005c45]">Free shipping<br>on every order</p>
                    <p class="mt-1 text-2xs text-[#337060]">No minimum spend required</p>
                    <a
                        href="{{ route('home') }}"
                        class="absolute right-3 bottom-3 inline-flex items-center gap-1 text-[10px] font-bold text-[#005c45] underline underline-offset-2"
                    >
                        Shop now
                        <x-ui.icon name="arrow-right" class="size-3" />
                    </a>
                </div>
            </aside>
        </div>
    </x-ui.container>
</section>
