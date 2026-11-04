@extends('layouts.storefront')

@section('title', 'Verified vendors | Vefiri admin')
@section('description', 'View all verified Vefiri vendors.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Verified vendors</span>
            </nav>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-medium text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Verified vendors</h1>
                    <p class="mt-1 text-xs text-muted">{{ number_format($vendors->total()) }} {{ $vendors->total() === 1 ? 'vendor' : 'vendors' }} approved to sell on Vefiri.</p>
                </div>

                <form method="GET" action="{{ route('admin.vendors.index') }}" class="flex flex-wrap gap-2">
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search store, business or email"
                        class="h-9 w-full rounded-xl border border-line bg-white px-3 text-xs outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 sm:w-64"
                    >
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl bg-ink px-4 text-xs font-semibold text-white transition hover:bg-ink-soft">Search</button>
                </form>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1040px] text-left text-xs">
                        <thead class="border-b border-line bg-[#fafafa] text-2xs text-muted uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Store</th>
                                <th class="px-4 py-3 font-semibold">Business</th>
                                <th class="px-4 py-3 font-semibold">Contact</th>
                                <th class="px-4 py-3 font-semibold">Location</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Approved</th>
                                <th class="px-4 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($vendors as $vendor)
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
                                    <td class="px-4 py-3 text-ink-soft">{{ $vendor->business_name }}</td>
                                    <td class="px-4 py-3">
                                        <p class="text-ink-soft">{{ $vendor->business_email }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ $vendor->phone }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $vendor->city }}, {{ $vendor->state }}</td>
                                    <td class="px-4 py-3">
                                        @if ($vendor->user?->isSuspended())
                                            <span class="rounded-full bg-[#fff1f0] px-2 py-0.5 text-[10px] font-bold text-[#b42318]">Suspended</span>
                                        @else
                                            <span class="rounded-full bg-[#e7f8f2] px-2 py-0.5 text-[10px] font-bold text-choice-dark">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $vendor->created_at?->format('M j, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @if ($vendor->user === null || $vendor->user->isAdmin() || $vendor->user->is(auth()->user()))
                                                <span class="text-[10px] font-semibold text-muted">Protected</span>
                                            @else
                                                @if ($vendor->user->isSuspended())
                                                    <form method="POST" action="{{ route('admin.vendors.reactivate', $vendor) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg border border-[#b7ebd2] bg-[#e7f8f2] px-2 py-1 text-[10px] font-semibold text-choice-dark transition hover:bg-[#d9f4e9]">
                                                            Reactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.vendors.suspend', $vendor) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg border border-[#fed7aa] bg-[#fff7ed] px-2 py-1 text-[10px] font-semibold text-[#9a3412] transition hover:bg-[#ffedd5]">
                                                            Suspend
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}" onsubmit="return confirm('Delete this vendor account? Their order history will be retained.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg border border-[#fecaca] bg-[#fef2f2] px-2 py-1 text-[10px] font-semibold text-[#b91c1c] transition hover:bg-[#fee2e2]">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-muted">No verified vendors match your search.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $vendors->links() }}</div>
        </x-ui.container>
    </section>
@endsection
