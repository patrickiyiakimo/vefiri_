@props([
    'columns' => [],
    'paymentMethods' => [],
])

<footer class="mt-4 border-t border-line bg-white">
    <div class="border-b border-line bg-[#fbfbfb] py-5">
        <x-ui.container class="grid grid-cols-2 gap-5 lg:grid-cols-4">
            <x-ui.trust-item
                icon="truck"
                title="Free shipping"
                description="On millions of Choice items"
            />
            <x-ui.trust-item
                icon="shield"
                title="Buyer protection"
                description="Money back if you are not satisfied"
            />
            <x-ui.trust-item
                icon="rotate"
                title="Easy returns"
                description="Return within 15 days of delivery"
            />
            <x-ui.trust-item
                icon="headset"
                title="24/7 support"
                description="We are here whenever you need us"
            />
        </x-ui.container>
    </div>

    <x-ui.container class="py-8">
        <div class="grid gap-8 lg:grid-cols-[1.15fr_repeat(4,1fr)]">
            <div>
                <x-ui.logo />
                <p class="mt-3 max-w-[270px] text-xs leading-5 text-muted">
                    Shop millions of products at unbeatable prices, with fast delivery and buyer protection on every order.
                </p>

                <div class="mt-4 flex items-center gap-2">
                    @foreach ([['instagram', 'Instagram'], ['facebook', 'Facebook'], ['x', 'X'], ['youtube', 'YouTube'], ['tiktok', 'TikTok']] as [$icon, $label])
                        <a
                            href="{{ route('home') }}"
                            aria-label="{{ $label }}"
                            class="flex size-8 items-center justify-center rounded-full bg-surface text-ink-soft transition hover:bg-ink hover:text-white"
                        >
                            <x-ui.icon :name="$icon" class="size-3.5" />
                        </a>
                    @endforeach
                </div>
            </div>

            @foreach ($columns as $column)
                <nav aria-label="{{ $column['title'] }}">
                    <h2 class="text-xs font-bold text-ink">{{ $column['title'] }}</h2>
                    <ul class="mt-3 space-y-2">
                        @foreach ($column['links'] as $link)
                            <li>
                                <a
                                    href="{{ $link === 'Become a verified vendor' ? route('vendors.index') : route('home') }}"
                                    class="text-2xs leading-5 text-muted transition hover:text-brand-600"
                                >
                                    {{ $link }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach
        </div>
    </x-ui.container>

    <div class="border-t border-line bg-[#fafafa]">
        <x-ui.container class="flex flex-col gap-5 py-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="mr-1 text-2xs font-semibold text-ink-soft">Pay with</span>
                    @foreach ($paymentMethods as $method)
                        <span class="rounded-md border border-line bg-white px-2 py-1 text-[9px] font-bold text-ink-soft">
                            {{ $method }}
                        </span>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 text-2xs text-muted">
                    <x-ui.icon name="globe" class="size-3.5" />
                    <span>English</span>
                    <span class="text-line">|</span>
                    <span>NGN</span>
                    <span class="text-line">|</span>
                    <span>Nigeria</span>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-line pt-4 text-[10px] leading-5 text-muted lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap gap-x-3 gap-y-1">
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Intellectual Property Protection</a>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Privacy Policy</a>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Terms of Use</a>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Sitemap</a>
                    <a href="{{ route('home') }}" class="transition hover:text-ink">Cookie Preferences</a>
                </div>
                <p>© {{ date('Y') }} Vefiri. All rights reserved.</p>
            </div>
        </div>
    </x-ui.container>
</div>
</footer>
