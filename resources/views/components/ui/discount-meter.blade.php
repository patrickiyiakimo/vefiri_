@props([
    'percentage' => 60,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    <div class="h-1.5 overflow-hidden rounded-full bg-brand-100">
        <div
            class="h-full rounded-full bg-gradient-to-r from-brand-500 to-[#ff9f00]"
            style="width: {{ max(0, min(100, $percentage)) }}%"
        ></div>
    </div>
    <p class="text-2xs font-medium text-brand-600">{{ $percentage }}% claimed</p>
</div>
