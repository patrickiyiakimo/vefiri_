<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">

    <title>@yield('title', 'AliExpress - Affordable Online Shopping')</title>
    <meta name="description" content="@yield('description', 'Shop millions of products at affordable prices with free shipping on Choice items, buyer protection and easy returns.')">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-ink">
    <a
        href="#main-content"
        class="sr-only z-[120] rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3"
    >
        Skip to main content
    </a>

    <x-navigation.top-bar />
    <x-navigation.header
        :search-categories="$searchCategories"
        :search-suggestions="$searchSuggestions"
        :search-value="request('q', '')"
    />
    <x-navigation.category-nav
        :nav-links="$navLinks"
        :menu-categories="$menuCategories"
    />

    <main id="main-content">
        @yield('content')
    </main>

    <x-navigation.footer
        :columns="$footerColumns"
        :payment-methods="$paymentMethods"
    />

    <x-navigation.mobile-menu :menu-categories="$menuCategories" />
    <x-cart.drawer />

    <div
        data-toast-container
        role="status"
        aria-live="polite"
        aria-atomic="false"
        class="pointer-events-none fixed inset-x-4 bottom-4 z-[100] flex flex-col items-end gap-2 sm:left-auto sm:w-96"
    ></div>

    <button
        type="button"
        data-back-to-top
        aria-label="Back to top"
        class="fixed right-4 bottom-4 z-30 hidden size-10 items-center justify-center rounded-full border border-line bg-white text-ink shadow-float transition hover:-translate-y-0.5 hover:text-brand-600 sm:size-11"
    >
        <x-ui.icon name="chevron-up" class="size-5" />
    </button>
</body>
</html>
