@extends('layouts.app')
@section('title', 'Ordine ' . $order->order_number . ' — Colors S.r.l.')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.orders') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Ordine {{ $order->order_number }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- Stato --}}
    <div class="bg-gray-50 px-5 py-4 mb-8 flex items-center justify-between">
        <span class="text-sm text-gray-600">Stato ordine</span>
        <span class="text-sm font-semibold
            {{ match($order->status) {
                'delivered' => 'text-green-600',
                'shipped'   => 'text-blue-600',
                'cancelled' => 'text-red-500',
                default     => 'text-gray-700',
            } }}">
            {{ match($order->status) {
                'pending'    => 'In attesa',
                'processing' => 'In elaborazione',
                'shipped'    => 'Spedito',
                'delivered'  => 'Consegnato',
                'cancelled'  => 'Annullato',
                default      => $order->status,
            } }}
        </span>
    </div>

    {{-- Prodotti --}}
    <div class="divide-y divide-gray-100 border border-gray-100 mb-8">
        @foreach($order->items as $item)
        <div class="flex gap-4 px-5 py-4">
            <div class="flex-shrink-0 w-16 h-16 bg-gray-50">
                @if($item->product?->main_image)
                    <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                @if($item->variant_name)
                    <p class="text-xs text-gray-400">{{ $item->variant_name }}</p>
                @endif
                <p class="text-xs text-gray-500 mt-1">Qtà {{ $item->quantity }} × € {{ number_format($item->price, 2, ',', '.') }}</p>
            </div>
            <div class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                € {{ number_format($item->total, 2, ',', '.') }}
            </div>
        </div>
        @endforeach
    </div>

    {{-- Totali --}}
    <div class="space-y-2 text-sm text-gray-600 mb-8">
        <div class="flex justify-between">
            <span>Subtotale</span>
            <span>€ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div class="flex justify-between text-green-600">
            <span>Sconto ({{ $order->discount_code }})</span>
            <span>−€ {{ number_format($order->discount_amount, 2, ',', '.') }}</span>
        </div>
        @endif
        <div class="flex justify-between">
            <span>Spedizione</span>
            <span>€ {{ number_format($order->shipping_cost, 2, ',', '.') }}</span>
        </div>
        <div class="flex justify-between font-semibold text-gray-900 border-t border-gray-100 pt-2">
            <span>Totale</span>
            <span>€ {{ number_format($order->total, 2, ',', '.') }}</span>
        </div>
    </div>

    {{-- Indirizzo spedizione --}}
    @if($order->shipping_address)
    <div class="border-t border-gray-100 pt-6">
        <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 mb-2">Indirizzo di spedizione</p>
        <p class="text-sm text-gray-600">
            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_postal_code }} {{ $order->shipping_city }} ({{ $order->shipping_province }})
        </p>
    </div>
    @endif

</div>
@endsection
