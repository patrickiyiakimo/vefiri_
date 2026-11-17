@extends('layouts.storefront')

@section('title', $category.' | Vefiri')
@section('description', 'Browse '.$category.' products on Vefiri with nationwide delivery, buyer protection and easy returns.')

@section('content')
    <section class="pt-4">
        <x-ui.container>
            <div class="rounded-2xl border border-black/[0.03] bg-white p-5 shadow-card sm:p-6">
                <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                    <x-ui.icon name="chevron-right" class="size-3" />
                    <span class="text-ink-soft">{{ $category }}</span>
                </nav>

                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 class="text-xl font-black tracking-tight text-ink sm:text-2xl">{{ $category }}</h1>
                        <p class="mt-1 text-xs text-muted">
                            {{ number_format($products->count()) }} {{ $products->count() === 1 ? 'product' : 'products' }} in this category
                        </p>
                    </div>

                    <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 self-start text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                        Browse all products
                        <x-ui.icon name="arrow-right" class="size-3.5" />
                    </a>
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
                        There are no products in {{ $category }} yet. Browse all products to keep shopping.
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
