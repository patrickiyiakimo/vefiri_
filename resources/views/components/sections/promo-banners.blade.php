@props([
    'banners' => [],
])

<section class="pt-4" aria-labelledby="promo-heading">
    <x-ui.container>
        <x-ui.section-heading
            id="promo-heading"
            title="More ways to save"
            subtitle="Coupons, member benefits and business pricing in one place."
            class="mb-3 px-1"
        />

        <div class="grid gap-3 md:grid-cols-3">
            @foreach ($banners as $banner)
                <x-cards.promo :banner="$banner" />
            @endforeach
        </div>
    </x-ui.container>
</section>
