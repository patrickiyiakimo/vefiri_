@extends('layouts.storefront')

@section('title', 'My account | Vefiri')
@section('description', 'View your Vefiri profile, wishlist, cart and vendor account details.')

@php
    $inputClasses = 'h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10';
@endphp

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">My account</span>
            </nav>

            <div class="mt-3 rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card sm:p-7">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-4">
                        <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                            <x-ui.icon name="user" class="size-6" />
                        </span>
                        <div class="min-w-0">
                            <h1 class="truncate text-xl font-black tracking-tight text-ink sm:text-2xl">{{ $user->name }}</h1>
                            <p class="mt-1 truncate text-xs text-muted">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-surface px-2.5 py-1 text-[10px] font-bold text-ink-soft">
                                    {{ $user->role->label() }}
                                </span>
                                <span class="text-2xs text-muted">Member since {{ $user->created_at?->format('F Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <x-buttons.secondary href="{{ route('home') }}" size="sm">Continue shopping</x-buttons.secondary>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-8 items-center justify-center rounded-full px-3.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => 'Wishlist items', 'value' => number_format($user->favourites_count), 'href' => route('favourites.index'), 'icon' => 'heart'],
                        ['label' => 'Items in cart', 'value' => number_format($cartQuantity), 'href' => route('cart.index'), 'icon' => 'cart'],
                        ['label' => 'Account type', 'value' => $user->role->label(), 'href' => null, 'icon' => 'shield'],
                        ['label' => 'Member since', 'value' => $user->created_at?->format('M j, Y'), 'href' => null, 'icon' => 'clock'],
                    ] as $stat)
                        @if ($stat['href'])
                            <a href="{{ $stat['href'] }}" class="rounded-xl bg-surface p-4 transition hover:bg-[#f0f0f0]">
                        @else
                            <div class="rounded-xl bg-surface p-4">
                        @endif
                            <div class="flex items-center justify-between">
                                <p class="text-2xs text-muted">{{ $stat['label'] }}</p>
                                <x-ui.icon :name="$stat['icon']" class="size-4 text-ink-soft" />
                            </div>
                            <p class="mt-2 truncate text-sm font-bold text-ink">{{ $stat['value'] }}</p>
                        @if ($stat['href'])
                            </a>
                        @else
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-semibold text-choice-dark">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mt-4 grid items-start gap-4 lg:grid-cols-2">
                <div class="space-y-4">
                    <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                        <header class="border-b border-line px-5 py-4">
                            <h2 class="text-sm font-bold text-ink">Account details</h2>
                            <p class="mt-0.5 text-2xs text-muted">The information linked to your Vefiri profile.</p>
                        </header>

                        <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-4 px-5 py-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="name" class="mb-1.5 block text-xs font-semibold text-ink">Full name</label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="{{ $inputClasses }}">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1.5 block text-xs font-semibold text-ink">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" class="{{ $inputClasses }}">
                                @error('email')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="divide-y divide-line rounded-xl bg-surface px-4">
                                @foreach ([
                                    ['label' => 'Account type', 'value' => $user->role->label()],
                                    ['label' => 'Email status', 'value' => $user->email_verified_at ? 'Verified' : 'Not verified'],
                                ] as $detail)
                                    <div class="flex items-center justify-between gap-3 py-3">
                                        <span class="text-2xs text-muted">{{ $detail['label'] }}</span>
                                        <span class="truncate text-xs font-semibold text-ink">{{ $detail['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <x-buttons.primary type="submit" size="sm">Save changes</x-buttons.primary>
                        </form>
                    </section>

                    <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                        <header class="border-b border-line px-5 py-4">
                            <h2 class="text-sm font-bold text-ink">Password</h2>
                            <p class="mt-0.5 text-2xs text-muted">Update the password used to sign in to your account.</p>
                        </header>

                        <form method="POST" action="{{ route('account.password.update') }}" class="space-y-4 px-5 py-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="current_password" class="mb-1.5 block text-xs font-semibold text-ink">Current password</label>
                                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="{{ $inputClasses }}">
                                @error('current_password')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-1.5 block text-xs font-semibold text-ink">New password</label>
                                <input id="password" name="password" type="password" required autocomplete="new-password" class="{{ $inputClasses }}">
                                <p class="mt-1.5 text-[10px] text-muted">Use at least 8 characters.</p>
                                @error('password')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold text-ink">Confirm new password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $inputClasses }}">
                            </div>

                            <x-buttons.primary type="submit" size="sm">Update password</x-buttons.primary>
                        </form>
                    </section>
                </div>

                <section class="rounded-2xl border border-black/[0.04] bg-white shadow-card">
                    <header class="border-b border-line px-5 py-4">
                        <h2 class="text-sm font-bold text-ink">Quick actions</h2>
                        <p class="mt-0.5 text-2xs text-muted">Jump back into shopping or manage your seller account.</p>
                    </header>

                    <div class="grid gap-2 px-5 py-4">
                        <a href="{{ route('favourites.index') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                            <x-ui.icon name="heart" class="size-4 text-brand-600" />
                            <span class="flex-1 text-xs font-semibold text-ink">View my wishlist</span>
                            <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                        </a>

                        <a href="{{ route('cart.index') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                            <x-ui.icon name="cart" class="size-4 text-brand-600" />
                            <span class="flex-1 text-xs font-semibold text-ink">Open my cart</span>
                            <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                        </a>

                        @if ($vendor)
                            <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                                <x-ui.icon name="store" class="size-4 text-brand-600" />
                                <span class="flex-1 text-xs font-semibold text-ink">Vendor dashboard</span>
                                <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                            </a>
                        @elseif ($application)
                            <a href="{{ route('vendors.status') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                                <x-ui.icon name="clock" class="size-4 text-brand-600" />
                                <span class="flex-1 text-xs font-semibold text-ink">Vendor application: {{ $application->status->label() }}</span>
                                <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                            </a>
                        @else
                            <a href="{{ route('vendors.apply') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                                <x-ui.icon name="store" class="size-4 text-brand-600" />
                                <span class="flex-1 text-xs font-semibold text-ink">Become a verified vendor</span>
                                <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                            </a>
                        @endif

                        @if ($user->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl bg-surface px-4 py-3 transition hover:bg-[#f0f0f0]">
                                <x-ui.icon name="shield" class="size-4 text-brand-600" />
                                <span class="flex-1 text-xs font-semibold text-ink">Admin dashboard</span>
                                <x-ui.icon name="chevron-right" class="size-3.5 text-muted" />
                            </a>
                        @endif
                    </div>
                </section>
            </div>
        </x-ui.container>
    </section>
@endsection
