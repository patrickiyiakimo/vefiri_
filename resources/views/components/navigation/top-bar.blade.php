<div class="border-b border-line bg-[#fafafa]">
    <x-ui.container class="flex h-8 items-center justify-between text-2xs text-muted">
        <div class="flex min-w-0 items-center gap-4">
            <a href="{{ route('home') }}" class="hidden items-center gap-1.5 transition hover:text-ink sm:flex">
                <x-ui.icon name="truck" class="size-3.5 text-choice-dark" />
                Free shipping on all Choice items
            </a>

            <a href="{{ route('home') }}" class="flex items-center gap-1.5 transition hover:text-ink">
                <x-ui.icon name="location" class="size-3.5" />
                <span class="truncate">Ship to: United States</span>
                <x-ui.icon name="chevron-down" class="size-3" />
            </a>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">
            <a href="{{ route('home') }}" class="hidden transition hover:text-ink md:inline">AliExpress Business</a>
            <a href="{{ route('home') }}" class="hidden transition hover:text-ink sm:inline">Help Center</a>
            <a href="{{ route('home') }}" class="hidden items-center gap-1 transition hover:text-ink lg:flex">
                <x-ui.icon name="download" class="size-3.5" />
                Download the app
            </a>
            <button
                type="button"
                data-toast="Language and currency settings are not available in this demo."
                class="flex items-center gap-1 transition hover:text-ink"
            >
                <x-ui.icon name="globe" class="size-3.5" />
                English / USD
                <x-ui.icon name="chevron-down" class="size-3" />
            </button>
        </div>
    </x-ui.container>
</div>
