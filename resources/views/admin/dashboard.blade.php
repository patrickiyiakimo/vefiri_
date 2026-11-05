@extends('layouts.storefront')

@section('title', 'Admin dashboard | Vefiri')
@section('description', 'Manage orders, users, vendors and vendor applications from the Vefiri admin dashboard.')

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
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Orders</a>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Users</a>
                    <a href="{{ route('admin.vendors.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Vendors</a>
                    <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center gap-1.5 rounded-full bg-surface px-3 py-1.5 text-xs font-semibold text-ink-soft transition hover:text-ink">Vendor applications</a>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Total users', 'value' => $stats['users'], 'icon' => 'user'],
                    ['label' => 'Verified vendors', 'value' => $stats['vendors'], 'icon' => 'store'],
                    ['label' => 'Premium vendors', 'value' => $stats['premiumVendors'], 'icon' => 'sparkles'],
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
                                    <div class="flex items-center gap-2">
                                        <p class="truncate text-xs font-semibold text-ink">{{ $application->store_name }}</p>
                                        @if ($application->applicant?->trashed())
                                            <span class="shrink-0 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-700">Deleted account</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 truncate text-2xs text-muted">{{ $application->applicant?->email ?? 'Deleted account' }} - {{ $application->business_name }}</p>
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

            <section class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <header class="flex items-center justify-between border-b border-line px-5 py-4">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Premium vendors</h2>
                        <p class="mt-0.5 text-2xs text-muted">
                            {{ number_format($stats['premiumVendors']) }}
                            {{ $stats['premiumVendors'] === 1 ? 'vendor has' : 'vendors have' }}
                            unlimited product uploads.
                        </p>
                    </div>
                    <a href="{{ route('admin.vendors.index') }}" class="text-2xs font-semibold text-brand-600">View all vendors</a>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[960px] text-left text-xs">
                        <thead class="border-b border-line bg-[#fafafa] text-2xs text-muted uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Store</th>
                                <th class="px-4 py-3 font-semibold">Contact</th>
                                <th class="px-4 py-3 font-semibold">Products</th>
                                <th class="px-4 py-3 font-semibold">Premium started</th>
                                <th class="px-4 py-3 font-semibold">Premium until</th>
                                <th class="px-4 py-3 font-semibold">Latest payment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($premiumVendors as $vendor)
                                @php($payment = $vendor->premiumPayments->first())
                                <tr class="hover:bg-[#fafafa]">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-[10px] font-black text-brand-700">
                                                {{ str($vendor->store_name)->substr(0, 1)->upper() }}
                                            </span>
                                            <div>
                                                <p class="font-semibold text-ink">{{ $vendor->store_name }}</p>
                                                <p class="mt-0.5 text-[10px] text-muted">{{ $vendor->user?->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-ink-soft">{{ $vendor->business_email }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ $vendor->phone }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-[#e7f8f2] px-2 py-0.5 text-[10px] font-bold text-choice-dark">Unlimited</span>
                                        <p class="mt-1 text-[10px] text-muted">{{ number_format($vendor->products_count) }} uploaded</p>
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $vendor->premium_started_at?->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-muted">{{ $vendor->premium_until?->format('M j, Y') ?? 'Lifetime' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($payment !== null)
                                            <p class="font-semibold text-ink">{{ $payment->currency }} {{ number_format($payment->amount / 100) }}</p>
                                            <p class="mt-0.5 text-[10px] text-muted">
                                                {{ $payment->paid_at?->format('M j, Y') ?? '—' }} &middot; {{ $payment->reference }}
                                            </p>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-muted">No vendors have upgraded to Premium yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </x-ui.container>
    </section>
@endsection
