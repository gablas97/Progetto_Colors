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
                <a href="{{ route('account.orders.show', $order->id) }}"
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
                @if($order->status === 'delivered' && config('services.google.place_id'))
                <div class="px-5 pb-3 flex justify-end">
                    <a href="https://g.page/r/{{ config('services.google.place_id') }}/review"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                        </svg>
                        Recensisci su Google
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links('pagination::tailwind') }}</div>
    @endif

</div>
@endsection
