@props([
    'status',
])

@php
    $classes = match ($status->value) {
        \App\Enums\OrderStatus::Paid->value,
        \App\Enums\OrderStatus::Delivered->value => 'bg-[#e7f8f2] text-choice-dark',
        \App\Enums\OrderStatus::Failed->value => 'bg-brand-50 text-brand-700',
        default => 'bg-[#fff7e6] text-[#8a5a00]',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold {$classes}"]) }}>
    {{ $status->label() }}
</span>
