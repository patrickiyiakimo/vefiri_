@extends('layouts.storefront')

@section('title', 'Users | Vefiri admin')
@section('description', 'View and filter all Vefiri users.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Users</span>
            </nav>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-medium text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">All users</h1>
                    <p class="mt-1 text-xs text-muted">{{ number_format($users->total()) }} {{ $users->total() === 1 ? 'user' : 'users' }} registered on Vefiri.</p>
                </div>

                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
                    <input
                        type="search"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search name or email"
                        class="h-9 w-full rounded-xl border border-line bg-white px-3 text-xs outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 sm:w-56"
                    >
                    <select name="role" class="h-9 rounded-xl border border-line bg-white px-3 text-xs outline-none transition focus:border-brand-500">
                        <option value="">All roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->value }}" @selected($role === $roleOption)>{{ $roleOption->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl bg-ink px-4 text-xs font-semibold text-white transition hover:bg-ink-soft">Filter</button>
                </form>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-xs">
                        <thead class="border-b border-line bg-[#fafafa] text-2xs text-muted uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Role</th>
                                <th class="px-4 py-3 font-semibold">Email</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Joined</th>
                                <th class="px-4 py-3 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($users as $user)
                                <tr class="hover:bg-[#fafafa]">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                                                <x-ui.icon name="user" class="size-4" />
                                            </span>
                                            <span class="font-semibold text-ink">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'rounded-full px-2 py-0.5 text-[10px] font-bold',
                                            'bg-brand-50 text-brand-700' => $user->isAdmin(),
                                            'bg-[#e7f8f2] text-choice-dark' => $user->isVendor(),
                                            'bg-surface text-ink-soft' => ! $user->isAdmin() && ! $user->isVendor(),
                                        ])>
                                            {{ $user->role->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-ink-soft">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        @if ($user->isSuspended())
                                            <span class="rounded-full bg-[#fff1f0] px-2 py-0.5 text-[10px] font-bold text-[#b42318]">Suspended</span>
                                        @else
                                            <span class="rounded-full bg-[#e7f8f2] px-2 py-0.5 text-[10px] font-bold text-choice-dark">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $user->created_at?->format('M j, Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @if ($user->isAdmin() || $user->is(auth()->user()))
                                                <span class="text-[10px] font-semibold text-muted">Protected</span>
                                            @else
                                                @if ($user->isSuspended())
                                                    <form method="POST" action="{{ route('admin.users.reactivate', $user) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg border border-[#b7ebd2] bg-[#e7f8f2] px-2 py-1 text-[10px] font-semibold text-choice-dark transition hover:bg-[#d9f4e9]">
                                                            Reactivate
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="rounded-lg border border-[#fed7aa] bg-[#fff7ed] px-2 py-1 text-[10px] font-semibold text-[#9a3412] transition hover:bg-[#ffedd5]">
                                                            Suspend
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this account? Their order history will be retained.')">
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
                                    <td colspan="6" class="px-4 py-10 text-center text-muted">No users match your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $users->links() }}</div>
        </x-ui.container>
    </section>
@endsection
