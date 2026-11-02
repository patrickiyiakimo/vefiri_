@props([
    'prefix' => 'register',
    'context' => 'modal',
])

@php
    $nameError = $errors->first('name');
    $emailError = $errors->first('email');
    $passwordError = $errors->first('password');
@endphp

<form
    method="POST"
    action="{{ route('register.store') }}"
    data-auth-form
    class="space-y-4"
>
    @csrf
    <input type="hidden" name="_form" value="register">

    <div>
        <label for="{{ $prefix }}-name" class="mb-1.5 block text-xs font-semibold text-ink">Full name</label>
        <input
            id="{{ $prefix }}-name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            autocomplete="name"
            required
            aria-invalid="{{ $nameError ? 'true' : 'false' }}"
            class="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
        >
        <p data-auth-error="name" @class(['mt-1.5 text-xs text-brand-700', 'hidden' => ! $nameError])>{{ $nameError }}</p>
    </div>

    <div>
        <label for="{{ $prefix }}-email" class="mb-1.5 block text-xs font-semibold text-ink">Email address</label>
        <input
            id="{{ $prefix }}-email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            autocomplete="email"
            required
            aria-invalid="{{ $emailError ? 'true' : 'false' }}"
            class="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
        >
        <p data-auth-error="email" @class(['mt-1.5 text-xs text-brand-700', 'hidden' => ! $emailError])>{{ $emailError }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="{{ $prefix }}-password" class="mb-1.5 block text-xs font-semibold text-ink">Password</label>
            <input
                id="{{ $prefix }}-password"
                type="password"
                name="password"
                autocomplete="new-password"
                required
                aria-invalid="{{ $passwordError ? 'true' : 'false' }}"
                class="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
            >
        </div>

        <div>
            <label for="{{ $prefix }}-password-confirmation" class="mb-1.5 block text-xs font-semibold text-ink">Confirm password</label>
            <input
                id="{{ $prefix }}-password-confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                required
                class="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
            >
        </div>
    </div>

    <p data-auth-error="password" @class(['text-xs text-brand-700', 'hidden' => ! $passwordError])>{{ $passwordError }}</p>

    <label class="flex cursor-pointer items-start gap-2 text-2xs leading-4 text-muted">
        <input type="checkbox" required class="mt-0.5 size-4 shrink-0 rounded border-line text-brand-600 focus:ring-brand-500">
        <span>
            I agree to Vefiri's
            <a href="{{ route('home') }}" class="font-semibold text-brand-600 hover:text-brand-700">Terms of Use</a>
            and
            <a href="{{ route('home') }}" class="font-semibold text-brand-600 hover:text-brand-700">Privacy Policy</a>.
        </span>
    </label>

    <p data-auth-error="general" class="hidden rounded-xl bg-brand-50 px-3 py-2 text-xs text-brand-700"></p>

    <x-buttons.primary type="submit" data-auth-submit class="w-full">
        <span data-auth-submit-label>Create account</span>
    </x-buttons.primary>

    <p class="text-center text-xs text-muted">
        Already have an account?
        @if ($context === 'page')
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 transition hover:text-brand-700">Sign in</a>
        @else
            <button type="button" data-auth-tab="login" class="font-semibold text-brand-600 transition hover:text-brand-700">Sign in</button>
        @endif
    </p>
</form>
