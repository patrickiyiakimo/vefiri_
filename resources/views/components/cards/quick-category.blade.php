@props([
    'label',
    'image',
    'icon' => null,
])

<a
    href="{{ route('home') }}"
    {{ $attributes->merge(['class' => 'group flex w-[72px] shrink-0 flex-col items-center gap-2 text-center sm:w-[80px]']) }}
>
    <span class="relative flex size-14 items-center justify-center overflow-hidden rounded-full bg-surface ring-1 ring-black/5 transition group-hover:-translate-y-0.5 group-hover:ring-2 group-hover:ring-brand-400 sm:size-16">
        <img
            src="{{ asset($image) }}"
            alt=""
            width="128"
            height="128"
            loading="lazy"
            decoding="async"
            class="h-full w-full object-cover transition duration-300 group-hover:scale-110"
        >
        @if ($icon)
            <span class="absolute right-0 bottom-0 flex size-5 items-center justify-center rounded-full bg-white text-brand-600 shadow-sm">
                <x-ui.icon :name="$icon" class="size-3" />
            </span>
        @endif
    </span>
    <span class="line-clamp-1 text-2xs font-semibold text-ink-soft transition group-hover:text-brand-600 sm:text-xs">
        {{ $label }}
    </span>
</a>
