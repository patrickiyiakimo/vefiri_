@props([
    'products' => [],
])

<section class="pt-4" aria-labelledby="super-deals-heading">
    <x-ui.container>
        <div class="overflow-hidden rounded-2xl border border-black/[0.03] bg-white shadow-card">
            <div class="relative overflow-hidden bg-gradient-to-r from-[#fff1e8] via-[#fff7f1] to-white px-4 py-4 sm:px-6">
                <div class="absolute -top-24 right-10 size-52 rounded-full border-[28px] border-brand-100/70" aria-hidden="true"></div>

                <div class="relative flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="flex size-8 items-center justify-center rounded-full bg-brand-600 text-white">
                                <x-ui.icon name="flame" class="size-4" />
                            </span>
                            <h2 id="super-deals-heading" class="bg-gradient-to-r from-brand-600 to-[#ff9f00] bg-clip-text text-xl font-black tracking-tight text-transparent sm:text-2xl">
                                SuperDeals
                            </h2>
                        </div>
                        <p class="mt-1 text-xs text-muted">Up to 80% off trending products. Limited stock only.</p>
                    </div>

                    <a href="{{ route('home') }}" class="group inline-flex items-center gap-1 text-xs font-semibold text-ink-soft transition hover:text-brand-600">
                        View all deals
                        <x-ui.icon name="arrow-right" class="size-3.5 transition group-hover:translate-x-0.5" />
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 p-3 sm:grid-cols-3 sm:gap-3 sm:p-4 md:grid-cols-4 lg:grid-cols-5">
                @foreach ($products as $product)
                    <x-cards.deal
                        :product="$product"
                        :progress="min(97, 58 + $loop->index * 4)"
                    />
                @endforeach
            </div>
        </div>
    </x-ui.container>
</section>
