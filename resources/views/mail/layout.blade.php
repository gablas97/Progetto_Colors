<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial,Helvetica,sans-serif; font-size:14px; color:#333333;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding:40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background-color:#ffffff;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#E8845A; padding:24px 40px;">
                            <a href="{{ url('/') }}" style="text-decoration:none; display:inline-block;">
                                <img src="{{ url('images/logo.png') }}"
                                     alt="Colors S.r.l."
                                     width="140"
                                     style="display:block; height:auto; max-height:52px; object-fit:contain;">
                            </a>
                        </td>
                    </tr>

                    {{-- Contenuto --}}
                    <tr>
                        <td style="padding:40px 40px 32px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Divisore --}}
                    <tr>
                        <td style="padding:0 40px;">
                            <hr style="border:none; border-top:1px solid #eeeeee; margin:0;">
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 40px; background-color:#fafafa; text-align:center;">
                            <p style="margin:0 0 6px; font-size:12px; color:#999999;">
                                Colors S.r.l. · Via Umbria 35, 74121 Taranto
                            </p>
                            <p style="margin:0 0 6px; font-size:12px; color:#999999;">
                                Lun–Ven: 08:00–13:30 / 16:30–20:00 &nbsp;·&nbsp; Sab: 08:00–13:00
                            </p>
                            <p style="margin:8px 0 0; font-size:12px;">
                                <a href="{{ url('/') }}" style="color:#E8845A; text-decoration:none;">Visita il sito</a>
                                &nbsp;·&nbsp;
                                <a href="mailto:colorstarantosrl@gmail.com" style="color:#E8845A; text-decoration:none;">colorstarantosrl@gmail.com</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
