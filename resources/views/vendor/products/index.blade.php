@extends('layouts.storefront')

@section('title', 'Your products | Vefiri vendor')
@section('description', 'Manage your Vefiri vendor product listings.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.dashboard') }}" class="transition hover:text-ink">Vendor dashboard</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Products</span>
            </nav>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Your products</h1>
                    <p class="mt-1 text-xs text-muted">Add, edit or hide products from your storefront.</p>
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

            <section class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <header class="flex items-center justify-between border-b border-line px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Product listings</h2>
                        <p class="mt-0.5 text-2xs text-muted">Showing {{ $products->count() }} of {{ $products->total() }} products.</p>
                    </div>
                </header>

                <div class="divide-y divide-line">
                    @forelse ($products as $product)
                        <article class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center">
                            <img
                                src="{{ $product->imageUrl() }}"
                                alt="{{ $product->name }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                width="64"
                                height="64"
                                class="size-16 shrink-0 rounded-xl object-cover"
                            >
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-ink">{{ $product->name }}</p>
                                <p class="mt-0.5 text-2xs text-muted">{{ $product->category }}</p>
                                <p class="mt-1 text-2xs text-muted">Price: <span class="font-semibold text-ink">&#8358;{{ number_format($product->price) }}</span> @if ($product->compare_price) &middot; Was <span class="line-through">&#8358;{{ number_format($product->compare_price) }}</span> @endif &middot; Stock: {{ $product->stock }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span @class([
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    'bg-[#e7f8f2] text-choice-dark' => $product->is_active,
                                    'bg-surface text-muted' => ! $product->is_active,
                                ])>
                                    {{ $product->is_active ? 'Live' : 'Hidden' }}
                                </span>
                                <x-buttons.secondary href="{{ route('vendor.products.edit', $product) }}" size="sm">
                                    Edit
                                </x-buttons.secondary>
                                <form method="POST" action="{{ route('vendor.products.destroy', $product) }}" onsubmit="return confirm('Remove this product from your storefront?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 items-center justify-center rounded-full px-3.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <span class="mx-auto flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                                <x-ui.icon name="package" class="size-5" />
                            </span>
                            <p class="mt-3 text-sm font-bold text-ink">No products yet</p>
                            <p class="mx-auto mt-1 max-w-sm text-xs leading-5 text-muted">
                                Upload your first product to start selling to shoppers across Nigeria.
                            </p>
                            <x-buttons.primary href="{{ route('vendor.products.create') }}" class="mt-4">
                                <x-ui.icon name="plus" class="size-4" />
                                Upload product
                            </x-buttons.primary>
                        </div>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="border-t border-line px-5 py-3">
                        {{ $products->links() }}
                    </div>
                @endif
            </section>
        </x-ui.container>
    </section>
@endsection
