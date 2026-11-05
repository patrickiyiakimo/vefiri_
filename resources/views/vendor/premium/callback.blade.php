@extends('layouts.storefront')

@section('title', 'Premium payment status | Vefiri vendor')
@section('description', 'Check the status of your Vefiri Premium upgrade payment.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.dashboard') }}" class="transition hover:text-ink">Vendor dashboard</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Premium payment</span>
            </nav>

            <div class="mx-auto mt-6 max-w-xl rounded-2xl border border-black/[0.04] bg-white p-6 shadow-card sm:p-8">
                @if ($payment === null)
                    <h1 class="text-lg font-black text-ink">We could not find this payment</h1>
                    <p class="mt-2 text-xs leading-5 text-muted">
                        The payment reference does not belong to your store. If you were charged, contact support with your reference.
                    </p>
                @elseif ($payment->isPaid())
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-[#e7f8f2] text-choice-dark">
                        <x-ui.icon name="badge-check" class="size-6" />
                    </span>
                    <h1 class="mt-4 text-lg font-black text-ink">Premium is active</h1>
                    <p class="mt-2 text-xs leading-5 text-muted">
                        Your payment was confirmed and your account has been upgraded automatically.
                    </p>
                    <dl class="mt-4 grid gap-2 rounded-xl bg-surface px-4 py-3 text-xs">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-muted">Reference</dt>
                            <dd class="font-semibold text-ink">{{ $payment->reference }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-muted">Paid on</dt>
                            <dd class="font-semibold text-ink">{{ $payment->paid_at?->toFormattedDateString() }}</dd>
                        </div>
                        @if ($vendor->premium_until !== null)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Premium until</dt>
                                <dd class="font-semibold text-ink">{{ $vendor->premium_until->toFormattedDateString() }}</dd>
                            </div>
                        @endif
                    </dl>
                @elseif ($payment->status === \App\Enums\VendorPremiumPaymentStatus::Failed)
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                        <x-ui.icon name="close" class="size-6" />
                    </span>
                    <h1 class="mt-4 text-lg font-black text-ink">Payment was not completed</h1>
                    <p class="mt-2 text-xs leading-5 text-muted">
                        Paystack did not complete this payment, so your account was not upgraded. You can try the upgrade again.
                    </p>
                    <p class="mt-4 rounded-xl bg-surface px-4 py-3 text-xs text-ink-soft">
                        Reference: <span class="font-semibold text-ink">{{ $reference }}</span>
                    </p>
                @else
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-[#fff7e6] text-[#8a5a00]">
                        <x-ui.icon name="clock" class="size-6" />
                    </span>
                    <h1 class="mt-4 text-lg font-black text-ink">Payment is being confirmed</h1>
                    <p class="mt-2 text-xs leading-5 text-muted">
                        Paystack is still confirming this payment. Refresh this page in a few seconds, or you can safely leave and your account will be upgraded automatically once the payment is verified.
                    </p>
                    <p class="mt-4 rounded-xl bg-surface px-4 py-3 text-xs text-ink-soft">
                        Reference: <span class="font-semibold text-ink">{{ $reference }}</span>
                    </p>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <x-buttons.primary href="{{ route('vendor.dashboard') }}">Back to dashboard</x-buttons.primary>
                    @if ($payment !== null && ! $payment->isPaid())
                        <x-buttons.secondary href="{{ route('vendor.premium.callback', ['reference' => $reference]) }}">Refresh status</x-buttons.secondary>
                    @endif
                    <x-buttons.secondary href="{{ route('vendor.premium.show') }}">Premium plan</x-buttons.secondary>
                </div>
            </div>
        </x-ui.container>
    </section>
@endsection
