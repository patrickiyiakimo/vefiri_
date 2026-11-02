@extends('layouts.storefront')

@section('title', 'Verified vendors | Vefiri')
@section('description', 'Discover trusted, verified Vefiri vendors and apply to open your own verified storefront.')

@section('content')
    <section class="pt-4">
        <x-ui.container>
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-[#111b3d] via-[#1b2a5c] to-[#2f478f] px-6 py-10 text-white sm:px-10 sm:py-12">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-2xs font-bold tracking-[0.12em] uppercase ring-1 ring-white/10">
                    <x-ui.icon name="badge-check" class="size-3.5 text-[#7ef0c8]" />
                    Vefiri verified vendors
                </span>

                <div class="mt-4 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <h1 class="text-3xl leading-tight font-black tracking-tight sm:text-4xl">
                            Shop from sellers we have verified
                        </h1>
                        <p class="mt-3 text-sm leading-6 text-white/70">
                            Every verified vendor is reviewed by our team before going live. Build trust with buyers,
                            sell to customers nationwide and grow with Vefiri.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-buttons.primary href="{{ route('vendors.apply') }}" size="lg">
                            Become a vendor
                            <x-ui.icon name="arrow-right" class="size-4" />
                        </x-buttons.primary>
                        <a
                            href="{{ route('vendors.apply') }}"
                            class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-white/30 px-6 text-sm font-semibold text-white transition hover:bg-white/10"
                        >
                            View requirements
                        </a>
                    </div>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    @foreach ([
                        ['icon' => 'shield', 'title' => 'Verified businesses', 'copy' => 'Identity and business details reviewed by our team.'],
                        ['icon' => 'store', 'title' => 'Storefront ready', 'copy' => 'A dedicated vendor profile buyers can trust.'],
                        ['icon' => 'truck', 'title' => 'Nationwide reach', 'copy' => 'Sell to customers across Nigeria.'],
                    ] as $benefit)
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4">
                            <x-ui.icon :name="$benefit['icon']" class="size-5 text-[#ffb18c]" />
                            <p class="mt-3 text-xs font-bold">{{ $benefit['title'] }}</p>
                            <p class="mt-1 text-2xs leading-5 text-white/55">{{ $benefit['copy'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-ui.container>
    </section>

    <section class="pt-5 pb-2">
        <x-ui.container>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-ink">Meet our verified vendors</h2>
                    <p class="mt-1 text-xs text-muted">
                        {{ number_format($vendors->total()) }} {{ $vendors->total() === 1 ? 'vendor' : 'vendors' }} approved by Vefiri.
                    </p>
                </div>

                <a href="{{ route('vendors.apply') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    Apply to become a vendor
                    <x-ui.icon name="arrow-right" class="size-3.5" />
                </a>
            </div>

            @if ($vendors->isEmpty())
                <div class="mt-4 rounded-2xl border border-black/[0.03] bg-white px-6 py-16 text-center shadow-card">
                    <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-surface text-muted">
                        <x-ui.icon name="store" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-lg font-bold text-ink">No verified vendors yet</h3>
                    <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-muted">
                        Be the first vendor to join the Vefiri marketplace and start selling to customers nationwide.
                    </p>
                    <x-buttons.primary href="{{ route('vendors.apply') }}" class="mt-5">
                        Start your application
                    </x-buttons.primary>
                </div>
            @else
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($vendors as $vendor)
                        <article class="flex h-full flex-col rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                            <div class="flex items-start gap-3">
                                <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-base font-black text-brand-700">
                                    {{ str($vendor->store_name)->substr(0, 1)->upper() }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="truncate text-sm font-bold text-ink">{{ $vendor->store_name }}</h3>
                                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-[#e7f8f2] px-2 py-0.5 text-[10px] font-bold text-choice-dark">
                                            <x-ui.icon name="badge-check" class="size-3" />
                                            Verified
                                        </span>
                                    </div>
                                    <p class="mt-0.5 truncate text-2xs text-muted">{{ $vendor->business_name }}</p>
                                </div>
                            </div>

                            <p class="mt-3 line-clamp-3 text-xs leading-5 text-ink-soft">
                                {{ $vendor->description ?: 'A verified Vefiri vendor offering quality products with reliable nationwide delivery.' }}
                            </p>

                            <div class="mt-auto flex items-center gap-3 pt-4 text-2xs text-muted">
                                <span class="inline-flex items-center gap-1">
                                    <x-ui.icon name="location" class="size-3.5" />
                                    {{ $vendor->city }}, {{ $vendor->state }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <x-ui.icon name="clock" class="size-3.5" />
                                    Joined {{ $vendor->created_at?->format('M Y') }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $vendors->links() }}
                </div>
            @endif
        </x-ui.container>
    </section>
@endsection
