@extends('layouts.storefront')

@section('title', 'Admin dashboard | Vefiri')
@section('description', 'Manage users, vendors and vendor applications from the Vefiri admin dashboard.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Admin</span>
            </nav>

            <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Admin dashboard</h1>
                    <p class="mt-1 text-xs text-muted">Overview of users, verified vendors and pending applications.</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Users</a>
                    <a href="{{ route('admin.vendors.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Vendors</a>
                    <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Vendor applications</a>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Total users', 'value' => $stats['users'], 'icon' => 'user'],
                    ['label' => 'Verified vendors', 'value' => $stats['vendors'], 'icon' => 'store'],
                    ['label' => 'Pending applications', 'value' => $stats['pendingApplications'], 'icon' => 'clock'],
                    ['label' => 'Approved applications', 'value' => $stats['approvedApplications'], 'icon' => 'badge-check'],
                ] as $stat)
                    <article class="rounded-2xl border border-black/[0.04] bg-white p-4 shadow-card">
                        <div class="flex items-center justify-between">
                            <p class="text-2xs text-muted">{{ $stat['label'] }}</p>
                            <span class="flex size-8 items-center justify-center rounded-full bg-surface text-ink-soft">
                                <x-ui.icon :name="$stat['icon']" class="size-4" />
                            </span>
                        </div>
                        <p class="mt-2 text-2xl font-black tracking-tight text-ink">{{ number_format($stat['value']) }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="flex items-center justify-between border-b border-line px-5 py-4">
                        <div>
                            <h2 class="text-sm font-bold text-ink">Recent vendor applications</h2>
                            <p class="mt-0.5 text-2xs text-muted">Latest submissions awaiting review.</p>
                        </div>
                        <a href="{{ route('admin.applications.index') }}" class="text-2xs font-semibold text-brand-600">View all</a>
                    </header>
                    <div class="divide-y divide-line">
                        @forelse ($recentApplications as $application)
                            <article class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-semibold text-ink">{{ $application->store_name }}</p>
                                    <p class="mt-0.5 truncate text-2xs text-muted">{{ $application->user->email }} - {{ $application->business_name }}</p>
                                </div>
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold bg-[#fff7e6] text-[#8a5a00]">
                                    {{ $application->status->label() }}
                                </span>
                            </article>
                        @empty
                            <p class="px-5 py-6 text-center text-xs text-muted">No vendor applications yet.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="flex items-center justify-between border-b border-line px-5 py-4">
                        <div>
                            <h2 class="text-sm font-bold text-ink">Recent users</h2>
                            <p class="mt-0.5 text-2xs text-muted">Newest registered accounts.</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="text-2xs font-semibold text-brand-600">View all</a>
                    </header>
                    <div class="divide-y divide-line">
                        @forelse ($recentUsers as $user)
                            <article class="flex items-center justify-between gap-3 px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-9 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                                        <x-ui.icon name="user" class="size-4" />
                                    </span>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-semibold text-ink">{{ $user->name }}</p>
                                        <p class="mt-0.5 truncate text-2xs text-muted">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-surface px-2 py-0.5 text-[10px] font-bold text-ink-soft">
                                    {{ $user->role->label() }}
                                </span>
                            </article>
                        @empty
                            <p class="px-5 py-6 text-center text-xs text-muted">No users yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </x-ui.container>
    </section>
@endsection
