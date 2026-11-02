@props([
    'prefix' => 'login',
    'context' => 'modal',
])

@php
    $emailError = $errors->first('email');
    $passwordError = $errors->first('password');
@endphp

<form
    method="POST"
    action="{{ route('login.store') }}"
    data-auth-form
    class="space-y-4"
>
    @csrf
    <input type="hidden" name="_form" value="login">

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

    <div>
        <label for="{{ $prefix }}-password" class="mb-1.5 block text-xs font-semibold text-ink">Password</label>
        <input
            id="{{ $prefix }}-password"
            type="password"
            name="password"
            autocomplete="current-password"
            required
            aria-invalid="{{ $passwordError ? 'true' : 'false' }}"
            class="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
        >
        <p data-auth-error="password" @class(['mt-1.5 text-xs text-brand-700', 'hidden' => ! $passwordError])>{{ $passwordError }}</p>
    </div>

    <label class="flex cursor-pointer items-center gap-2 text-xs text-ink-soft">
        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-line text-brand-600 focus:ring-brand-500">
        Keep me signed in
    </label>

    <p data-auth-error="general" class="hidden rounded-xl bg-brand-50 px-3 py-2 text-xs text-brand-700"></p>

    <x-buttons.primary type="submit" data-auth-submit class="w-full">
        <span data-auth-submit-label>Sign in</span>
    </x-buttons.primary>

    <p class="text-center text-xs text-muted">
        New to Vefiri?
        @if ($context === 'page')
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 transition hover:text-brand-700">Create an account</a>
        @else
            <button type="button" data-auth-tab="register" class="font-semibold text-brand-600 transition hover:text-brand-700">Create an account</button>
        @endif
    </p>
</form>
