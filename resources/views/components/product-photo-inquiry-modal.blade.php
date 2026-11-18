@php
    $customer = auth()->user();
    $types = App\Enums\ProductPhotoInquiryType::cases();
    $selectedType = old('type', App\Enums\ProductPhotoInquiryType::DamagedProduct->value);
@endphp

<div
    id="product-photo-inquiry-modal"
    data-photo-inquiry-modal
    class="group fixed inset-0 z-[96] hidden"
>
    <div
        data-photo-inquiry-close
        class="absolute inset-0 bg-black/55 opacity-0 backdrop-blur-[3px] transition-opacity duration-300 group-data-[open=true]:opacity-100"
    ></div>

    <div class="absolute inset-0 flex items-end justify-center p-0 sm:items-center sm:p-5">
        <div
            role="dialog"
            aria-modal="true"
            aria-labelledby="photo-inquiry-title"
            class="relative max-h-[94vh] w-full max-w-lg translate-y-5 scale-[0.98] overflow-y-auto rounded-t-3xl bg-white p-5 opacity-0 shadow-2xl transition duration-300 ease-out group-data-[open=true]:translate-y-0 group-data-[open=true]:scale-100 group-data-[open=true]:opacity-100 sm:rounded-3xl sm:p-6"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                        <x-ui.icon name="camera" class="size-5" />
                    </span>
                    <div>
                        <h2 id="photo-inquiry-title" class="text-lg font-black tracking-tight text-ink">Send a product photo</h2>
                        <p class="mt-0.5 text-xs text-muted">Show Vefiri what you received or what you are looking for.</p>
                    </div>
                </div>
                <button
                    type="button"
                    data-photo-inquiry-close
                    aria-label="Close product photo dialog"
                    class="flex size-9 shrink-0 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink"
                >
                    <x-ui.icon name="close" class="size-5" />
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('product-photo-inquiries.store') }}"
                enctype="multipart/form-data"
                data-photo-inquiry-form
                class="mt-5 space-y-3"
            >
                @csrf

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="photo-inquiry-name" class="text-xs font-semibold text-ink">Your name</label>
                        <input
                            id="photo-inquiry-name"
                            type="text"
                            name="customer_name"
                            value="{{ old('customer_name', $customer?->name) }}"
                            autocomplete="name"
                            maxlength="120"
                            required
                            class="mt-1 h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                        >
                        <p data-photo-inquiry-error="customer_name" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                    </div>

                    <div>
                        <label for="photo-inquiry-email" class="text-xs font-semibold text-ink">Email address</label>
                        <input
                            id="photo-inquiry-email"
                            type="email"
                            name="customer_email"
                            value="{{ old('customer_email', $customer?->email) }}"
                            autocomplete="email"
                            maxlength="255"
                            required
                            class="mt-1 h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                        >
                        <p data-photo-inquiry-error="customer_email" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                    </div>
                </div>

                <div>
                    <label for="photo-inquiry-type" class="text-xs font-semibold text-ink">What should Vefiri check?</label>
                    <select
                        id="photo-inquiry-type"
                        name="type"
                        required
                        class="mt-1 h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                    >
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($selectedType === $type->value)>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    <p data-photo-inquiry-error="type" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                </div>

                <div>
                    <label for="photo-inquiry-order" class="text-xs font-semibold text-ink">Order reference <span class="font-normal text-muted">(optional)</span></label>
                    <input
                        id="photo-inquiry-order"
                        type="text"
                        name="order_reference"
                        value="{{ old('order_reference') }}"
                        placeholder="e.g. ORD-123456"
                        maxlength="100"
                        class="mt-1 h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                    >
                    <p data-photo-inquiry-error="order_reference" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                </div>

                <div>
                    <label for="photo-inquiry-image" class="text-xs font-semibold text-ink">Product photo</label>
                    <input
                        id="photo-inquiry-image"
                        type="file"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        required
                        class="mt-1 w-full rounded-xl border border-line bg-white px-3 py-2 text-xs text-ink outline-none transition file:mr-3 file:rounded-full file:bg-brand-50 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-brand-700 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                    >
                    <p class="mt-1 text-[11px] text-muted">JPG, PNG or WebP, up to 4 MB.</p>
                    <p data-photo-inquiry-error="image" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                </div>

                <div>
                    <label for="photo-inquiry-message" class="text-xs font-semibold text-ink">Message <span class="font-normal text-muted">(optional)</span></label>
                    <textarea
                        id="photo-inquiry-message"
                        name="message"
                        rows="3"
                        maxlength="1000"
                        placeholder="Tell us what happened or when you would like this product to be available."
                        class="mt-1 w-full resize-none rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10"
                    >{{ old('message') }}</textarea>
                    <p data-photo-inquiry-error="message" class="mt-1 hidden text-[11px] text-[#b42318]"></p>
                </div>

                <p data-photo-inquiry-error="general" class="hidden text-[11px] text-[#b42318]"></p>

                <div class="flex flex-col gap-2 pt-1 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-[11px] leading-4 text-muted">We will reply by email after reviewing your photo.</p>
                    <x-buttons.primary type="submit" data-photo-inquiry-submit>Send to Vefiri</x-buttons.primary>
                </div>
            </form>
        </div>
    </div>
</div>
