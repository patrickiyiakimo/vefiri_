<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <title>@yield('title', $brandName ?? config('app.name'))</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; color:#1c1c1c; font-family:'Segoe UI', Arial, Helvetica, sans-serif; -webkit-font-smoothing:antialiased;">
    <div style="display:none; max-height:0; max-width:0; overflow:hidden; opacity:0; color:#f5f5f5; font-size:1px; line-height:1px;">
        @yield('preheader')
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:36px 12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border:1px solid #e6e6e6; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td style="height:6px; background-color:#ff5a00; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px 10px;">
                            <a href="{{ $brandUrl ?? config('app.url') }}" style="display:inline-block; color:#1c1c1c; text-decoration:none;">
                                <img src="{{ asset('images/email/vefiri-icon.png') }}" width="40" height="40" alt="Vefiri logo" style="display:inline-block; width:40px; height:40px; border:0; border-radius:9px; vertical-align:middle;">
                                <span style="display:inline-block; padding-left:10px; vertical-align:middle; font-size:25px; font-weight:800; letter-spacing:-1.2px; color:#1c1c1c;">
                                    <span style="color:#ff5a00;">Ve</span>firi
                                </span>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:6px 32px 0; font-size:14px; line-height:22px; color:#1c1c1c;">
                            @yield('content')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 32px 0;">
                            @include('emails.partials.signature')
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px 28px; background-color:#fafafa; border-top:1px solid #eeeeee;">
                            <p style="margin:0; font-size:11px; line-height:17px; color:#767676; text-align:center;">
                                This is an automated email from {{ $brandName ?? config('app.name') }}. Please do not reply to this message.
                            </p>
                            <p style="margin:6px 0 0; font-size:11px; line-height:17px; color:#9a9a9a; text-align:center;">
                                &copy; {{ date('Y') }} {{ $brandName ?? config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
