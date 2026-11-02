@props([
    'amount',
    'old' => null,
    'size' => 'md',
])

<span {{ $attributes->merge(['class' => 'inline-flex items-baseline gap-1.5']) }}>
    <span @class([
        'font-bold tracking-tight text-ink',
        'text-base' => $size === 'sm',
        'text-lg' => $size === 'md',
        'text-2xl' => $size === 'lg',
    ])>
        <span class="text-[0.72em]">&#8358;</span>{{ number_format($amount) }}
    </span>

    @if ($old && $old > $amount)
        <span @class([
            'text-muted line-through',
            'text-[10px]' => $size === 'sm',
            'text-xs' => $size !== 'sm',
        ])>
            &#8358;{{ number_format($old) }}
        </span>
    @endif
</span>
