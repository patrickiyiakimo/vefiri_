@extends('layouts.storefront')

@section('title', 'Premium plan | Vefiri vendor')
@section('description', 'Upgrade your Vefiri vendor account to Premium and upload more products.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.dashboard') }}" class="transition hover:text-ink">Vendor dashboard</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Premium plan</span>
            </nav>

            <div class="mt-3">
                <h1 class="text-2xl font-black tracking-tight text-ink">Premium plan</h1>
                <p class="mt-1 text-xs text-muted">Unlock a bigger catalogue and keep growing your storefront.</p>
            </div>

            <div class="mt-4">
                <x-vendor.nav :vendor="$vendor" />
            </div>

            @if (session('status'))
                <div @class([
                    'mt-3 rounded-xl px-4 py-3 text-xs font-semibold',
                    'bg-[#fff7e6] text-[#8a5a00]' => session('limit_reached'),
                    'bg-[#e7f8f2] text-choice-dark' => ! session('limit_reached'),
                ])>
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Your plan</h2>
                        <p class="mt-0.5 text-2xs text-muted">Entitlements update automatically once Paystack confirms your payment.</p>
                    </header>

                    <div class="grid gap-4 px-5 py-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-2xs text-muted">Current plan</p>
                                <p class="mt-1 text-lg font-black text-ink">{{ $vendor->currentPlan()->label() }}</p>
                            </div>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                'bg-[#e7f8f2] text-choice-dark' => $vendor->isPremium(),
                                'bg-surface text-muted' => ! $vendor->isPremium(),
                            ])>
                                {{ $vendor->isPremium() ? 'Active' : 'Free tier' }}
                            </span>
                        </div>

                        <dl class="grid gap-2 text-xs">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Free product limit</dt>
                                <dd class="font-semibold text-ink">{{ number_format($freeProductLimit) }} products</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Premium product limit</dt>
                                <dd class="font-semibold text-ink">
                                    {{ $premiumProductLimit === null ? 'Unlimited' : number_format((int) $premiumProductLimit).' products' }}
                                </dd>
                            </div>
                            @if ($vendor->premium_until !== null)
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-muted">Premium paid until</dt>
                                    <dd class="font-semibold text-ink">{{ $vendor->premium_until->toFormattedDateString() }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if ($vendor->hasReachedProductLimit())
                            <p class="rounded-xl bg-[#fff7e6] px-4 py-3 text-2xs font-semibold text-[#8a5a00]">
                                You have reached your current product limit. Upgrade to keep uploading.
                            </p>
                        @endif
                    </div>
                </section>

                <section id="premium-payment" class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">{{ $vendor->isPremium() ? 'Extend Premium' : 'Upgrade to Premium' }}</h2>
                        <p class="mt-0.5 text-2xs text-muted">Secure payment is handled by Paystack.</p>
                    </header>

                    <div class="grid gap-4 px-5 py-5">
                        <div>
                            <p class="text-3xl font-black tracking-tight text-ink">
                                &#8358;{{ number_format($price / 100) }}
                            </p>
                            <p class="mt-1 text-2xs text-muted">
                                @if ($durationDays > 0)
                                    {{ $durationDays }} days of Premium access. Paying again extends your current expiry date.
                                @else
                                    One-time payment for lifetime Premium access.
                                @endif
                            </p>
                        </div>

                        <ul class="grid gap-2 text-xs text-ink-soft">
                            <li class="flex items-start gap-2">
                                <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                                <span>{{ $premiumProductLimit === null ? 'Upload unlimited products' : 'Upload up to '.number_format((int) $premiumProductLimit).' products' }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                                <span>Your account is upgraded automatically after Paystack confirms payment</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                                <span>No manual approval needed</span>
                            </li>
                        </ul>

                        <form id="premium-payment-form" method="POST" action="{{ route('vendor.premium.store') }}" class="grid gap-2">
                            @csrf
                            <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

                            <x-buttons.primary type="submit">
                                <x-ui.icon name="lock" class="size-4" />
                                Pay &#8358;{{ number_format($price / 100) }} with Paystack
                            </x-buttons.primary>
                        </form>

                        @error('premium')
                            <p class="text-2xs text-brand-700">{{ $message }}</p>
                        @enderror
                        @error('idempotency_key')
                            <p class="text-2xs text-brand-700">{{ $message }}</p>
                        @enderror

                        @if ($latestPayment !== null)
                            <p class="text-2xs text-muted">
                                Latest payment: {{ $latestPayment->status->label() }}
                                @if ($latestPayment->paid_at !== null)
                                    on {{ $latestPayment->paid_at->toFormattedDateString() }}
                                @endif
                            </p>
                        @endif
                    </div>
                </section>
            </div>

            @if (session('limit_reached'))
                <x-vendor.product-limit-modal
                    :free-product-limit="$freeProductLimit"
                    :premium-product-limit="$premiumProductLimit"
                    :price="$price"
                />
            @endif
        </x-ui.container>
    </section>
@endsection
