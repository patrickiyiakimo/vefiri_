@props([
    'class' => '',
    'tone' => 'dark',
])

<a
    href="{{ route('home') }}"
    aria-label="Vefiri home"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 ' . $class]) }}
>
    <img
        src="{{ asset('images/vefiri-logo.jpeg') }}"
        alt=""
        width="1024"
        height="1024"
        class="size-8 shrink-0 rounded-lg object-contain sm:size-9"
    >
    <span @class([
        'text-[22px] leading-none font-extrabold tracking-[-0.055em] sm:text-[25px]',
        'text-white' => $tone === 'light',
        'text-ink' => $tone !== 'light',
    ])>
        <span class="text-brand-500">Ve</span>firi
    </span>
</a>
