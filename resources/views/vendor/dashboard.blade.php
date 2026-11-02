@extends('layouts.storefront')

@section('title', 'Vendor dashboard | Vefiri')
@section('description', 'Manage your Vefiri storefront, track stock and see how your products are performing.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Vendor dashboard</span>
            </nav>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Vendor dashboard</h1>
                    <p class="mt-1 text-xs text-muted">Upload products and keep your storefront fresh for shoppers.</p>
                </div>

                <x-buttons.primary href="{{ route('vendor.products.create') }}">
                    <x-ui.icon name="plus" class="size-4" />
                    Upload product
                </x-buttons.primary>
            </div>

            <div class="mt-4">
                <x-vendor.nav :vendor="$vendor" />
            </div>

            @if (session('status'))
                <div class="mt-3 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-semibold text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Total products', 'value' => $stats['total'], 'icon' => 'package'],
                    ['label' => 'Live products', 'value' => $stats['active'], 'icon' => 'badge-check'],
                    ['label' => 'Out of stock', 'value' => $stats['outOfStock'], 'icon' => 'clock'],
                    ['label' => 'Units in stock', 'value' => $stats['stock'], 'icon' => 'store'],
                ] as $stat)
                    <article class="rounded-2xl border border-black/[0.04] bg-white p-4 shadow-card">
                        <div class="flex items-center justify-between">
                            <p class="text-2xs text-muted">{{ $stat['label'] }}</p>
                            <span class="flex size-8 items-center justify-center rounded-full bg-surface text-ink-soft">
                                <x-ui.icon :name="$stat['icon']" class="size-4" />
                            </span>
                        </div>
                        <p class="mt-2 text-2xl font-black tracking-tight text-ink">{{ number_format($stat['value']) }}</p>
                    </article>
                @endforeach
            </div>

            <section class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <header class="flex items-center justify-between border-b border-line px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Recently uploaded</h2>
                        <p class="mt-0.5 text-2xs text-muted">Your latest products and their current status.</p>
                    </div>
                    <a href="{{ route('vendor.products.index') }}" class="text-2xs font-semibold text-brand-600">Manage all</a>
                </header>

                <div class="divide-y divide-line">
                    @forelse ($recentProducts as $product)
                        <article class="flex items-center gap-3 px-5 py-3">
                            <img
                                src="{{ asset('storage/'.$product->image_path) }}"
                                alt=""
                                width="48"
                                height="48"
                                class="size-12 shrink-0 rounded-xl object-cover"
                            >
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-ink">{{ $product->name }}</p>
                                <p class="mt-0.5 text-2xs text-muted">{{ $product->category }} &middot; {{ $product->stock }} in stock</p>
                            </div>
                            <span @class([
                                'shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold',
                                'bg-[#e7f8f2] text-choice-dark' => $product->is_active,
                                'bg-surface text-muted' => ! $product->is_active,
                            ])>
                                {{ $product->is_active ? 'Live' : 'Hidden' }}
                            </span>
                            <a href="{{ route('vendor.products.edit', $product) }}" class="shrink-0 text-2xs font-semibold text-brand-600">Edit</a>
                        </article>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <span class="mx-auto flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                                <x-ui.icon name="package" class="size-5" />
                            </span>
                            <p class="mt-3 text-sm font-bold text-ink">No products uploaded yet</p>
                            <p class="mx-auto mt-1 max-w-sm text-xs leading-5 text-muted">
                                Add your first product and it will appear in the homepage vendor section as soon as it is live.
                            </p>
                            <x-buttons.primary href="{{ route('vendor.products.create') }}" class="mt-4">
                                <x-ui.icon name="plus" class="size-4" />
                                Upload your first product
                            </x-buttons.primary>
                        </div>
                    @endforelse
                </div>
            </section>
        </x-ui.container>
    </section>
@endsection
