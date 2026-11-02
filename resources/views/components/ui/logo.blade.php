@props([
    'class' => '',
    'tone' => 'dark',
])

<a
    href="{{ route('home') }}"
    aria-label="Vefiri home"
    {{ $attributes->merge(['class' => 'inline-flex items-center ' . $class]) }}
>
    <span @class([
        'text-[25px] leading-none font-extrabold tracking-[-0.055em] sm:text-[27px]',
        'text-white' => $tone === 'light',
        'text-ink' => $tone !== 'light',
    ])>
        <span class="text-brand-500">Ve</span>firi
    </span>
</a>
