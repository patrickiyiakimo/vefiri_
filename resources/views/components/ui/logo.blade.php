@props([
    'class' => '',
])

<a
    href="{{ route('home') }}"
    aria-label="AliExpress home"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 ' . $class]) }}
>
    <span class="text-[27px] leading-none font-extrabold tracking-[-0.055em] sm:text-[30px]">
        <span class="text-[#e62e04]">Ali</span><span class="text-brand-500">Express</span>
    </span>
</a>
