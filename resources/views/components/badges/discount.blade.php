@props([
    'percentage',
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md bg-brand-600 px-1.5 py-0.5 text-2xs font-bold leading-none text-white shadow-sm']) }}>
    -{{ $percentage }}%
</span>
