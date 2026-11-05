@extends('emails.layouts.brand')

@section('title', 'Your Vefiri order has been delivered')
@section('preheader', 'Your Vefiri order has been marked as delivered.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        Your order has been delivered
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Hi {{ $order->customer_name }}, your Vefiri order
        <strong style="color:#1c1c1c;">{{ $order->payment_reference }}</strong>
        has been marked as delivered.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#fafafa; border:1px solid #eeeeee; border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#767676;">Delivery confirmation</p>
                <p style="margin:7px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Order reference:</strong> {{ $order->payment_reference }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Delivered on:</strong> {{ $order->delivered_at?->format('M j, Y \a\t g:i A') }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Order total:</strong> &#8358;{{ number_format($order->total / 100) }}</p>
            </td>
        </tr>
    </table>

    <h2 style="margin:26px 0 0; font-size:16px; line-height:23px; font-weight:700; color:#1c1c1c;">Delivered to</h2>

    <p style="margin:9px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        <strong style="color:#1c1c1c;">{{ $order->customer_name }}</strong><br>
        {{ $order->delivery_address }}@if ($order->delivery_address_line_2), {{ $order->delivery_address_line_2 }}@endif<br>
        {{ $order->delivery_city }}, {{ $order->delivery_state }}@if ($order->delivery_postal_code) {{ $order->delivery_postal_code }}@endif
        @if ($order->customer_phone)
            <br>{{ $order->customer_phone }}
        @endif
    </p>

    <h2 style="margin:26px 0 0; font-size:16px; line-height:23px; font-weight:700; color:#1c1c1c;">Items delivered</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:10px; border:1px solid #eeeeee; border-radius:12px; overflow:hidden;">
        @forelse ($order->items as $item)
            <tr>
                <td style="padding:13px 15px; border-bottom:1px solid #eeeeee; font-size:13px; line-height:20px; color:#1c1c1c;">
                    <strong>{{ $item->product_name }}</strong><br>
                    <span style="font-size:12px; color:#767676;">{{ $item->vendor?->store_name ?? 'Vefiri vendor' }} &middot; Qty {{ $item->quantity }}</span>
                </td>
                <td align="right" valign="top" style="padding:13px 15px; border-bottom:1px solid #eeeeee; font-size:13px; line-height:20px; font-weight:700; color:#1c1c1c; white-space:nowrap;">
                    &#8358;{{ number_format($item->line_total / 100) }}
                </td>
            </tr>
        @empty
            <tr>
                <td style="padding:13px 15px; font-size:13px; line-height:20px; color:#767676;">No items recorded for this order.</td>
            </tr>
        @endforelse
    </table>

    @include('emails.partials.button', ['url' => $orderUrl, 'label' => 'View order details'])

    <p style="margin:18px 0 0; font-size:12px; line-height:19px; color:#767676;">
        If you have not received this order, please contact us at
        <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>
        with your order reference so we can investigate promptly.
    </p>
@endsection
