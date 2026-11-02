@extends('layouts.storefront')

@section('title', 'Become a verified vendor | Vefiri')
@section('description', 'Submit your business details to become a verified Vefiri vendor and open your storefront.')

@php
    $inputClasses = 'h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10';
    $oldCategories = old('product_categories', $application?->product_categories ?? []);
@endphp

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('vendors.index') }}" class="transition hover:text-ink">Verified vendors</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Apply</span>
            </nav>

            <div class="mt-3 grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
                <div class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card sm:p-7">
                    <span class="flex size-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <x-ui.icon name="store" class="size-5" />
                    </span>

                    <h1 class="mt-4 text-2xl font-black tracking-tight text-ink">Become a verified vendor</h1>
                    <p class="mt-1.5 max-w-2xl text-xs leading-5 text-muted">
                        Tell us about your business. Our team reviews every application and will approve your storefront
                        once your details meet our vendor requirements.
                    </p>

                    @if ($application && $application->status === \App\Enums\VendorApplicationStatus::Rejected)
                        <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-xs text-brand-700">
                            <p class="font-bold">Your previous application was not approved.</p>
                            @if ($application->admin_note)
                                <p class="mt-1 leading-5">{{ $application->admin_note }}</p>
                            @endif
                            <p class="mt-1 leading-5">You can update your details below and submit a new application.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vendors.apply.store') }}" class="mt-6 space-y-5">
                        @csrf

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="business_name" class="mb-1.5 block text-xs font-semibold text-ink">Registered business name</label>
                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $application?->business_name) }}" required class="{{ $inputClasses }}">
                                @error('business_name')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="store_name" class="mb-1.5 block text-xs font-semibold text-ink">Store name</label>
                                <input id="store_name" name="store_name" type="text" value="{{ old('store_name', $application?->store_name) }}" required class="{{ $inputClasses }}">
                                @error('store_name')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="business_email" class="mb-1.5 block text-xs font-semibold text-ink">Business email</label>
                                <input id="business_email" name="business_email" type="email" value="{{ old('business_email', $application?->business_email ?? auth()->user()->email) }}" required class="{{ $inputClasses }}">
                                @error('business_email')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="mb-1.5 block text-xs font-semibold text-ink">Phone number</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone', $application?->phone) }}" required class="{{ $inputClasses }}">
                                @error('phone')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="business_registration_number" class="mb-1.5 block text-xs font-semibold text-ink">
                                    Business registration number <span class="font-normal text-muted">(optional)</span>
                                </label>
                                <input id="business_registration_number" name="business_registration_number" type="text" value="{{ old('business_registration_number', $application?->business_registration_number) }}" class="{{ $inputClasses }}">
                                @error('business_registration_number')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label for="address" class="mb-1.5 block text-xs font-semibold text-ink">Business address</label>
                                <input id="address" name="address" type="text" value="{{ old('address', $application?->address) }}" required class="{{ $inputClasses }}">
                                @error('address')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="mb-1.5 block text-xs font-semibold text-ink">City</label>
                                <input id="city" name="city" type="text" value="{{ old('city', $application?->city) }}" required class="{{ $inputClasses }}">
                                @error('city')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="mb-1.5 block text-xs font-semibold text-ink">State</label>
                                <input id="state" name="state" type="text" value="{{ old('state', $application?->state) }}" required class="{{ $inputClasses }}">
                                @error('state')
                                    <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-xs font-semibold text-ink">What do you sell?</label>
                            <textarea id="description" name="description" rows="4" required class="w-full rounded-xl border border-line bg-white px-3.5 py-3 text-sm text-ink outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10">{{ old('description', $application?->description) }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <fieldset>
                            <legend class="mb-2 text-xs font-semibold text-ink">Product categories</legend>
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($categories as $category)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line px-3 py-2.5 text-xs text-ink-soft transition hover:border-brand-300 hover:bg-brand-50/40">
                                        <input
                                            type="checkbox"
                                            name="product_categories[]"
                                            value="{{ $category }}"
                                            @checked(in_array($category, $oldCategories, true))
                                            class="size-4 rounded border-line text-brand-600 focus:ring-brand-500"
                                        >
                                        {{ $category }}
                                    </label>
                                @endforeach
                            </div>
                            @error('product_categories')
                                <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                            @enderror
                            @error('product_categories.*')
                                <p class="mt-1.5 text-xs text-brand-700">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <label class="flex cursor-pointer items-start gap-2 text-2xs leading-4 text-muted">
                            <input type="checkbox" required class="mt-0.5 size-4 shrink-0 rounded border-line text-brand-600 focus:ring-brand-500">
                            <span>I confirm the information provided is accurate and I agree to the Vefiri vendor terms.</span>
                        </label>

                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <x-buttons.primary type="submit">
                                Submit application
                                <x-ui.icon name="arrow-right" class="size-4" />
                            </x-buttons.primary>
                            <a href="{{ route('vendors.index') }}" class="text-xs font-semibold text-muted transition hover:text-ink">Cancel</a>
                        </div>
                    </form>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-2xl border border-black/[0.04] bg-white p-5 shadow-card">
                        <h2 class="text-sm font-bold text-ink">Vendor requirements</h2>
                        <ul class="mt-3 space-y-3">
                            @foreach ([
                                'A registered business name and contact details.',
                                'A physical business address in Nigeria.',
                                'Clear details about the products you sell.',
                                'Accurate information that matches your business documents.',
                                'Agreement to Vefiri seller and buyer protection policies.',
                            ] as $requirement)
                                <li class="flex items-start gap-2 text-xs leading-5 text-ink-soft">
                                    <x-ui.icon name="check" class="mt-0.5 size-4 shrink-0 text-choice-dark" />
                                    {{ $requirement }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-2xl bg-[#e7f8f2] p-5">
                        <span class="flex size-9 items-center justify-center rounded-full bg-white text-choice-dark">
                            <x-ui.icon name="clock" class="size-4" />
                        </span>
                        <h2 class="mt-3 text-sm font-bold text-ink">What happens next?</h2>
                        <p class="mt-1 text-xs leading-5 text-ink-soft">
                            Applications are reviewed by our admin team. You will see the approval status in your vendor
                            dashboard, and your store goes live as soon as you are approved.
                        </p>
                    </div>
                </aside>
            </div>
        </x-ui.container>
    </section>
@endsection
