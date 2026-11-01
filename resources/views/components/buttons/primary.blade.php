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
        {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center bg-gradient-to-r from-brand-600 to-[#ff8a00] font-semibold text-white shadow-[0_6px_18px_-8px_rgba(240,68,0,0.8)] transition hover:brightness-105 active:scale-[0.98] {$classes}"]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center bg-gradient-to-r from-brand-600 to-[#ff8a00] font-semibold text-white shadow-[0_6px_18px_-8px_rgba(240,68,0,0.8)] transition hover:brightness-105 active:scale-[0.98] disabled:pointer-events-none disabled:opacity-50 {$classes}"]) }}
    >
        {{ $slot }}
    </button>
@endif
