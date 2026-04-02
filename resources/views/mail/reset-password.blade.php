@extends('mail.layout')

@section('content')
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#E8845A;">
    Reset password
</p>
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Ciao {{ $user->first_name }}!
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Hai richiesto di reimpostare la password del tuo account. Clicca il pulsante qui sotto per sceglierne una nuova.
</p>
<p style="margin:0 0 28px; font-size:14px; color:#555555; line-height:1.7;">
    Il link scadrà tra <strong>60 minuti</strong>. Se non hai richiesto il reset, il tuo account è al sicuro — ignora questa email.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ $resetUrl }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Reimposta password
            </a>
        </td>
    </tr>
</table>

<p style="margin:0 0 8px; font-size:12px; color:#aaaaaa; line-height:1.6;">
    Se il pulsante non funziona, copia e incolla questo link nel browser:
</p>
<p style="margin:0 0 24px; font-size:11px; color:#aaaaaa; word-break:break-all;">
    <a href="{{ $resetUrl }}" style="color:#E8845A; text-decoration:none;">{{ $resetUrl }}</a>
</p>

<p style="margin:0; font-size:13px; color:#aaaaaa; line-height:1.6;">
    Per sicurezza, questo link può essere usato una sola volta.
</p>
@endsection
