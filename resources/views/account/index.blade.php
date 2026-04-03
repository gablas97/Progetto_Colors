@extends('layouts.app')

@section('title', 'Il mio account - Colors S.r.l.')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-start justify-between mb-10">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Ciao, {{ $user->first_name }}!</h1>
            <p class="text-sm text-gray-500">Benvenuto nella tua area personale!</p>
        </div>
        <a href="{{ route('account.profile') }}"
           class="text-xs font-semibold tracking-wider uppercase text-gray-400 hover:text-primary transition-colors border border-gray-300 hover:border-primary px-4 py-2 flex-shrink-0">
            Il mio profilo
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
        <a href="{{ route('account.orders') }}"
           class="group border border-gray-300 p-6 hover:border-primary hover:bg-primary-50 transition-all">
            <p class="text-3xl font-semibold text-gray-900 group-hover:text-primary transition-colors mb-1">{{ $ordersCount }}</p>
            <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 group-hover:text-primary transition-colors">Ordini</p>
        </a>
        <a href="{{ route('account.wishlist') }}"
           class="group border border-gray-300 p-6 hover:border-primary hover:bg-primary-50 transition-all">
            <p class="text-3xl font-semibold text-gray-900 group-hover:text-primary transition-colors mb-1">{{ $wishlistCount }}</p>
            <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 group-hover:text-primary transition-colors">Wishlist</p>
        </a>
    </div>

    {{-- Indirizzo predefinito --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-500">Indirizzo predefinito</h2>
            <a href="{{ route('account.addresses') }}" class="text-xs text-primary uppercase font-semibold hover:text-primary-dark transition-colors">Gestisci</a>
        </div>
        <div class="border border-gray-300 p-5">
            @if($defaultAddress)
                <p class="text-sm font-medium text-gray-800 mb-1">{{ $defaultAddress->first_name }} {{ $defaultAddress->last_name }}</p>
                <p class="text-sm text-gray-500">{{ $defaultAddress->address }}</p>
                <p class="text-sm text-gray-500">{{ $defaultAddress->postal_code }} {{ $defaultAddress->city }} ({{ $defaultAddress->province }})</p>
                @if($defaultAddress->phone)
                    <p class="text-xs text-gray-400 mt-1">+39 {{ $defaultAddress->phone }}</p>
                @endif
            @else
                <div class="flex items-center gap-4">
                    <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500 mb-0.5">Nessun indirizzo salvato.</p>
                        <a href="{{ route('account.addresses') }}" class="text-xs font-semibold tracking-wider uppercase text-primary hover:text-primary-dark transition-colors">
                            Aggiungi indirizzo
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Banner recensione Google --}}
    @if($hasDeliveredOrder && config('services.google.place_id'))
    <div class="border border-gray-200 px-5 py-4 mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-yellow-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-gray-800">Sei soddisfatto dei tuoi acquisti?</p>
                <p class="text-xs text-gray-400 mt-0.5">Lascia una recensione su Google e aiuta altri clienti a scegliere.</p>
            </div>
        </div>
        <a href="https://search.google.com/local/writereview?placeid={{ config('services.google.place_id') }}"
           target="_blank" rel="noopener"
           class="flex-shrink-0 btn-primary text-xs">
            Recensisci su Google
        </a>
    </div>
    @endif

    {{-- Ultimi ordini --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-500">Ultimi ordini</h2>
            @if($recentOrders->isNotEmpty())
                <a href="{{ route('account.orders') }}" class="text-xs text-primary uppercase font-semibold hover:text-primary-dark transition-colors">Vedi tutti</a>
            @endif
        </div>

        @if($recentOrders->isNotEmpty())
        <div class="divide-y divide-gray-100 border border-gray-300 p-5">
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
        @else
        <div class="border border-gray-300 p-5 flex items-center gap-4">
            <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v.375c0 .621.504 1.125 1.125 1.125Z"/>
            </svg>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">Nessun ordine effettuato.</p>
                <a href="{{ route('products.index') }}" class="text-xs font-semibold tracking-wider uppercase text-primary hover:text-primary-dark transition-colors">
                    Scopri i prodotti
                </a>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
