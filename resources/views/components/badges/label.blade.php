@props([
    'tone' => 'light',
    'icon' => null,
])

@php
    $tones = match ($tone) {
        'brand' => 'bg-brand-600 text-white',
        'green' => 'bg-[#e7f8f2] text-choice-dark',
        'dark' => 'bg-ink text-white',
        'yellow' => 'bg-[#fff5cc] text-[#8a5d00]',
        default => 'bg-white/90 text-ink-soft ring-1 ring-black/5',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2 py-1 text-2xs font-semibold leading-none {$tones}"]) }}>
    @if ($icon)
        <x-ui.icon :name="$icon" class="size-2.5" />
    @endif
    {{ $slot }}
</span>
