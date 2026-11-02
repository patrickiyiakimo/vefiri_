@extends('layouts.storefront')

@section('title', $query !== '' ? 'Search results for '.$query.' | Vefiri' : 'Search products | Vefiri')

@section('content')
    <section class="pt-4">
        <x-ui.container>
            <div class="rounded-2xl border border-black/[0.03] bg-white p-5 shadow-card sm:p-6">
                <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                    <x-ui.icon name="chevron-right" class="size-3" />
                    <span class="text-ink-soft">Search</span>
                </nav>

                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-ink sm:text-2xl">
                            @if ($query !== '')
                                Results for &ldquo;{{ $query }}&rdquo;
                            @else
                                All products
                            @endif
                        </h1>
                        <p class="mt-1 text-xs text-muted">
                            {{ number_format($products->count()) }} {{ $products->count() === 1 ? 'product' : 'products' }} found
                            @if ($selectedCategory !== '' && $selectedCategory !== 'All categories')
                                in {{ $selectedCategory }}
                            @endif
                        </p>
                    </div>

                    @if ($selectedCategory !== '' && $selectedCategory !== 'All categories')
                        <a
                            href="{{ route('search', array_filter(['q' => $query])) }}"
                            class="inline-flex items-center gap-1.5 self-start rounded-full bg-surface px-3 py-1.5 text-2xs font-semibold text-ink-soft transition hover:bg-brand-50 hover:text-brand-700"
                        >
                            {{ $selectedCategory }}
                            <x-ui.icon name="close" class="size-3" />
                        </a>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </section>

    <section class="pt-4 pb-2">
        <x-ui.container>
            @if ($products->isEmpty())
                <div class="rounded-2xl border border-black/[0.03] bg-white px-6 py-16 text-center shadow-card">
                    <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-surface text-muted">
                        <x-ui.icon name="search" class="size-7" />
                    </span>
                    <h2 class="mt-4 text-lg font-bold text-ink">No products found</h2>
                    <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-muted">
                        Try a different keyword or browse all categories to find what you need.
                    </p>
                    <x-buttons.primary href="{{ route('search') }}" class="mt-5">
                        Browse all products
                    </x-buttons.primary>
                </div>
            @else
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($products as $product)
                        <x-cards.product :product="$product" />
                    @endforeach
                </div>
            @endif
        </x-ui.container>
    </section>
@endsection
