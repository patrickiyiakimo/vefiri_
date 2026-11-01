@props([
    'categories' => [],
])

<section class="pt-3" aria-labelledby="quick-categories-heading">
    <x-ui.container>
        <div class="rounded-2xl border border-black/[0.03] bg-white px-3 py-3 shadow-card sm:px-5">
            <div class="flex items-center justify-between gap-3">
                <h2 id="quick-categories-heading" class="text-sm font-bold text-ink">Top categories</h2>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-2xs font-semibold text-muted transition hover:text-brand-600">
                    View all
                    <x-ui.icon name="chevron-right" class="size-3" />
                </a>
            </div>

            <div class="no-scrollbar mt-2 flex items-start gap-3 overflow-x-auto pb-1 sm:justify-between sm:gap-1">
                @foreach ($categories as $category)
                    <x-cards.quick-category
                        :label="$category['label']"
                        :image="$category['image']"
                        :icon="$category['icon']"
                    />
                @endforeach
            </div>
        </div>
    </x-ui.container>
</section>
