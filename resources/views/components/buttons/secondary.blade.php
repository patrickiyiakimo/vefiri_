@props([
    'href' => null,
    'type' => 'button',
    'size' => 'md',
])

@php
    $classes = match ($size) {
        'sm' => 'h-8 gap-1.5 rounded-full px-3.5 text-xs',
        'lg' => 'h-12 gap-2 rounded-full px-7 text-sm',
        default => 'h-10 gap-2 rounded-full px-5 text-sm',
    };
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center border border-ink bg-white font-semibold text-ink transition hover:bg-ink hover:text-white active:scale-[0.98] {$classes}"]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center border border-ink bg-white font-semibold text-ink transition hover:bg-ink hover:text-white active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 {$classes}"]) }}
    >
        {{ $slot }}
    </button>
@endif
