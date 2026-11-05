@extends('emails.layouts.brand')

@section('title', 'Your Vefiri vendor application has been approved')
@section('preheader', 'Your store is now a verified vendor on Vefiri.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        You are approved to sell on Vefiri
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Hi {{ $application->user?->name }}, congratulations. Your vendor application for
        <strong style="color:#1c1c1c;">{{ $vendor->store_name }}</strong> has been approved, and your store is now verified on Vefiri.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#e7f8f2; border:1px solid #b7ebd2; border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#007d5e;">Approved vendor</p>
                <p style="margin:7px 0 0; font-size:15px; line-height:22px; font-weight:700; color:#1c1c1c;">{{ $vendor->store_name }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#4f4f4f;">{{ $vendor->business_name }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#4f4f4f;">Approved {{ $application->reviewed_at?->format('M j, Y') }}</p>
            </td>
        </tr>
    </table>

    <h2 style="margin:26px 0 0; font-size:16px; line-height:23px; font-weight:700; color:#1c1c1c;">Next steps</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:10px;">
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Open your vendor dashboard using your existing Vefiri account.</td>
        </tr>
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Add your products with clear descriptions, prices and stock levels.</td>
        </tr>
        <tr>
            <td style="padding:0; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Keep your inventory and store details up to date for a smooth customer experience.</td>
        </tr>
    </table>

    @include('emails.partials.button', ['url' => $vendorDashboardUrl, 'label' => 'Open vendor dashboard'])

    <p style="margin:18px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        You can also
        <a href="{{ $applicationUrl }}" style="color:#f04400; font-weight:600; text-decoration:none;">view your application</a>
        in your account.
    </p>

    <p style="margin:18px 0 0; font-size:12px; line-height:19px; color:#767676;">
        Questions about selling on Vefiri? Contact us at
        <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>.
    </p>
@endsection
