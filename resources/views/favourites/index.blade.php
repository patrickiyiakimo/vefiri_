@extends('layouts.storefront')

@section('title', 'My wishlist | Vefiri')
@section('description', 'View and manage the products you have saved to your Vefiri wishlist.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">My wishlist</span>
            </nav>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">My wishlist</h1>
                    <p class="mt-1 text-xs text-muted" data-wishlist-summary>
                        {{ number_format($products->count()) }} {{ $products->count() === 1 ? 'item' : 'items' }} saved to your account.
                    </p>
                </div>

                <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    Continue shopping
                    <x-ui.icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            <div
                data-wishlist-empty
                @class([
                    'mt-4 rounded-2xl border border-black/[0.03] bg-white px-6 py-16 text-center shadow-card',
                    'hidden' => ! $products->isEmpty(),
                ])
            >
                <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-surface text-muted">
                    <x-ui.icon name="heart" class="size-7" />
                </span>
                <h2 class="mt-4 text-lg font-bold text-ink">Your wishlist is empty</h2>
                <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-muted">
                    Tap the heart on any product to save it here and find it again later.
                </p>
                <x-buttons.primary href="{{ route('search') }}" class="mt-5">
                    Browse products
                </x-buttons.primary>
            </div>

            <div
                data-wishlist-grid
                @class([
                    'mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-5',
                    'hidden' => $products->isEmpty(),
                ])
            >
                @foreach ($products as $product)
                    <x-cards.product :product="$product" data-wishlist-item />
                @endforeach
            </div>
        </x-ui.container>
    </section>
@endsection
