@extends('layouts.app')
@section('title', 'Il mio account — Colors S.r.l.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Ciao, {{ $user->first_name }}!</h1>
    <p class="text-sm text-gray-500 mb-10">Benvenuto nella tua area personale.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @foreach([
            ['I miei ordini',  route('account.orders'),    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['Indirizzi',      route('account.addresses'), 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
            ['Wishlist',       route('account.wishlist'),  'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['Profilo',        route('account.profile'),   'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ] as [$label, $url, $icon])
        <a href="{{ $url }}" class="group flex flex-col items-center gap-3 border border-gray-100 p-6 hover:border-primary hover:bg-primary-50 transition-all">
            <svg class="w-7 h-7 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon }}"/>
            </svg>
            <span class="text-xs font-semibold tracking-wider uppercase text-gray-600 group-hover:text-primary transition-colors">{{ $label }}</span>
        </a>
        @endforeach
    </div>

    {{-- Ultimi ordini --}}
    @if($recentOrders->isNotEmpty())
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-500">Ultimi ordini</h2>
            <a href="{{ route('account.orders') }}" class="text-xs text-primary hover:text-primary-dark transition-colors">Vedi tutti →</a>
        </div>
        <div class="divide-y divide-gray-100 border border-gray-100">
            @foreach($recentOrders as $order)
            <a href="{{ route('account.orders.show', $order->id) }}"
               class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">€ {{ number_format($order->total, 2, ',', '.') }}</p>
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
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
