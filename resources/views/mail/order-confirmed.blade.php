@extends('mail.layout')

@section('content')
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#E8845A;">
    Ordine confermato
</p>
<p style="margin:0 0 20px; font-size:22px; font-weight:700; color:#162032;">
    Grazie per il tuo ordine!
</p>
<p style="margin:0 0 24px; font-size:14px; color:#555555; line-height:1.7;">
    Ciao {{ $order->shipping_first_name }}, abbiamo ricevuto il tuo ordine
    <strong>#{{ $order->number }}</strong> e lo stiamo elaborando.
    Riceverai una notifica non appena verrà spedito.
</p>

{{-- Riepilogo prodotti --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px; border-top:1px solid #eeeeee; border-bottom:1px solid #eeeeee; padding:16px 0;">
    @foreach($order->items as $item)
    <tr>
        <td style="padding:8px 0; font-size:13px; color:#333333; line-height:1.5;">
            <strong>{{ $item->product_name }}</strong>
            @if($item->variant_name)
                <span style="color:#999999;"> – {{ $item->variant_name }}</span>
            @endif
            <br>
            <span style="color:#999999;">Qtà: {{ $item->quantity }}</span>
        </td>
        <td style="padding:8px 0; font-size:13px; color:#333333; text-align:right; white-space:nowrap; vertical-align:top;">
            € {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}
        </td>
    </tr>
    @endforeach
</table>

{{-- Totali --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td style="font-size:13px; color:#777777; padding:3px 0;">Subtotale</td>
        <td style="font-size:13px; color:#777777; text-align:right;">€ {{ number_format($order->subtotal, 2, ',', '.') }}</td>
    </tr>
    <tr>
        <td style="font-size:13px; color:#777777; padding:3px 0;">Spedizione</td>
        <td style="font-size:13px; color:#777777; text-align:right;">
            {{ $order->shipping_cost > 0 ? '€ ' . number_format($order->shipping_cost, 2, ',', '.') : 'Gratuita' }}
        </td>
    </tr>
    <tr>
        <td style="font-size:15px; font-weight:700; color:#162032; padding:10px 0 3px; border-top:1px solid #eeeeee;">Totale</td>
        <td style="font-size:15px; font-weight:700; color:#162032; text-align:right; padding:10px 0 3px; border-top:1px solid #eeeeee;">
            € {{ number_format($order->total, 2, ',', '.') }}
        </td>
    </tr>
</table>

{{-- Indirizzo spedizione --}}
<p style="margin:0 0 6px; font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#aaaaaa;">Indirizzo di spedizione</p>
<p style="margin:0 0 24px; font-size:13px; color:#555555; line-height:1.7;">
    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
    {{ $order->shipping_address }}<br>
    {{ $order->shipping_postal_code }} {{ $order->shipping_city }} ({{ $order->shipping_province }})
</p>

@if($order->user_id)
<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="background-color:#E8845A; padding:14px 32px;">
            <a href="{{ route('account.orders.show', $order) }}" style="color:#ffffff; text-decoration:none; font-size:12px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;">
                Visualizza ordine
            </a>
        </td>
    </tr>
</table>
@endif
@endsection
