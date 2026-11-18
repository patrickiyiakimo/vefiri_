@extends('layouts.storefront')

@section('title', 'Product photo inquiries | Vefiri admin')
@section('description', 'Review product photos sent by customers for damaged deliveries and availability requests.')

@section('content')
    <section class="pt-4 pb-2">
        <x-ui.container>
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-2xs text-muted">
                <a href="{{ route('home') }}" class="transition hover:text-ink">Home</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-ink">Admin</a>
                <x-ui.icon name="chevron-right" class="size-3" />
                <span class="text-ink-soft">Product photos</span>
            </nav>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-ink">Product photo inquiries</h1>
                    <p class="mt-1 text-xs text-muted">
                        {{ number_format($inquiries->total()) }} {{ $inquiries->total() === 1 ? 'submission' : 'submissions' }} from customers, including delivery issues and product requests.
                    </p>
                </div>

                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                    <x-ui.icon name="arrow-left" class="size-3.5" />
                    Back to dashboard
                </a>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-black/[0.04] bg-white shadow-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-left text-xs">
                        <thead class="border-b border-line bg-[#fafafa] text-2xs text-muted uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Photo</th>
                                <th class="px-4 py-3 font-semibold">Customer</th>
                                <th class="px-4 py-3 font-semibold">Reason</th>
                                <th class="px-4 py-3 font-semibold">Order</th>
                                <th class="px-4 py-3 font-semibold">Message</th>
                                <th class="px-4 py-3 font-semibold">Received</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($inquiries as $inquiry)
                                <tr class="hover:bg-[#fafafa]">
                                    <td class="px-4 py-3">
                                        <a href="{{ $inquiry->imageUrl() }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 transition hover:text-brand-700">
                                            <img src="{{ $inquiry->imageUrl() }}" alt="Product photo from {{ $inquiry->customer_name }}" class="size-12 rounded-lg border border-line object-cover">
                                            View photo
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-ink">{{ $inquiry->customer_name }}</p>
                                        <p class="mt-0.5 text-[10px] text-muted">{{ $inquiry->customer_email }}</p>
                                        @if ($inquiry->customer)
                                            <p class="mt-0.5 text-[10px] text-choice-dark">Registered customer</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold {{ $inquiry->type === App\Enums\ProductPhotoInquiryType::DamagedProduct ? 'bg-[#fff1f0] text-[#b42318]' : 'bg-brand-50 text-brand-700' }}">
                                            {{ $inquiry->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-[11px] text-ink-soft">
                                        {{ $inquiry->order_reference ?? 'Not provided' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if (filled($inquiry->message))
                                            <p class="line-clamp-2 max-w-[280px] text-ink-soft" title="{{ $inquiry->message }}">{{ $inquiry->message }}</p>
                                        @else
                                            <span class="text-muted">No message</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $inquiry->created_at?->format('M j, Y g:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-muted">No product photos have been sent yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $inquiries->links() }}</div>
        </x-ui.container>
    </section>
@endsection
