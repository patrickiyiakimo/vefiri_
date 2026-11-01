@props([
    'label' => 'Choice',
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full bg-ink px-2 py-1 text-2xs font-bold leading-none text-white']) }}>
    <x-ui.icon name="lightning" class="size-2.5 text-[#ffd60a]" />
    {{ $label }}
</span>
