@extends('emails.layouts.brand')

@section('title', 'Welcome to Vefiri')
@section('preheader', 'Your Vefiri account is ready. Here is what you can do next.')

@section('content')
    <h1 style="margin:0; font-size:25px; line-height:32px; font-weight:800; letter-spacing:-0.7px; color:#1c1c1c;">
        Welcome to Vefiri, {{ $user->name }}
    </h1>

    <p style="margin:14px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Your Vefiri account has been created successfully with
        <strong style="color:#1c1c1c;">{{ $user->email }}</strong>. We are glad to have you with us.
    </p>

    <p style="margin:12px 0 0; font-size:14px; line-height:23px; color:#4f4f4f;">
        Vefiri brings quality products, verified vendors and dependable nationwide delivery together in one place.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:22px; background-color:#fafafa; border:1px solid #eeeeee; border-radius:12px;">
        <tr>
            <td style="padding:16px 18px;">
                <p style="margin:0; font-size:11px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#767676;">Your account</p>
                <p style="margin:7px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Email:</strong> {{ $user->email }}</p>
                <p style="margin:4px 0 0; font-size:13px; line-height:20px; color:#1c1c1c;"><strong>Status:</strong> Active</p>
            </td>
        </tr>
    </table>

    @include('emails.partials.button', ['url' => $homeUrl, 'label' => 'Start shopping on Vefiri'])

    <p style="margin:18px 0 0; font-size:13px; line-height:21px; color:#4f4f4f;">
        You can also
        <a href="{{ $accountUrl }}" style="color:#f04400; font-weight:600; text-decoration:none;">view your account</a>
        to update your profile, track orders and manage your saved favourites.
    </p>

    <h2 style="margin:26px 0 0; font-size:16px; line-height:23px; font-weight:700; color:#1c1c1c;">Make the most of your account</h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:10px;">
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Discover products from trusted, verified vendors.</td>
        </tr>
        <tr>
            <td style="padding:0 0 8px; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Save items to your wishlist and return to them later.</td>
        </tr>
        <tr>
            <td style="padding:0; font-size:13px; line-height:21px; color:#4f4f4f;">&bull; Review your order history and delivery details at any time.</td>
        </tr>
    </table>

    <p style="margin:22px 0 0; font-size:12px; line-height:19px; color:#767676;">
        If you did not create this account, please contact us at
        <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>
        so we can help secure your information.
    </p>
@endsection
