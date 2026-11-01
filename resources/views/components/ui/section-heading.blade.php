@props([
    'title',
    'subtitle' => null,
    'action' => null,
    'href' => '#',
    'accent' => false,
    'id' => null,
])

<div {{ $attributes->merge(['class' => 'flex items-end justify-between gap-4']) }}>
    <div class="min-w-0">
        <div class="flex items-center gap-2">
            <h2
                @if ($id) id="{{ $id }}" @endif
                @class([
                    'text-xl font-bold tracking-tight sm:text-2xl',
                    'bg-gradient-to-r from-brand-600 to-[#ff9f00] bg-clip-text text-transparent' => $accent,
                ])
            >
                {{ $title }}
            </h2>
        </div>
        @if ($subtitle)
            <p class="mt-1 text-xs text-muted sm:text-sm">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($action)
        <a
            href="{{ $href }}"
            class="group inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-ink-soft transition hover:text-brand-600 sm:text-sm"
        >
            {{ $action }}
            <x-ui.icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
        </a>
    @endif
</div>
