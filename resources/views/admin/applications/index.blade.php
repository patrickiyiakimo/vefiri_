@extends('layouts.storefront')

@section('title', 'Vendor applications | Vefiri admin')
@section('description', 'Review pending Vefiri vendor applications and approve or reject them.')

@php
    $toneClasses = [
        \App\Enums\VendorApplicationStatus::Pending->value => 'bg-[#fff7e6] text-[#8a5a00]',
        \App\Enums\VendorApplicationStatus::Approved->value => 'bg-[#e7f8f2] text-choice-dark',
        \App\Enums\VendorApplicationStatus::Rejected->value => 'bg-brand-50 text-brand-700',
    ];
@endphp

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Vendor applications</span>
            </nav>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-medium text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-xs font-medium text-brand-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Vendor applications</h1>
                    <p class="mt-1 text-xs text-muted">{{ number_format($applications->total()) }} {{ $applications->total() === 1 ? 'application' : 'applications' }} with this status.</p>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($statuses as $statusOption)
                    <a
                        href="{{ route('admin.applications.index', ['status' => $statusOption->value]) }}"
                        @class([
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition',
                            'bg-ink text-white' => $status === $statusOption,
                            'bg-surface text-ink-soft hover:text-ink' => $status !== $statusOption,
                        ])
                    >
                        {{ $statusOption->label() }}
                    </a>
                @endforeach
            </div>

            <div class="mt-4 space-y-3">
                @forelse ($applications as $application)
                    <article class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-sm font-black text-brand-700">
                                    {{ str($application->store_name)->substr(0, 1)->upper() }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-sm font-bold text-ink">{{ $application->store_name }}</h2>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $toneClasses[$application->status->value] }}">
                                            {{ $application->status->label() }}
                                        </span>
                                        @if ($application->applicant?->trashed())
                                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">Deleted account</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 text-2xs text-muted">
                                        {{ $application->business_name }} - {{ $application->applicant?->name ?? 'Deleted account' }} ({{ $application->applicant?->email ?? 'no email' }})
                                    </p>
                                    <p class="mt-2 max-w-2xl text-xs leading-5 text-ink-soft">{{ $application->description }}</p>

                                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-2xs text-muted">
                                        <span class="inline-flex items-center gap-1"><x-ui.icon name="location" class="size-3.5" />{{ $application->address }}, {{ $application->city }}, {{ $application->state }}</span>
                                        <span class="inline-flex items-center gap-1"><x-ui.icon name="mail" class="size-3.5" />{{ $application->business_email }}</span>
                                        <span class="inline-flex items-center gap-1"><x-ui.icon name="user" class="size-3.5" />{{ $application->phone }}</span>
                                        @if ($application->business_registration_number)
                                            <span class="inline-flex items-center gap-1"><x-ui.icon name="shield" class="size-3.5" />RC {{ $application->business_registration_number }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        @foreach ($application->product_categories as $category)
                                            <span class="rounded-full bg-surface px-2 py-0.5 text-[10px] font-medium text-ink-soft">{{ $category }}</span>
                                        @endforeach
                                    </div>

                                    @if ($application->admin_note)
                                        <p class="mt-3 rounded-xl bg-brand-50 px-3 py-2 text-2xs leading-5 text-brand-700">
                                            <span class="font-bold">Admin note:</span> {{ $application->admin_note }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="shrink-0">
                                @if ($application->isPending())
                                    <div class="flex flex-col gap-2 sm:flex-row lg:flex-col">
                                        @if ($application->applicant?->trashed())
                                            <p class="rounded-xl bg-brand-50 px-3 py-2 text-2xs leading-5 text-brand-700 lg:w-48">
                                                Applicant account deleted. Approval is disabled.
                                            </p>
                                        @else
                                            <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                                                @csrf
                                                @method('PATCH')
                                                <x-buttons.primary type="submit" size="sm" class="w-full">
                                                    Approve vendor
                                                </x-buttons.primary>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.applications.reject', $application) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="text"
                                                name="admin_note"
                                                placeholder="Reason (optional)"
                                                class="h-8 w-full rounded-full border border-line bg-white px-3 text-2xs outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 lg:w-48"
                                            >
                                            <x-buttons.secondary type="submit" size="sm" class="w-full">
                                                Reject
                                            </x-buttons.secondary>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-right text-2xs text-muted">
                                        <p>Reviewed {{ $application->reviewed_at?->format('M j, Y') }}</p>
                                        @if ($application->reviewer)
                                            <p class="mt-0.5">by {{ $application->reviewer->name }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-black/[0.03] bg-white px-6 py-16 text-center shadow-card">
                        <span class="mx-auto flex size-16 items-center justify-center rounded-full bg-surface text-muted">
                            <x-ui.icon name="badge-check" class="size-7" />
                        </span>
                        <h2 class="mt-4 text-lg font-bold text-ink">No applications here</h2>
                        <p class="mx-auto mt-1 max-w-md text-xs leading-5 text-muted">There are no vendor applications with this status right now.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-5">{{ $applications->links() }}</div>
        </x-ui.container>
    </section>
@endsection
