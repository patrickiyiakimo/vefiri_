@props([
    'rating' => 0,
    'sold' => null,
    'compact' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-2xs text-muted']) }}>
    <span class="flex items-center gap-0.5 text-[#ffb400]" aria-label="{{ $rating }} out of 5 stars">
        @for ($star = 1; $star <= 5; $star++)
            <svg
                viewBox="0 0 24 24"
                class="size-3 {{ $star <= round($rating) ? 'fill-current' : 'fill-[#dedede]' }}"
                aria-hidden="true"
            >
                <path d="m12 2.5 2.9 6 6.6.9-4.8 4.6 1.2 6.5-5.9-3.1L6.1 20.5l1.2-6.5-4.8-4.6 6.6-.9 2.9-6Z" />
            </svg>
        @endfor
    </span>

    @if (! $compact)
        <span class="font-medium text-ink-soft">{{ number_format($rating, 1) }}</span>
    @endif

    @if ($sold)
        <span class="h-2.5 w-px bg-line" aria-hidden="true"></span>
        <span>{{ $sold }} sold</span>
    @endif
</div>
