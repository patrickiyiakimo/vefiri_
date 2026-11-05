@props([
    'freeProductLimit' => 10,
    'premiumProductLimit' => null,
    'price' => 0,
])

<div
    data-limit-modal
    role="dialog"
    aria-modal="true"
    aria-labelledby="product-limit-title"
    class="group fixed inset-0 z-[98] hidden"
>
    <div
        data-limit-modal-close
        class="absolute inset-0 bg-black/55 opacity-0 backdrop-blur-[3px] transition-opacity duration-300 group-data-[open=true]:opacity-100"
    ></div>

    <div class="absolute inset-0 flex items-end justify-center p-0 sm:items-center sm:p-5">
        <div class="relative w-full max-w-md translate-y-5 scale-[0.98] overflow-hidden rounded-t-3xl bg-white opacity-0 shadow-2xl transition duration-300 ease-out group-data-[open=true]:translate-y-0 group-data-[open=true]:scale-100 group-data-[open=true]:opacity-100 sm:rounded-3xl">
            <header class="flex items-start justify-between gap-3 border-b border-line px-5 py-4">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-2xl bg-[#fff7e6] text-[#8a5a00]">
                        <x-ui.icon name="lock" class="size-5" />
                    </span>
                    <div>
                        <p class="text-[10px] font-bold tracking-[0.6px] text-[#8a5a00] uppercase">Product limit reached</p>
                        <h2 id="product-limit-title" class="mt-0.5 text-base font-black tracking-tight text-ink">
                            You've used all {{ number_format($freeProductLimit) }} product slots
                        </h2>
                    </div>
                </div>

                <button
                    type="button"
                    data-limit-modal-close
                    aria-label="Close product limit dialog"
                    class="flex size-9 shrink-0 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink"
                >
                    <x-ui.icon name="close" class="size-4" />
                </button>
            </header>

            <div class="px-5 py-5">
                <p class="text-xs leading-5 text-muted">
                    Your Free plan allows up to {{ number_format($freeProductLimit) }} products.
                    Upgrade to Premium to keep uploading without deleting your existing listings.
                </p>

                <ul class="mt-4 grid gap-2 rounded-2xl bg-surface px-4 py-3 text-xs text-ink-soft">
                    <li class="flex items-start gap-2">
                        <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                        <span>
                            {{ $premiumProductLimit === null ? 'Upload unlimited products' : 'Upload up to '.number_format((int) $premiumProductLimit).' products' }}
                        </span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                        <span>Your current products stay live</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-ui.icon name="badge-check" class="mt-0.5 size-3.5 shrink-0 text-choice-dark" />
                        <span>Upgraded automatically after Paystack confirms payment</span>
                    </li>
                </ul>

                <div class="mt-5 grid gap-2">
                    <x-buttons.primary type="submit" form="premium-payment-form" data-limit-modal-primary>
                        <x-ui.icon name="sparkles" class="size-4" />
                        Upgrade for &#8358;{{ number_format($price / 100) }}
                    </x-buttons.primary>

                    <x-buttons.secondary type="button" data-limit-modal-close>
                        Maybe later
                    </x-buttons.secondary>
                </div>

                <p class="mt-3 text-center text-2xs text-muted">
                    Secure payment is handled by Paystack. No manual approval is needed.
                </p>
            </div>
        </div>
    </div>
</div>
