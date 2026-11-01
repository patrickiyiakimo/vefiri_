@props([
    'banner',
])

<article {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl bg-gradient-to-br p-5 sm:p-6 ' . $banner['theme']]) }}>
    <div class="absolute -top-10 -right-8 size-36 rounded-full border-[22px] border-white/30" aria-hidden="true"></div>
    <div class="absolute -right-4 -bottom-12 size-28 rounded-full border-[18px] border-white/25" aria-hidden="true"></div>

    <div class="relative z-10 max-w-[270px]">
        <p class="text-2xs font-bold tracking-[0.16em] text-brand-700/75 uppercase">{{ $banner['eyebrow'] }}</p>
        <h3 class="mt-1.5 text-lg leading-6 font-bold text-ink sm:text-xl">{{ $banner['title'] }}</h3>
        <p class="mt-1.5 text-xs leading-5 text-ink-soft">{{ $banner['description'] }}</p>

        <x-buttons.secondary href="{{ route('home') }}" size="sm" class="mt-4 border-ink/15 bg-white/75 ring-1 ring-white/60">
            {{ $banner['cta'] }}
            <x-ui.icon name="arrow-right" class="size-3.5" />
        </x-buttons.secondary>
    </div>

    <span class="absolute right-6 bottom-5 flex size-12 items-center justify-center rounded-2xl bg-white/60 text-brand-600 shadow-sm backdrop-blur">
        <x-ui.icon :name="$banner['icon']" class="size-6" />
    </span>
</article>
