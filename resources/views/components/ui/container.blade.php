@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'mx-auto w-full max-w-[1400px] px-3 sm:px-4 lg:px-5 ' . $class]) }}>
    {{ $slot }}
</div>
