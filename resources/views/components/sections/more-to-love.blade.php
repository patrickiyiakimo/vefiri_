@props([
    'tabs' => [],
])

<section class="pt-4" aria-labelledby="more-to-love-heading">
    <x-ui.container>
        <div data-tabs class="rounded-2xl border border-black/[0.03] bg-white px-3 py-4 shadow-card sm:px-5 sm:py-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <x-ui.section-heading
                    id="more-to-love-heading"
                    title="More to love"
                    subtitle="Handpicked finds based on what shoppers are loving right now."
                    accent
                />

                <div class="no-scrollbar flex items-center gap-1 overflow-x-auto" role="tablist" aria-label="Recommended products">
                    @foreach ($tabs as $tab)
                        <button
                            type="button"
                            role="tab"
                            data-tab-target="{{ $tab['id'] }}"
                            data-active="{{ $loop->first ? 'true' : 'false' }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            class="shrink-0 rounded-full px-3 py-2 text-xs font-semibold text-muted transition hover:bg-surface hover:text-ink data-[active=true]:bg-ink data-[active=true]:text-white"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($tabs as $tab)
                <div
                    data-tab-panel="{{ $tab['id'] }}"
                    data-load-more-scope
                    @class([
                        'mt-4',
                        'hidden' => ! $loop->first,
                    ])
                >
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-6">
                        @foreach ($tab['products'] as $product)
                            <x-cards.product
                                :product="$product"
                                data-more-item
                                :class="$loop->index >= 12 ? 'hidden' : null"
                            />
                        @endforeach
                    </div>

                    @if (count($tab['products']) > 12)
                        <div data-load-more-controls class="mt-5 flex justify-center">
                            <x-buttons.secondary type="button" data-load-more class="px-7">
                                Show more products
                                <x-ui.icon name="chevron-down" class="size-4" />
                            </x-buttons.secondary>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-ui.container>
</section>
