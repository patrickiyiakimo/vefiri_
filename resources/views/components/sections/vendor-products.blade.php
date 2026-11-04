@props([
    'products' => [],
])

@if (count($products) > 0)
    <section class="pt-4" aria-labelledby="vendor-products-heading">
        <x-ui.container>
            <div class="rounded-2xl border border-black/[0.03] bg-white px-3 py-4 shadow-card sm:px-5 sm:py-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 id="vendor-products-heading" class="text-xl font-black tracking-tight text-ink sm:text-2xl">
                                Fresh from verified vendors
                            </h2>
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f8f2] px-2 py-1 text-2xs font-bold text-choice-dark">
                                <x-ui.icon name="badge-check" class="size-3" />
                                Verified sellers
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-muted">New products uploaded by trusted Vefiri vendors.</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                            Shop all products
                            <x-ui.icon name="arrow-right" class="size-3.5" />
                        </a>
                        @if (auth()->user()?->isVendor())
                            <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">
                                Vendor dashboard
                                <x-ui.icon name="store" class="size-3.5" />
                            </a>
                        @else
                            <a href="{{ route('vendors.apply') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">
                                Sell on Vefiri
                                <x-ui.icon name="store" class="size-3.5" />
                            </a>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-6">
                    @foreach ($products as $product)
                        <x-cards.product :product="$product" />
                    @endforeach
                </div>
            </div>
        </x-ui.container>
    </section>
@endif
