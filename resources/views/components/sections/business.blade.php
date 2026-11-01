@props([
    'stats' => [],
])

<section class="pt-4" aria-labelledby="business-heading">
    <x-ui.container>
        <div class="relative overflow-hidden rounded-2xl bg-[#0e1838] px-5 py-6 text-white shadow-card sm:px-8 sm:py-8">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -top-24 -right-24 size-80 rounded-full border-[42px] border-[#27376b]/60"></div>
                <div class="absolute -bottom-28 left-1/3 size-72 rounded-full bg-[#ff5a00]/10 blur-3xl"></div>
                <div class="absolute top-0 right-1/4 h-full w-px rotate-12 bg-white/5"></div>
            </div>

            <div class="relative grid items-center gap-7 lg:grid-cols-[minmax(0,1fr)_minmax(360px,0.8fr)]">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-2xs font-semibold tracking-[0.08em] text-[#aebbe8] uppercase ring-1 ring-white/10">
                        <x-ui.icon name="briefcase" class="size-3" />
                        AliExpress Business
                    </span>

                    <h2 id="business-heading" class="mt-3 max-w-xl text-2xl leading-tight font-black tracking-tight sm:text-3xl">
                        Source smarter.<br class="hidden sm:block"> Scale your business globally.
                    </h2>

                    <p class="mt-3 max-w-xl text-xs leading-5 text-white/65 sm:text-sm sm:leading-6">
                        Access factory-direct pricing, dedicated sourcing support and flexible payment terms built for growing businesses.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach (['Tax exemptions', 'Express payments', 'Financial support'] as $perk)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/[0.07] px-2.5 py-1 text-2xs text-white/80 ring-1 ring-white/10">
                                <x-ui.icon name="check" class="size-3 text-[#37d7a5]" />
                                {{ $perk }}
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <x-buttons.secondary href="{{ route('home') }}" class="border-white bg-white text-[#0e1838] hover:bg-[#ffe4d4] hover:text-[#0e1838]">
                            Start sourcing
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </x-buttons.secondary>
                        <a href="{{ route('home') }}" class="text-xs font-semibold text-white/80 underline decoration-white/30 underline-offset-4 transition hover:text-white">
                            Learn more
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    @foreach ($stats as $stat)
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm">
                            <p class="text-2xl font-black tracking-tight text-white sm:text-3xl">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-2xs leading-4 text-white/55">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-ui.container>
</section>
