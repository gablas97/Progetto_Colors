@extends('mail.layout')

@section('content')
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Benvenuto, {{ $user->first_name }}!
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Siamo felici di averti con noi. Il tuo account Colors S.r.l. è stato creato con successo.
</p>
<p style="margin:0 0 24px; font-size:14px; color:#555555; line-height:1.7;">
    Da adesso puoi accedere alla tua area personale per tenere traccia degli ordini, salvare i tuoi prodotti preferiti e gestire le tue informazioni.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 28px;">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ route('account.index') }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Vai al tuo account
            </a>
        </td>
    </tr>
</table>

<p style="margin:0; font-size:13px; color:#aaaaaa; line-height:1.6;">
    Se non hai creato questo account, ignora questa email o contattaci a
    <a href="mailto:colorstarantosrl@gmail.com" style="color:#E8845A; text-decoration:none;">colorstarantosrl@gmail.com</a>.
</p>
@endsection
