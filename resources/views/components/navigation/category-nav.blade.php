@props([
    'navLinks' => [],
    'menuCategories' => [],
])

<nav aria-label="Main navigation" class="border-b border-line bg-white">
    <x-ui.container class="flex h-11 items-center gap-1">
        <div class="relative shrink-0" data-dropdown>
            <button
                type="button"
                data-dropdown-trigger
                aria-expanded="false"
                class="flex h-8 items-center gap-1.5 rounded-full px-2.5 text-xs font-bold text-ink transition hover:bg-surface"
            >
                <x-ui.icon name="grid" class="size-4" />
                <span class="hidden sm:inline">All categories</span>
                <span class="sm:hidden">All</span>
                <x-ui.icon name="chevron-down" class="size-3.5 text-muted" />
            </button>

            <div
                data-dropdown-panel
                class="absolute top-[calc(100%+10px)] left-0 z-50 hidden w-[calc(100vw-24px)] max-w-[980px] overflow-hidden rounded-2xl border border-line bg-white p-5 shadow-float mega-menu-enter"
            >
                <div class="grid grid-cols-2 gap-x-6 gap-y-5 md:grid-cols-3 xl:grid-cols-5">
                    @foreach ($menuCategories as $category)
                        <section>
                            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-bold text-ink transition hover:text-brand-600">
                                <span class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                                    <x-ui.icon :name="$category['icon']" class="size-3.5" />
                                </span>
                                {{ $category['label'] }}
                            </a>
                            <ul class="mt-2 space-y-1.5 pl-9">
                                @foreach ($category['children'] as $child)
                                    <li>
                                        <a href="{{ route('home') }}" class="text-2xs text-muted transition hover:text-brand-600">
                                            {{ $child }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>

                <div class="mt-5 flex items-center justify-between border-t border-line pt-4">
                    <p class="text-2xs text-muted">Millions of products from trusted sellers worldwide.</p>
                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-1 text-xs font-semibold text-brand-600">
                        View all categories
                        <x-ui.icon name="arrow-right" class="size-3.5 transition group-hover:translate-x-0.5" />
                    </a>
                </div>
            </div>
        </div>

        <div class="no-scrollbar flex min-w-0 flex-1 items-center gap-0.5 overflow-x-auto">
            @foreach ($navLinks as $link)
                <a
                    href="{{ route('home') }}"
                    @class([
                        'flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1.5 text-xs font-medium transition hover:bg-surface',
                        'font-semibold text-choice-dark' => $link['accent'] === 'choice',
                        'font-semibold text-brand-600' => $link['accent'] === 'sale',
                        'text-ink-soft' => $link['accent'] === null,
                    ])
                >
                    <x-ui.icon
                        :name="$link['icon']"
                        @class([
                            'size-3.5',
                            'text-choice' => $link['accent'] === 'choice',
                            'text-brand-500' => $link['accent'] === 'sale',
                            'text-muted' => $link['accent'] === null,
                        ])
                    />
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <a
            href="{{ route('register', ['type' => 'vendor']) }}"
            class="ml-2 hidden shrink-0 items-center gap-1.5 border-l border-line pl-4 text-xs font-semibold text-ink-soft transition hover:text-brand-600 xl:flex"
        >
            <x-ui.icon name="badge-check" class="size-4 text-brand-600" />
            Become a verified vendor
        </a>
    </x-ui.container>
</nav>
