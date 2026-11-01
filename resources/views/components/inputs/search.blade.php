@props([
    'categories' => [],
    'suggestions' => [],
    'value' => '',
])

<form
    action="{{ route('home') }}"
    method="GET"
    role="search"
    data-search
    {{ $attributes->merge(['class' => 'relative w-full']) }}
>
    <div class="flex h-12 w-full items-stretch overflow-hidden rounded-full border-2 border-ink bg-white shadow-sm transition focus-within:border-brand-500 focus-within:ring-4 focus-within:ring-brand-500/10">
        <label for="search-category" class="sr-only">Search category</label>
        <select
            id="search-category"
            name="category"
            class="hidden h-full max-w-[152px] shrink-0 cursor-pointer border-r border-line bg-white py-0 pr-7 pl-4 text-xs font-medium text-ink-soft outline-none md:block"
        >
            @foreach ($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>

        <div class="flex min-w-0 flex-1 items-center gap-2 pl-3 sm:pl-4">
            <x-ui.icon name="search" class="size-4 shrink-0 text-muted" />
            <label for="site-search" class="sr-only">Search AliExpress</label>
            <input
                id="site-search"
                type="search"
                name="q"
                value="{{ $value }}"
                autocomplete="off"
                placeholder="Search for products, brands and more"
                data-search-input
                class="h-full min-w-0 flex-1 bg-transparent text-sm text-ink outline-none [&::-webkit-search-cancel-button]:hidden placeholder:text-[#929292]"
            >
            <button
                type="button"
                aria-label="Search by image"
                data-toast="Image search is not available in this demo."
                class="flex size-9 shrink-0 items-center justify-center rounded-full text-ink-soft transition hover:bg-surface hover:text-brand-600"
            >
                <x-ui.icon name="camera" class="size-5" />
            </button>
        </div>

        <button
            type="submit"
            class="m-1 ml-0 flex shrink-0 items-center justify-center gap-1.5 rounded-full bg-gradient-to-r from-brand-600 to-[#ff8a00] px-4 text-sm font-bold text-white transition hover:brightness-105 active:scale-[0.98] sm:px-6"
        >
            <x-ui.icon name="search" class="size-4 sm:hidden" />
            <span class="hidden sm:inline">Search</span>
            <span class="sr-only sm:hidden">Search</span>
        </button>
    </div>

    <div
        data-search-panel
        class="absolute inset-x-0 top-[calc(100%+8px)] z-50 hidden overflow-hidden rounded-2xl border border-line bg-white p-4 shadow-float"
    >
        <div class="flex items-center justify-between gap-3">
            <p class="flex items-center gap-1.5 text-xs font-semibold text-ink">
                <x-ui.icon name="flame" class="size-3.5 text-brand-600" />
                Trending searches
            </p>
            <span class="text-2xs text-muted">Updated today</span>
        </div>

        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($suggestions as $suggestion)
                <button
                    type="button"
                    data-search-suggestion="{{ $suggestion }}"
                    class="rounded-full bg-surface px-3 py-1.5 text-xs text-ink-soft transition hover:bg-brand-50 hover:text-brand-700"
                >
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>
</form>
