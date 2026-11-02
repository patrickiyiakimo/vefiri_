@props([
    'class' => '',
    'tone' => 'dark',
])

<a
    href="{{ route('home') }}"
    aria-label="Vefiri home"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 ' . $class]) }}
>
    <span class="flex size-8 shrink-0 items-center justify-center rounded-[10px] bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-[0_6px_16px_-8px_rgba(240,68,0,0.9)]">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" class="size-5">
            <path d="M6 6.5h3.3l2.7 8 2.7-8H18L14.1 19H9.9L6 6.5Z" fill="currentColor" />
            <circle cx="18.1" cy="6.9" r="1.6" fill="#ffd60a" />
        </svg>
    </span>
    <span @class([
        'text-[25px] leading-none font-extrabold tracking-[-0.055em] sm:text-[27px]',
        'text-white' => $tone === 'light',
        'text-ink' => $tone !== 'light',
    ])>
        <span class="text-brand-500">Ve</span>firi
    </span>
</a>
