@extends('layouts.storefront')

@section('title', 'Edit product | Vefiri vendor')
@section('description', 'Update your product details, pricing or stock.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.dashboard') }}" class="transition hover:text-ink">Vendor dashboard</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.products.index') }}" class="transition hover:text-ink">Products</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Edit product</span>
            </nav>

            <div class="mt-3">
                <h1 class="text-2xl font-black tracking-tight text-ink">Edit product</h1>
                <p class="mt-1 text-xs text-muted">{{ $product->name }}</p>
            </div>

            <div class="mt-4">
                <x-vendor.nav :vendor="$vendor" />
            </div>

            <form method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data" class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                @csrf
                @method('PATCH')

                <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Product details</h2>
                        <p class="mt-0.5 text-2xs text-muted">Update the information shown to shoppers.</p>
                    </header>

                    <div class="grid gap-3 px-5 py-4">
                        <label class="grid gap-1.5">
                            <span class="text-xs font-semibold text-ink">Product name</span>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $product->name) }}"
                                required
                                class="h-10 rounded-xl border border-line bg-white px-3 text-xs text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                            >
                            @error('name')
                                <span class="text-2xs text-brand-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1.5">
                            <span class="text-xs font-semibold text-ink">Category</span>
                            <select
                                name="category"
                                required
                                class="h-10 rounded-xl border border-line bg-white px-3 text-xs font-semibold text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                            >
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <span class="text-2xs text-brand-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-1.5">
                            <span class="text-xs font-semibold text-ink">Description</span>
                            <textarea
                                name="description"
                                rows="7"
                                required
                                class="rounded-xl border border-line bg-white px-3 py-2 text-xs leading-5 text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                            >{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <span class="text-2xs text-brand-700">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                </section>

                <div class="grid gap-4">
                    <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                        <header class="border-b border-line px-5 py-4">
                            <h2 class="text-sm font-bold text-ink">Pricing & stock</h2>
                        </header>

                        <div class="grid gap-3 px-5 py-4">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="grid gap-1.5">
                                    <span class="text-xs font-semibold text-ink">Price (NGN)</span>
                                    <input
                                        type="number"
                                        name="price"
                                        min="100"
                                        step="1"
                                        value="{{ old('price', $product->price) }}"
                                        required
                                        class="h-10 rounded-xl border border-line bg-white px-3 text-xs text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                    >
                                    @error('price')
                                        <span class="text-2xs text-brand-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid gap-1.5">
                                    <span class="text-xs font-semibold text-ink">Compare price (optional)</span>
                                    <input
                                        type="number"
                                        name="compare_price"
                                        min="101"
                                        step="1"
                                        value="{{ old('compare_price', $product->compare_price) }}"
                                        class="h-10 rounded-xl border border-line bg-white px-3 text-xs text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                    >
                                    @error('compare_price')
                                        <span class="text-2xs text-brand-700">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>

                            <label class="grid gap-1.5">
                                <span class="text-xs font-semibold text-ink">Stock quantity</span>
                                <input
                                    type="number"
                                    name="stock"
                                    min="0"
                                    step="1"
                                    value="{{ old('stock', $product->stock) }}"
                                    required
                                    class="h-10 rounded-xl border border-line bg-white px-3 text-xs text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                                >
                                @error('stock')
                                    <span class="text-2xs text-brand-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                        <header class="border-b border-line px-5 py-4">
                            <h2 class="text-sm font-bold text-ink">Product image</h2>
                            <p class="mt-0.5 text-2xs text-muted">Leave empty to keep the current image.</p>
                        </header>

                        <div class="grid gap-3 px-5 py-4">
                            <img
                                src="{{ $product->imageUrl() }}"
                                alt=""
                                onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                width="128"
                                height="128"
                                class="size-32 rounded-xl object-cover"
                            >

                            <label class="grid gap-1.5">
                                <span class="text-xs font-semibold text-ink">Replace image</span>
                                <input
                                    type="file"
                                    name="image"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-xs text-muted file:mr-3 file:h-9 file:rounded-full file:border-0 file:bg-surface file:px-4 file:text-xs file:font-semibold file:text-ink hover:file:bg-[#f0f0f0]"
                                >
                                @error('image')
                                    <span class="text-2xs text-brand-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="flex items-center gap-2 text-xs text-ink">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="size-4 rounded border-line text-brand-600 focus:ring-brand-500" @checked(old('is_active', $product->is_active))>
                                <span class="font-semibold">Live on storefront</span>
                            </label>
                        </div>
                    </section>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-buttons.primary type="submit">
                            <x-ui.icon name="check" class="size-4" />
                            Update product
                        </x-buttons.primary>
                        <x-buttons.secondary href="{{ route('vendor.products.index') }}">
                            Cancel
                        </x-buttons.secondary>
                    </div>
                </div>
            </form>
        </x-ui.container>
    </section>
@endsection
