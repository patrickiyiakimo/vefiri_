<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; border-top:1px solid #e6e6e6; padding-top:22px;">
    <tr>
        <td width="54" valign="top" style="width:54px;">
            <img src="{{ asset('images/email/vefiri-icon.png') }}" width="44" height="44" alt="Vefiri logo" style="display:block; width:44px; height:44px; border:0; border-radius:10px;">
        </td>
        <td valign="top" style="padding-left:14px;">
            <p style="margin:0; font-size:19px; line-height:24px; font-weight:800; letter-spacing:-0.8px; color:#1c1c1c;">
                <span style="color:#ff5a00;">Ve</span>firi
            </p>
            <p style="margin:4px 0 0; font-size:12px; line-height:18px; color:#767676;">
                Shop quality products with fast, nationwide delivery.
            </p>
            <p style="margin:8px 0 0; font-size:12px; line-height:18px; color:#4f4f4f;">
                <a href="{{ $brandUrl }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $brandHost }}</a>
                <span style="color:#d0d0d0;">&nbsp;&bull;&nbsp;</span>
                <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>
            </p>
        </td>
    </tr>
</table>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; margin-top:18px;">
    <tr>
        <td style="padding:13px 15px; background-color:#f5f5f5; border-radius:10px; font-size:11px; line-height:18px; color:#767676;">
            <strong style="color:#4f4f4f;">Automated message:</strong>
            Please do not reply to this email. If you need help, contact
            <a href="mailto:{{ $supportEmail }}" style="color:#f04400; font-weight:600; text-decoration:none;">{{ $supportEmail }}</a>.
        </td>
    </tr>
</table>
