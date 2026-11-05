@extends('emails.layouts.brand')

@section('title', 'We received your Vefiri vendor application')
@section('preheader', 'Your vendor application is now pending review.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        We received your application
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Hi {{ $application->user?->name }}, thank you for applying to sell on Vefiri. We have received your vendor application for
        <strong style="color:#1c1c1c;">{{ $application->store_name }}</strong>.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#fafafa; border:1px solid #eeeeee; border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#767676;">Application summary</p>
                <p style="margin:7px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Store name:</strong> {{ $application->store_name }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Business name:</strong> {{ $application->business_name }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Submitted:</strong> {{ $application->created_at?->format('M j, Y \a\t g:i A') }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Status:</strong> Pending review</p>
            </td>
        </tr>
    </table>

    <h2 style="margin:26px 0 0; font-size:16px; line-height:23px; font-weight:700; color:#1c1c1c;">What happens next</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:10px;">
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Our team will review your business and store details.</td>
        </tr>
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; We will email you as soon as a decision has been made.</td>
        </tr>
        <tr>
            <td style="padding:0; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; You can track the application status from your account at any time.</td>
        </tr>
    </table>

    <p style="margin:18px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        You do not need to submit another application while this one is being reviewed.
    </p>

    @include('emails.partials.button', ['url' => $applicationUrl, 'label' => 'View application status'])

    <p style="margin:18px 0 0; font-size:12px; line-height:19px; color:#767676;">
        If any of your details need to be corrected, contact us at
        <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>
        and quote your store name.
    </p>
@endsection
