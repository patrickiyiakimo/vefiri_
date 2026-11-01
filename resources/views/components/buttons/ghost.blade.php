@props([
    'href' => null,
    'type' => 'button',
])

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => 'inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-full px-3 text-xs font-semibold text-ink-soft transition hover:bg-black/5 hover:text-ink']) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => 'inline-flex h-9 shrink-0 items-center justify-center gap-1.5 rounded-full px-3 text-xs font-semibold text-ink-soft transition hover:bg-black/5 hover:text-ink']) }}
    >
        {{ $slot }}
    </button>
@endif
