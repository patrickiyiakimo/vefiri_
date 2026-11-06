@props([
    'id',
    'name',
    'autocomplete' => 'current-password',
    'required' => true,
    'invalid' => false,
    'class' => 'h-11 w-full rounded-xl border border-line bg-white px-3.5 pr-11 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10',
])

<div class="relative" data-password-field>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="password"
        autocomplete="{{ $autocomplete }}"
        {{ $required ? 'required' : '' }}
        aria-invalid="{{ $invalid ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $class]) }}
    >

    <button
        type="button"
        data-password-toggle
        aria-controls="{{ $id }}"
        aria-label="Show password"
        aria-pressed="false"
        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-muted transition hover:text-ink focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40"
    >
        <x-ui.icon name="eye" class="size-4" data-password-icon-show />
        <x-ui.icon name="eye-off" class="hidden size-4" data-password-icon-hide />
    </button>
</div>
