@props([])

<section class="py-4" aria-labelledby="newsletter-heading">
    <x-ui.container>
        <div class="relative overflow-hidden rounded-2xl bg-[#171717] px-5 py-6 text-white shadow-card sm:px-8 sm:py-8 lg:px-10">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -top-24 right-16 size-72 rounded-full bg-brand-600/20 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-10 size-64 rounded-full border-[32px] border-white/5"></div>
                <div class="absolute top-0 right-0 h-full w-1/2 bg-gradient-to-l from-brand-500/10 to-transparent"></div>
            </div>

            <div class="relative grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.85fr)] lg:gap-12">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-2xs font-bold tracking-[0.1em] text-[#ffd7c2] uppercase ring-1 ring-white/10">
                        <x-ui.icon name="mail" class="size-3" />
                        Members get more
                    </span>

                    <h2 id="newsletter-heading" class="mt-3 max-w-xl text-2xl leading-tight font-black tracking-tight sm:text-3xl">
                        Get ₦8,000 off your next order
                    </h2>

                    <p class="mt-2 max-w-lg text-xs leading-5 text-white/60 sm:text-sm sm:leading-6">
                        Join 100 million+ shoppers and be the first to know about new deals, exclusive coupons and product drops.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-2xs text-white/60">
                        <span class="inline-flex items-center gap-1.5">
                            <x-ui.icon name="check" class="size-3 text-[#37d7a5]" />
                            Exclusive coupons
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-ui.icon name="check" class="size-3 text-[#37d7a5]" />
                            Early sale access
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-ui.icon name="check" class="size-3 text-[#37d7a5]" />
                            No spam, ever
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/[0.06] p-4 ring-1 ring-white/10 backdrop-blur-sm sm:p-5">
                    <p class="text-sm font-bold">Unlock your welcome coupon</p>
                    <p class="mt-1 text-2xs leading-5 text-white/55">Enter your email and we will send the code right away.</p>

                    <x-inputs.newsletter class="mt-4" />

                    <p class="mt-3 text-[10px] leading-4 text-white/40">
                        By subscribing, you agree to receive marketing emails from Vefiri. Unsubscribe anytime.
                    </p>
                </div>
            </div>
        </div>
    </x-ui.container>
</section>

