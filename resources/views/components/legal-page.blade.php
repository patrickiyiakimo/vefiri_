@props([
    'title',
    'summary',
    'updated' => 'January 1, 2026',
])

<section class="pt-4 pb-2">
    <x-ui.container>
        <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
            <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
            <x-ui.icon name="chevron-right" class="size-3" />
            <span class="text-ink-soft">{{ $title }}</span>
        </nav>

        <div class="mt-3 rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card sm:p-8">
            <div class="flex flex-col gap-4 border-b border-line pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div class="max-w-2xl">
                    <span class="flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-ui.icon name="shield" class="size-5" />
                    </span>
                    <h1 class="mt-4 text-2xl font-black tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
                    <p class="mt-2 text-sm leading-6 text-muted">{{ $summary }}</p>
                    <p class="mt-3 text-2xs font-semibold text-muted">Last Updated: {{ $updated }}</p>
                </div>

                <nav aria-label="Policies" class="flex flex-wrap gap-1.5 sm:flex-col sm:items-end">
                    @foreach ([
                        ['label' => 'Privacy Policy', 'route' => 'pages.privacy'],
                        ['label' => 'Terms & Conditions', 'route' => 'pages.terms'],
                        ['label' => 'Returns & Refunds', 'route' => 'pages.returns'],
                    ] as $policy)
                        <a
                            href="{{ route($policy['route']) }}"
                            @class([
                                'rounded-full px-3 py-1.5 text-2xs font-semibold transition',
                                'bg-ink text-white' => request()->routeIs($policy['route']),
                                'bg-surface text-ink-soft hover:text-ink' => ! request()->routeIs($policy['route']),
                            ])
                        >
                            {{ $policy['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <article class="legal-prose mx-auto mt-6 max-w-3xl">
                {{ $slot }}
            </article>
        </div>
    </x-ui.container>
</section>
