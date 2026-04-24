@extends('layouts.app')

@section('title', 'Carrello - Colors S.r.l.')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Il tuo carrello</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($cart->items->isEmpty())
        <div class="text-center py-24">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-400 text-sm mb-6">Il tuo carrello è vuoto.</p>
            <a href="{{ route('products.index') }}" class="btn-primary">Vai ai prodotti</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- Lista articoli --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <span class="text-xs text-gray-400">{{ $cart->items->sum('quantity') }} {{ $cart->items->sum('quantity') === 1 ? 'articolo' : 'articoli' }}</span>
                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-primary uppercase font-semibold hover:text-primary-dark transition-colors cursor-pointer">
                            Svuota carrello
                        </button>
                    </form>
                </div>
            <div class="divide-y divide-gray-100">
                @foreach($cart->items as $item)
                <div class="flex gap-4 py-5">
                    {{-- Immagine --}}
                    <a href="{{ route('products.show', $item->product->slug) }}"
                       class="flex-shrink-0 w-20 h-20 bg-gray-50 overflow-hidden">
                        @if($item->product->main_image)
                            <img src="{{ Storage::url($item->product->main_image) }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100"></div>
                        @endif
                    </a>

                    {{-- Info + quantità --}}
                    <div class="flex-1 min-w-0 flex flex-col gap-2">
                        {{-- Nome, variante, prezzo --}}
                        <div class="min-w-0">
                            <a href="{{ route('products.show', $item->product->slug) }}"
                               class="text-sm font-medium text-gray-800 hover:text-primary transition-colors line-clamp-2">
                                {{ $item->product->name }}
                            </a>
                            @if($item->productVariant)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->productVariant->name }}</p>
                            @endif
                            <p class="text-sm font-semibold text-gray-900 mt-1">
                                € {{ number_format($item->product->price, 2, ',', '.') }}
                            </p>
                        </div>

                        {{-- Quantità + rimuovi + totale riga --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                    @csrf @method('PATCH')
                                    <div class="flex items-center border border-gray-300">
                                        <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}"
                                                class="px-2.5 py-1.5 text-gray-500 hover:text-gray-900 transition-colors text-sm cursor-pointer">−</button>
                                        <span class="px-3 text-sm text-gray-700">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                                class="px-2.5 py-1.5 text-gray-500 hover:text-gray-900 transition-colors text-sm cursor-pointer">+</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-300 hover:text-red-400 transition-colors cursor-pointer" aria-label="Rimuovi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <span class="text-sm font-semibold text-gray-900 whitespace-nowrap">
                                € {{ number_format($item->product->price * $item->quantity, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            </div>

            {{-- Riepilogo --}}
            <div>
                <div class="bg-gray-50 p-6 sticky top-20">
                    <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-700 mb-5">Riepilogo</h2>

                    @php
                        $subtotal = $cart->items->sum(fn($i) => $i->product->price * $i->quantity);
                    @endphp

                    <div class="space-y-2 text-sm text-gray-600 mb-5">
                        <div class="flex justify-between">
                            <span>Subtotale</span>
                            <span>€ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-400">
                            <span>Spedizione</span>
                            <span>Calcolata al checkout</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-6">
                        <div class="flex justify-between font-semibold text-gray-900">
                            <span>Totale</span>
                            <span>€ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">IVA inclusa</p>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-primary block text-center w-full">
                        Procedi al checkout
                    </a>

                    @guest
                    <p class="text-xs text-center text-gray-400 mt-2">
                        <a href="{{ route('login') }}" class="text-primary hover:underline">Accedi</a>
                        o continua come ospite
                    </p>
                    @endguest

                    <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-400 hover:text-gray-600 transition-colors mt-4">
                        Continua lo shopping
                    </a>
                </div>
            </div>

        </div>
    @endif

</div>

@endsection
