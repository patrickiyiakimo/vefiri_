@props([
    'placeholder' => 'Enter your email address',
    'value' => '',
])

<form
    data-newsletter-form
    {{ $attributes->merge(['class' => 'flex w-full flex-col gap-2 sm:flex-row']) }}
>
    <label for="newsletter-email" class="sr-only">Email address</label>
    <div class="relative min-w-0 flex-1">
        <x-ui.icon name="mail" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-muted" />
        <input
            id="newsletter-email"
            type="email"
            name="email"
            value="{{ $value }}"
            required
            autocomplete="email"
            placeholder="{{ $placeholder }}"
            class="h-11 w-full rounded-full border border-line bg-white pr-4 pl-11 text-sm text-ink outline-none transition placeholder:text-muted focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
        >
    </div>

    <x-buttons.primary type="submit" class="h-11 px-6">
        Get my coupon
        <x-ui.icon name="arrow-right" class="size-4" />
    </x-buttons.primary>
</form>
