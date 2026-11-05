@extends('emails.layouts.brand')

@section('title', 'Update on your Vefiri vendor application')
@section('preheader', 'An update is available on your Vefiri vendor application.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        Update on your vendor application
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Hi {{ $application->user?->name }}, thank you for your interest in selling on Vefiri. After carefully reviewing your application for
        <strong style="color:#1c1c1c;">{{ $application->store_name }}</strong>, we are unable to approve it at this time.
    </p>

    @if ($application->admin_note)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#fff7ed; border:1px solid #fed7aa; border-radius:12px;">
            <tr>
                <td style="padding:16px 18px;">
                    <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#9a3412;">Reason from our review team</p>
                    <p style="margin:8px 0 0; font-size:13px; line-height:21px; color:#7c2d12;">{{ $application->admin_note }}</p>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:18px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        You are welcome to review the feedback, update your business details and submit a new application when you are ready.
    </p>

    @include('emails.partials.button', ['url' => $reapplyUrl, 'label' => 'Review and reapply'])

    <p style="margin:18px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        You can also
        <a href="{{ $applicationUrl }}" style="color:#f04400; font-weight:600; text-decoration:none;">view your application status</a>
        in your account.
    </p>

    <p style="margin:18px 0 0; font-size:12px; line-height:19px; color:#767676;">
        If you would like more clarity on this decision, contact us at
        <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>
        and quote your store name.
    </p>
@endsection
