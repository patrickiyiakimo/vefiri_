@props([
    'tabs' => [],
])

<section class="pt-4" aria-labelledby="todays-deals-heading">
    <x-ui.container>
        <div data-tabs class="overflow-hidden rounded-2xl border border-black/[0.03] bg-white shadow-card">
            <div class="relative overflow-hidden border-b border-line bg-gradient-to-r from-[#fff0e8] via-[#fff8f4] to-white px-4 py-4 sm:px-6">
                <div class="absolute -top-16 right-12 size-40 rounded-full border-[22px] border-brand-100/60" aria-hidden="true"></div>

                <div class="relative flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 id="todays-deals-heading" class="text-xl font-black tracking-tight text-ink sm:text-2xl">
                                Today's deals
                            </h2>
                            <span class="inline-flex items-center gap-1 rounded-full bg-brand-600 px-2 py-1 text-2xs font-bold text-white">
                                <x-ui.icon name="flame" class="size-2.5" />
                                Hot
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-muted">Lowest prices of the day. New deals added daily.</p>
                    </div>

                    <div
                        data-countdown
                        class="flex w-fit items-center gap-2 rounded-full bg-ink px-3 py-1.5 text-2xs font-medium text-white"
                    >
                        <x-ui.icon name="clock" class="size-3.5 text-[#ffd60a]" />
                        Ends in
                        <span data-countdown-value class="font-mono text-xs font-bold tabular-nums">00:00:00</span>
                    </div>
                </div>
            </div>

            <div class="px-4 pt-4 sm:px-6">
                <div class="no-scrollbar flex items-center gap-2 overflow-x-auto pb-4" role="tablist" aria-label="Deal collections">
                    @foreach ($tabs as $tab)
                        <button
                            type="button"
                            role="tab"
                            data-tab-target="{{ $tab['id'] }}"
                            data-active="{{ $loop->first ? 'true' : 'false' }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            class="shrink-0 rounded-full bg-surface px-4 py-2 text-xs font-semibold text-ink-soft transition hover:bg-brand-50 hover:text-brand-700 data-[active=true]:bg-ink data-[active=true]:text-white"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($tabs as $tab)
                <div
                    data-tab-panel="{{ $tab['id'] }}"
                    @class([
                        'px-3 pb-4 sm:px-5 sm:pb-5',
                        'hidden' => ! $loop->first,
                    ])
                >
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6 lg:gap-3">
                        @foreach ($tab['products'] as $product)
                            <x-cards.deal
                                :product="$product"
                                :progress="min(96, 52 + $loop->index * 7)"
                            />
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.container>
</section>
