@props([
    'vendor' => null,
])

<div class="flex flex-col gap-3 rounded-2xl border border-black/[0.04] bg-white p-4 shadow-card sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-black text-brand-700">
            {{ str($vendor->store_name)->substr(0, 1)->upper() }}
        </span>
        <div class="min-w-0">
            <p class="truncate text-sm font-black text-ink">{{ $vendor->store_name }}</p>
            <p class="mt-0.5 flex items-center gap-1 text-2xs text-choice-dark">
                <x-ui.icon name="badge-check" class="size-3" />
                Verified vendor
            </p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-1.5">
        <a
            href="{{ route('vendor.dashboard') }}"
            @class([
                'rounded-full px-3 py-1.5 text-2xs font-semibold transition',
                'bg-ink text-white' => request()->routeIs('vendor.dashboard'),
                'bg-surface text-ink-soft hover:text-ink' => ! request()->routeIs('vendor.dashboard'),
            ])
        >
            Overview
        </a>
        <a
            href="{{ route('vendor.products.index') }}"
            @class([
                'rounded-full px-3 py-1.5 text-2xs font-semibold transition',
                'bg-ink text-white' => request()->routeIs('vendor.products.*'),
                'bg-surface text-ink-soft hover:text-ink' => ! request()->routeIs('vendor.products.*'),
            ])
        >
            Products
        </a>
        <a
            href="{{ route('vendor.premium.show') }}"
            @class([
                'rounded-full px-3 py-1.5 text-2xs font-semibold transition',
                'bg-ink text-white' => request()->routeIs('vendor.premium.*'),
                'bg-surface text-ink-soft hover:text-ink' => ! request()->routeIs('vendor.premium.*'),
            ])
        >
            {{ $vendor->isPremium() ? 'Premium' : 'Upgrade' }}
        </a>
        <a href="{{ route('vendors.status') }}" class="rounded-full bg-surface px-3 py-1.5 text-2xs font-semibold text-ink-soft transition hover:text-ink">
            Store profile
        </a>
        <a href="{{ route('home') }}" class="rounded-full bg-surface px-3 py-1.5 text-2xs font-semibold text-ink-soft transition hover:text-ink">
            View storefront
        </a>
    </div>
</div>
