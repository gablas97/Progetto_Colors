@extends('mail.layout')

@section('content')
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#E8845A;">
    Verifica email
</p>
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Ciao {{ $user->first_name }}!
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Clicca il pulsante qui sotto per verificare il tuo indirizzo email e attivare il tuo account.
</p>
<p style="margin:0 0 28px; font-size:14px; color:#555555; line-height:1.7;">
    Il link scadrà tra <strong>60 minuti</strong>.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ $url }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Verifica email
            </a>
        </td>
    </tr>
</table>

<p style="margin:0 0 8px; font-size:12px; color:#aaaaaa; line-height:1.6;">
    Se il pulsante non funziona, copia e incolla questo link nel browser:
</p>
<p style="margin:0 0 24px; font-size:11px; color:#aaaaaa; word-break:break-all;">
    <a href="{{ $url }}" style="color:#E8845A; text-decoration:none;">{{ $url }}</a>
</p>

<p style="margin:0; font-size:13px; color:#aaaaaa; line-height:1.6;">
    Se non hai creato un account su Colors S.r.l., ignora questa email.
</p>
@endsection
