@props([
    'direction' => 'next',
    'tone' => 'light',
])

@php
    $classes = match ($tone) {
        'dark' => 'bg-black/35 text-white hover:bg-black/55',
        'glass' => 'border border-white/60 bg-white/85 text-ink shadow-sm backdrop-blur hover:bg-white',
        default => 'border border-line bg-white text-ink shadow-sm hover:border-brand-200 hover:text-brand-600',
    };
@endphp

<button
    type="button"
    aria-label="{{ $direction === 'prev' ? 'Previous items' : 'Next items' }}"
    {{ $attributes->merge(['class' => "inline-flex size-9 shrink-0 items-center justify-center rounded-full transition disabled:pointer-events-none disabled:opacity-30 {$classes}"]) }}
>
    <x-ui.icon :name="$direction === 'prev' ? 'chevron-left' : 'chevron-right'" class="size-4" />
</button>
