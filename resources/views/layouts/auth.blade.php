<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f04400">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Account | Vefiri')</title>
    <meta name="description" content="@yield('description', 'Sign in or create your Vefiri account to shop quality products with nationwide delivery and buyer protection.')">
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <meta property="og:site_name" content="Vefiri">
    <meta property="og:title" content="@yield('title', 'Account | Vefiri')">
    <meta property="og:description" content="@yield('description', 'Sign in or create your Vefiri account.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-ink">
    <div class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(440px,0.72fr)]">
        <aside class="relative hidden overflow-hidden bg-[#111b3d] p-10 text-white lg:flex lg:flex-col xl:p-14">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -top-32 -right-24 size-[420px] rounded-full border-[64px] border-white/[0.04]"></div>
                <div class="absolute -bottom-40 -left-24 size-[360px] rounded-full bg-brand-500/20 blur-3xl"></div>
                <div class="absolute top-1/3 right-1/4 size-48 rounded-full bg-[#37d7a5]/10 blur-2xl"></div>
            </div>

            <div class="relative">
                <x-ui.logo tone="light" />
            </div>

            <div class="relative my-auto max-w-lg">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-2xs font-bold tracking-[0.12em] text-[#ffd7c2] uppercase ring-1 ring-white/10">
                    <x-ui.icon name="badge-check" class="size-3" />
                    Shop with confidence
                </span>

                <h1 class="mt-4 text-4xl leading-[1.05] font-black tracking-[-0.04em] xl:text-5xl">
                    Everyday finds.<br>
                    Delivered nationwide.
                </h1>

                <p class="mt-4 max-w-md text-sm leading-6 text-white/60">
                    Join millions of shoppers discovering quality products at unbeatable prices, with secure payments and buyer protection on every order.
                </p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['icon' => 'truck', 'title' => 'Fast delivery', 'copy' => 'Tracked shipping across Nigeria'],
                        ['icon' => 'shield', 'title' => 'Buyer protection', 'copy' => 'Shop safely with secure checkout'],
                        ['icon' => 'gift', 'title' => 'Member rewards', 'copy' => 'Coupons and exclusive member deals'],
                        ['icon' => 'rotate', 'title' => 'Easy returns', 'copy' => 'Simple returns within 15 days'],
                    ] as $benefit)
                        <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm">
                            <x-ui.icon :name="$benefit['icon']" class="size-5 text-[#ffb18c]" />
                            <p class="mt-3 text-xs font-bold">{{ $benefit['title'] }}</p>
                            <p class="mt-1 text-2xs leading-4 text-white/50">{{ $benefit['copy'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="relative text-2xs text-white/35">© {{ date('Y') }} Vefiri. All rights reserved.</p>
        </aside>

        <div class="flex min-h-screen flex-col">
            <header class="flex items-center justify-between px-5 py-4 sm:px-8">
                <div class="lg:hidden">
                    <x-ui.logo />
                </div>
                <a
                    href="{{ route('home') }}"
                    class="ml-auto inline-flex items-center gap-1.5 text-xs font-semibold text-muted transition hover:text-ink"
                >
                    <x-ui.icon name="arrow-left" class="size-4" />
                    Back to shop
                </a>
            </header>

            <main class="flex flex-1 items-center justify-center px-5 py-6 sm:px-8">
                <div class="w-full max-w-md">
                    @if (session('status'))
                        <div class="mb-4 rounded-xl bg-[#e7f8f2] px-4 py-3 text-xs font-medium text-choice-dark">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <footer class="px-5 py-5 text-center text-2xs text-muted sm:px-8">
                <span class="inline-flex flex-wrap items-center justify-center gap-x-3 gap-y-1">
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Help Center</a>
                    <span class="text-line">|</span>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Privacy Policy</a>
                    <span class="text-line">|</span>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Terms of Use</a>
                </span>
            </footer>
        </div>
    </div>

    <script src="//code.jivosite.com/widget/vlvNzUXyLG" async></script>
</body>
</html>
