@extends('layouts.storefront')

@section('title', 'Vendor application status | Vefiri')
@section('description', 'Track the status of your Vefiri vendor application.')

@php
    $statusClasses = $application === null
        ? ''
        : match ($application->status) {
            \App\Enums\VendorApplicationStatus::Pending => 'bg-[#fff7e6] text-[#8a5a00]',
            \App\Enums\VendorApplicationStatus::Approved => 'bg-[#e7f8f2] text-choice-dark',
            \App\Enums\VendorApplicationStatus::Rejected => 'bg-brand-50 text-brand-700',
        };
@endphp

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendors.index') }}" class="transition hover:text-ink">Verified vendors</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Application status</span>
            </nav>

            <div class="mt-3 rounded-2xl border border-black/[0.04] bg-white p-6 shadow-card sm:p-8">
                @if ($vendor)
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-base font-black text-brand-700">
                                {{ str($vendor->store_name)->substr(0, 1)->upper() }}
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h1 class="text-xl font-black tracking-tight text-ink">{{ $vendor->store_name }}</h1>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f8f2] px-2 py-0.5 text-[10px] font-bold text-choice-dark">
                                        <x-ui.icon name="badge-check" class="size-3" />
                                        Verified vendor
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-muted">{{ $vendor->business_name }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <x-buttons.primary href="{{ route('vendor.dashboard') }}" size="sm">
                                <x-ui.icon name="store" class="size-3.5" />
                                Vendor dashboard
                            </x-buttons.primary>
                            <a href="{{ route('vendors.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                                View verified vendors
                                <x-ui.icon name="arrow-right" class="size-3.5" />
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            ['label' => 'Business email', 'value' => $vendor->business_email],
                            ['label' => 'Phone', 'value' => $vendor->phone],
                            ['label' => 'Location', 'value' => $vendor->city.', '.$vendor->state],
                            ['label' => 'Approved', 'value' => $vendor->created_at?->format('M j, Y')],
                        ] as $detail)
                            <div class="rounded-xl bg-surface p-4">
                                <p class="text-2xs text-muted">{{ $detail['label'] }}</p>
                                <p class="mt-1 truncate text-xs font-semibold text-ink">{{ $detail['value'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 rounded-xl border border-line p-4">
                        <p class="text-2xs font-semibold text-muted">About your store</p>
                        <p class="mt-1 text-xs leading-5 text-ink-soft">{{ $vendor->description ?: 'No store description provided.' }}</p>
                    </div>
                @elseif ($application)
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h1 class="text-xl font-black tracking-tight text-ink">Vendor application</h1>
                            <p class="mt-1 text-xs text-muted">Submitted {{ $application->created_at?->format('M j, Y \a\t g:ia') }}</p>
                        </div>
                        <span class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusClasses }}">
                            {{ $application->status->label() }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach ([
                            ['label' => 'Store name', 'value' => $application->store_name],
                            ['label' => 'Business name', 'value' => $application->business_name],
                            ['label' => 'Business email', 'value' => $application->business_email],
                            ['label' => 'Phone', 'value' => $application->phone],
                            ['label' => 'Address', 'value' => $application->address.', '.$application->city.', '.$application->state],
                            ['label' => 'Registration number', 'value' => $application->business_registration_number ?: 'Not provided'],
                        ] as $detail)
                            <div class="rounded-xl bg-surface p-4">
                                <p class="text-2xs text-muted">{{ $detail['label'] }}</p>
                                <p class="mt-1 text-xs font-semibold text-ink">{{ $detail['value'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    @if ($application->admin_note)
                        <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-xs text-brand-700">
                            <p class="font-bold">Note from our team</p>
                            <p class="mt-1 leading-5">{{ $application->admin_note }}</p>
                        </div>
                    @endif

                    @if ($application->status === \App\Enums\VendorApplicationStatus::Rejected)
                        <div class="mt-5">
                            <x-buttons.primary href="{{ route('vendors.apply') }}">
                                Submit a new application
                                <x-ui.icon name="arrow-right" class="size-4" />
                            </x-buttons.primary>
                        </div>
                    @endif
                @else
                    <span class="flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-ui.icon name="store" class="size-5" />
                    </span>
                    <h1 class="mt-4 text-xl font-black tracking-tight text-ink">No vendor application yet</h1>
                    <p class="mt-1.5 max-w-lg text-xs leading-5 text-muted">
                        Submit your business details to start your verified vendor application. You can track the review
                        status here at any time.
                    </p>
                    <x-buttons.primary href="{{ route('vendors.apply') }}" class="mt-5">
                        Become a vendor
                        <x-ui.icon name="arrow-right" class="size-4" />
                    </x-buttons.primary>
                @endif
            </div>
        </x-ui.container>
    </section>
@endsection
