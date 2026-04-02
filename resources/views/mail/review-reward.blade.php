@extends('mail.layout')

@section('content')
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#E8845A;">
    Grazie per la tua recensione
</p>
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Ecco il tuo sconto del 15%!
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Ciao {{ $user->first_name }}, la tua recensione è stata approvata e pubblicata. Grazie per aver aiutato altri clienti a scegliere meglio!
</p>
<p style="margin:0 0 24px; font-size:14px; color:#555555; line-height:1.7;">
    Come ringraziamento, ti abbiamo riservato uno sconto del <strong>15%</strong> sul tuo prossimo ordine:
</p>

{{-- Codice sconto --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px; background-color:#fef6f2; border:1px solid #f5cdb4; padding:20px 24px; text-align:center;">
    <tr>
        <td>
            <p style="margin:0 0 6px; font-size:11px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#aaaaaa;">Il tuo codice sconto</p>
            <p style="margin:0; font-size:28px; font-weight:700; color:#E8845A; letter-spacing:0.12em; font-family:monospace;">{{ $discount->code }}</p>
            <p style="margin:8px 0 0; font-size:12px; color:#999999;">Valido fino al {{ $discount->expires_at->format('d/m/Y') }} · sconto del 15%</p>
        </td>
    </tr>
</table>

<p style="margin:0 0 24px; font-size:13px; color:#777777; line-height:1.7;">
    Il codice è monouso e può essere usato una sola volta. Si ricorda che è personale e non cedibile.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ route('products.index') }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Vai al negozio
            </a>
        </td>
    </tr>
</table>

<p style="margin:0; font-size:12px; color:#aaaaaa; line-height:1.6;">
    Non hai scritto una recensione su Colors S.r.l.? Contattaci a
    <a href="mailto:colorstarantosrl@gmail.com" style="color:#E8845A; text-decoration:none;">colorstarantosrl@gmail.com</a>.
</p>
@endsection
