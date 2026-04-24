@extends('layouts.app')

@section('title', 'I miei ordini - Colors S.r.l.')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">I miei ordini</h1>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-24">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-gray-400 text-sm mb-6">Non hai ancora effettuato ordini.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Vai ai prodotti</a>
        </div>
    @else
        <div class="border border-gray-100">
            @foreach($orders as $order)
            <div class="{{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <a href="{{ route('account.orders.show', $order->order_number) }}"
                   class="flex items-center justify-between px-5 py-5 hover:bg-gray-50 transition-colors block">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items->count() ?? '?' }} prodotti</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">€ {{ number_format($order->total, 2, ',', '.') }}</p>
                        <span class="inline-block text-xs px-2 py-0.5 mt-1
                            {{ match($order->status) {
                                'delivered' => 'bg-green-100 text-green-700',
                                'shipped'   => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-600',
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
                </a>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links('pagination::tailwind') }}</div>
    @endif

</div>
@endsection
