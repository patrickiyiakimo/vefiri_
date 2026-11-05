@extends('emails.layouts.brand')

@section('title', 'New sign-in to your Vefiri account')
@section('preheader', 'A successful sign-in to your Vefiri account was recorded.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        New sign-in to your account
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Hi {{ $user->name }}, we recorded a successful sign-in to your Vefiri account.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#fafafa; border:1px solid #eeeeee; border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#767676;">Sign-in details</p>
                <p style="margin:7px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Account email:</strong> {{ $user->email }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Date and time:</strong> {{ $signedInAt }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>IP address:</strong> {{ $ipAddress ?: 'Unavailable' }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Device:</strong> {{ $device ?: 'Unavailable' }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:18px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        If this was you, no further action is required.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:16px;">
        <tr>
            <td style="padding:13px 15px; background-color:#fff7ed; border:1px solid #fed7aa; border-radius:10px; font-size:12px; line-height:20px; color:#7c2d12;">
                <strong>Did not sign in?</strong>
                Your account may be at risk. Please change your password immediately and contact us at
                <a href="mailto:{{ $supportEmail }}" style="color:#9a3412; font-weight:700; text-decoration:none;">{{ $supportEmail }}</a>.
            </td>
        </tr>
    </table>

    @include('emails.partials.button', ['url' => $accountUrl, 'label' => 'Review account security'])
@endsection
