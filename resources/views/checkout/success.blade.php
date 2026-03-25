@extends('layouts.app')
@section('title', 'Ordine confermato — Colors S.r.l.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 mb-6">
        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Ordine ricevuto!</h1>
    <p class="text-gray-500 text-sm mb-1">Numero ordine: <span class="font-semibold text-gray-700">{{ $order->order_number }}</span></p>
    <p class="text-gray-400 text-xs mb-8">Ti contatteremo per confermare i dettagli e il pagamento.</p>

    <div class="border border-gray-100 text-left mb-8">
        @foreach($order->items as $item)
        <div class="flex gap-4 px-5 py-4 border-b border-gray-100 last:border-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                @if($item->variant_name) <p class="text-xs text-gray-400">{{ $item->variant_name }}</p> @endif
                <p class="text-xs text-gray-500 mt-0.5">Qtà {{ $item->quantity }} × € {{ number_format($item->price, 2, ',', '.') }}</p>
            </div>
            <p class="text-sm font-semibold text-gray-900 whitespace-nowrap">€ {{ number_format($item->total, 2, ',', '.') }}</p>
        </div>
        @endforeach

        <div class="px-5 py-4 space-y-1 text-sm text-gray-600 bg-gray-50">
            <div class="flex justify-between">
                <span>Subtotale</span>
                <span>€ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Spedizione</span>
                <span>{{ $order->shipping_cost > 0 ? '€ ' . number_format($order->shipping_cost, 2, ',', '.') : 'Gratuita' }}</span>
            </div>
            <div class="flex justify-between font-semibold text-gray-900 border-t border-gray-200 pt-1">
                <span>Totale</span>
                <span>€ {{ number_format($order->total, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('account.orders.show', $order->order_number) }}" class="btn-primary">Visualizza ordine</a>
        <a href="{{ route('products.index') }}" class="btn-outline-dark">Continua gli acquisti</a>
    </div>

</div>
@endsection
