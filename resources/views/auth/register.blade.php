@extends('layouts.auth')

@section('title', $isVendor ? 'Become a verified vendor | Vefiri' : 'Create your account | Vefiri')
@section('description', $isVendor ? 'Create your Vefiri account to start your verified vendor application.' : 'Create your Vefiri account to shop quality products with nationwide delivery.')

@section('content')
    <div class="rounded-3xl border border-black/[0.04] bg-white p-6 shadow-card sm:p-8">
        <span class="flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
            <x-ui.icon :name="$isVendor ? 'store' : 'sparkles'" class="size-5" />
        </span>

        <h1 class="mt-5 text-2xl font-black tracking-tight text-ink">
            {{ $isVendor ? 'Become a verified vendor' : 'Create your account' }}
        </h1>
        <p class="mt-1.5 text-xs leading-5 text-muted">
            {{ $isVendor
                ? 'Start with a Vefiri account, then complete your vendor verification and store setup.'
                : 'Join Vefiri to unlock exclusive coupons, faster checkout and buyer protection.' }}
        </p>

        @if ($isVendor)
            <div class="mt-4 flex items-start gap-2 rounded-xl bg-[#e7f8f2] px-3 py-2.5 text-2xs leading-5 text-choice-dark">
                <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0" />
                Verified vendors get a storefront, seller dashboard, payout support and a verified badge.
            </div>
        @endif

        <div class="mt-6">
            <x-auth.register-form prefix="page-register" context="page" />
        </div>
    </div>
@endsection
