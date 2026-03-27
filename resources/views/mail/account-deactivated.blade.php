@extends('mail.layout')

@section('content')
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Account eliminato
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Ciao {{ $user->first_name }}, il tuo account Colors S.r.l. è stato eliminato come richiesto.
</p>
<p style="margin:0 0 24px; font-size:14px; color:#555555; line-height:1.7;">
    Se hai cambiato idea, puoi riattivarlo entro <strong>7 giorni</strong> cliccando il pulsante qui sotto. Dopo questa scadenza, dovrai contattarci direttamente per ripristinarlo.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
    <tr>
        <td style="background-color:#162032; padding:14px 32px;">
            <a href="{{ $reactivationUrl }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Annulla eliminazione
            </a>
        </td>
    </tr>
</table>

<p style="margin:0; font-size:13px; color:#aaaaaa; line-height:1.6;">
    Se hai eliminato l'account volontariamente e non vuoi riattivarlo, ignora questa email.
    Per assistenza scrivi a <a href="mailto:colorstarantosrl@gmail.com" style="color:#E8845A; text-decoration:none;">colorstarantosrl@gmail.com</a>.
</p>
@endsection
