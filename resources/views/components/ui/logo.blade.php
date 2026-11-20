@props([
    'class' => '',
])

<a
    href="{{ route('home') }}"
    aria-label="Vefiri home"
    {{ $attributes->merge(['class' => 'inline-flex items-center ' . $class]) }}
>
    <img
        src="{{ asset('images/vefiri-logo.jpeg') }}"
        alt=""
        width="1024"
        height="1024"
        class="size-8 rounded-lg object-contain sm:size-9"
    >
</a>
