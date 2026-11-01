@props([
    'icon',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-start gap-3']) }}>
    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
        <x-ui.icon :name="$icon" class="size-5" />
    </span>
    <div class="min-w-0">
        <p class="text-sm font-semibold text-ink">{{ $title }}</p>
        @if ($description)
            <p class="mt-0.5 text-xs leading-5 text-muted">{{ $description }}</p>
        @endif
    </div>
</div>
