<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $title ?? config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f3f5; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"
       style="background-color:#f1f3f5; padding:32px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="background-color:#ffffff; border:1px solid #dde1e6; border-radius:8px; overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td style="padding:24px 32px; border-bottom:3px solid #0f766e;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="color:#1a1a1a; font-size:18px; font-weight:bold;">
                                    {{ config('app.name') }}
                                </td>
                                @isset($badge)
                                    <td align="right"
                                        style="color:#0f766e; font-size:13px; font-weight:bold; letter-spacing:0.5px;">
                                        {{ $badge }}
                                    </td>
                                @endisset
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Body slot --}}
                <tr>
                    <td style="padding:36px 32px; color:#1f2933; font-size:16px; line-height:1.6;">
                        {{ $slot }}
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 32px; text-align: center; border-top:1px solid #e2e5e9; background-color:#f8f9fa; font-size:13px; color:#52606d; line-height:1.5;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
