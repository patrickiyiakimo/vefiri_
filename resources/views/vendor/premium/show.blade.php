@extends('layouts.storefront')

@section('title', 'Product slots | Vefiri vendor')
@section('description', 'Buy extra product slots to list more products on your Vefiri storefront.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendor.dashboard') }}" class="transition hover:text-ink">Vendor dashboard</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Product slots</span>
            </nav>

            <div class="mt-3">
                <h1 class="text-2xl font-black tracking-tight text-ink">Product slots</h1>
                <p class="mt-1 text-xs text-muted">Buy extra product slots once and keep them forever.</p>
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
                        <h2 class="text-sm font-bold text-ink">Your product slots</h2>
                        <p class="mt-0.5 text-2xs text-muted">Slots are added automatically once Paystack confirms your payment.</p>
                    </header>

                    <div class="grid gap-4 px-5 py-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-2xs text-muted">Total product limit</p>
                                <p class="mt-1 text-lg font-black text-ink">{{ number_format($productLimit) }} products</p>
                            </div>
                            <span @class([
                                'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                'bg-[#e7f8f2] text-choice-dark' => $productSlotCount > 0,
                                'bg-surface text-muted' => $productSlotCount === 0,
                            ])>
                                {{ $productSlotCount > 0 ? number_format($productSlotCount).' extra slots' : 'Free tier' }}
                            </span>
                        </div>

                        <dl class="grid gap-2 text-xs">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Free product limit</dt>
                                <dd class="font-semibold text-ink">{{ number_format($freeProductLimit) }} products</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Slots purchased</dt>
                                <dd class="font-semibold text-ink">{{ number_format($productSlotCount) }} products</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-muted">Products uploaded</dt>
                                <dd class="font-semibold text-ink">{{ number_format($productCount) }} of {{ number_format($productLimit) }}</dd>
                            </div>
                        </dl>

                        <div class="h-2 overflow-hidden rounded-full bg-surface">
                            <div
                                class="h-full rounded-full bg-choice"
                                style="width: {{ $productLimit > 0 ? min(100, ($productCount / $productLimit) * 100) : 0 }}%"
                            ></div>
                        </div>

                        @if ($vendor->hasReachedProductLimit())
                            <p class="rounded-xl bg-[#fff7e6] px-4 py-3 text-2xs font-semibold text-[#8a5a00]">
                                You have used all {{ number_format($productLimit) }} of your product slots. Buy more slots to keep uploading.
                            </p>
                        @else
                            <p class="rounded-xl bg-surface px-4 py-3 text-2xs text-ink-soft">
                                You can upload {{ number_format($vendor->remainingProductSlots()) }} more {{ $vendor->remainingProductSlots() === 1 ? 'product' : 'products' }} on your current limit.
                            </p>
                        @endif
                    </div>
                </section>

                <section id="product-slots-payment" class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Buy product slots</h2>
                        <p class="mt-0.5 text-2xs text-muted">
                            &#8358;{{ number_format($unitPrice / 100) }} per extra product. Secure payment is handled by Paystack.
                        </p>
                    </header>

                    <div class="grid gap-4 px-5 py-5">
                        <form
                            id="premium-payment-form"
                            method="POST"
                            action="{{ route('vendor.premium.store') }}"
                            class="grid gap-4"
                            data-product-slots-form
                            data-unit-price="{{ $unitPrice }}"
                            data-currency-symbol="&#8358;"
                        >
                            @csrf
                            <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

                            <div class="grid gap-2">
                                <p class="text-2xs font-semibold text-ink-soft">Choose how many extra products you need</p>

                                @foreach ($bundles as $quantity)
                                    <label class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-line px-4 py-3 transition hover:border-choice has-[:checked]:border-choice has-[:checked]:bg-[#e7f8f2]">
                                        <span class="flex items-center gap-3">
                                            <input
                                                type="radio"
                                                name="quantity"
                                                value="{{ $quantity }}"
                                                @checked($loop->first)
                                                class="size-4 accent-choice"
                                            >
                                            <span class="text-xs font-semibold text-ink">{{ number_format($quantity) }} more products</span>
                                        </span>
                                        <span class="text-xs font-black text-ink">&#8358;{{ number_format(($unitPrice * $quantity) / 100) }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between gap-3 rounded-xl bg-surface px-4 py-3">
                                <span class="text-2xs text-muted">You pay</span>
                                <span class="text-xl font-black tracking-tight text-ink" data-product-slots-amount>
                                    &#8358;{{ number_format(($unitPrice * ($bundles[0] ?? 0)) / 100) }}
                                </span>
                            </div>

                            <x-buttons.primary type="submit" data-product-slots-submit>
                                <x-ui.icon name="lock" class="size-4" />
                                Pay &#8358;<span data-product-slots-submit-amount>{{ number_format(($unitPrice * ($bundles[0] ?? 0)) / 100) }}</span> with Paystack
                            </x-buttons.primary>
                        </form>

                        @error('premium')
                            <p class="text-2xs text-brand-700">{{ $message }}</p>
                        @enderror
                        @error('quantity')
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
                    :product-limit="$productLimit"
                    :bundles="$bundles"
                    :unit-price="$unitPrice"
                />
            @endif
        </x-ui.container>
    </section>
@endsection
