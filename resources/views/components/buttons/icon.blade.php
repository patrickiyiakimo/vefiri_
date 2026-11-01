@props([
    'href' => null,
    'type' => 'button',
    'label',
    'tone' => 'light',
    'size' => 'md',
])

@php
    $tones = match ($tone) {
        'brand' => 'bg-brand-600 text-white hover:bg-brand-700',
        'dark' => 'bg-ink text-white hover:bg-black',
        'glass' => 'bg-white/90 text-ink shadow-sm backdrop-blur hover:bg-white',
        default => 'bg-white text-ink-soft hover:bg-brand-50 hover:text-brand-600',
    };

    $sizes = match ($size) {
        'sm' => 'size-8',
        'lg' => 'size-12',
        default => 'size-10',
    };

    $classes = "inline-flex shrink-0 items-center justify-center rounded-full transition active:scale-95 {$tones} {$sizes}";
@endphp

@if ($href)
    <a href="{{ $href }}" aria-label="{{ $label }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        aria-label="{{ $label }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif
