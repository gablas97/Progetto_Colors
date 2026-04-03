@extends('mail.layout')

@section('content')
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#E8845A;">
    Ordine spedito
</p>
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Il tuo ordine è in arrivo!
</p>
<p style="margin:0 0 16px; font-size:14px; color:#555555; line-height:1.7;">
    Ciao {{ $order->shipping_first_name }}, il tuo ordine <strong>#{{ $order->number }}</strong>
    è stato spedito ed è in viaggio verso di te.
</p>

@if($order->tracking_number)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px; background-color:#fef6f2; padding:16px 20px;">
    <tr>
        <td>
            <p style="margin:0 0 4px; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#aaaaaa;">Numero di tracciamento</p>
            <p style="margin:0; font-size:16px; font-weight:700; color:#E8845A; letter-spacing:0.05em;">{{ $order->tracking_number }}</p>
        </td>
    </tr>
</table>
@endif

<p style="margin:0 0 24px; font-size:14px; color:#555555; line-height:1.7;">
    I tempi di consegna stimati sono di 1–3 giorni lavorativi.<br>
    Se hai domande sulla consegna, contattaci a
    <a href="mailto:colorstarantosrl@gmail.com" style="color:#E8845A; text-decoration:none;">colorstarantosrl@gmail.com</a>.
</p>

@if($order->user_id)
<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ route('account.orders.show', $order) }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Visualizza ordine
            </a>
        </td>
    </tr>
</table>
@endif

@php $placeId = config('services.google.place_id'); @endphp
@if($placeId)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0; background-color:#f9fafb; border:1px solid #e5e7eb; padding:20px 24px; text-align:center;">
    <tr>
        <td>
            <p style="margin:0 0 4px; font-size:13px; font-weight:700; color:#162032;">Sei soddisfatto del servizio?</p>
            <p style="margin:0 0 14px; font-size:12px; color:#6b7280;">La tua opinione è importante — aiuta altri clienti a scegliere Colors.</p>
            <a href="https://search.google.com/local/writereview?placeid={{ $placeId }}"
               style="display:inline-block; background-color:#ffffff; border:1px solid #e5e7eb; color:#374151; text-decoration:none; font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:10px 24px;">
                ★ Lascia una recensione su Google
            </a>
        </td>
    </tr>
</table>
@endif
@endsection
