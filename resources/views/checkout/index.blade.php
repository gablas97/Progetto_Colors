@extends('layouts.app')
@section('title', 'Checkout — Colors S.r.l.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Checkout</h1>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        {{-- ===== COLONNA SINISTRA: form indirizzo ===== --}}
        <div class="lg:col-span-2 space-y-6">

            @guest
            <div class="border border-gray-200 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Contatto</h2>
                <div>
                    <label class="form-label">Email *</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required
                        class="input-field @error('guest_email') border-red-400 @enderror"
                        placeholder="La useremo per inviarti la conferma dell'ordine">
                    @error('guest_email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <p class="text-xs text-gray-400 mt-3">
                    Hai già un account? <a href="{{ route('login') }}" class="text-primary hover:underline">Accedi</a>
                </p>
            </div>
            @endguest

            <div class="border border-gray-200 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Indirizzo di spedizione</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Nome *</label>
                        <input type="text" name="shipping_first_name"
                            value="{{ old('shipping_first_name', $defaultAddress?->first_name ?? auth()->user()->first_name) }}"
                            required class="input-field @error('shipping_first_name') border-red-400 @enderror">
                        @error('shipping_first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Cognome *</label>
                        <input type="text" name="shipping_last_name"
                            value="{{ old('shipping_last_name', $defaultAddress?->last_name ?? auth()->user()->last_name) }}"
                            required class="input-field @error('shipping_last_name') border-red-400 @enderror">
                        @error('shipping_last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label">Azienda</label>
                    <input type="text" name="shipping_company"
                        value="{{ old('shipping_company', $defaultAddress?->company) }}"
                        class="input-field">
                </div>

                <div class="mt-4">
                    <label class="form-label">Indirizzo *</label>
                    <input type="text" name="shipping_address"
                        value="{{ old('shipping_address', $defaultAddress?->address) }}"
                        required class="input-field @error('shipping_address') border-red-400 @enderror">
                    @error('shipping_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="col-span-2">
                        <label class="form-label">Città *</label>
                        <input type="text" name="shipping_city"
                            value="{{ old('shipping_city', $defaultAddress?->city) }}"
                            required class="input-field @error('shipping_city') border-red-400 @enderror">
                        @error('shipping_city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Prov. *</label>
                        <input type="text" name="shipping_province"
                            value="{{ old('shipping_province', $defaultAddress?->province) }}"
                            required maxlength="2" class="input-field uppercase @error('shipping_province') border-red-400 @enderror">
                        @error('shipping_province') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="form-label">CAP *</label>
                        <input type="text" name="shipping_postal_code"
                            value="{{ old('shipping_postal_code', $defaultAddress?->postal_code) }}"
                            required class="input-field @error('shipping_postal_code') border-red-400 @enderror">
                        @error('shipping_postal_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Telefono</label>
                        <input type="tel" name="shipping_phone"
                            value="{{ old('shipping_phone', $defaultAddress?->phone ?? auth()->user()->phone) }}"
                            class="input-field">
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-3">Note ordine</h2>
                <textarea name="notes" rows="3" placeholder="Istruzioni per la consegna, richieste particolari..." class="input-field resize-none">{{ old('notes') }}</textarea>
            </div>

        </div>

        {{-- ===== COLONNA DESTRA: riepilogo ordine ===== --}}
        <div class="lg:col-span-1">
            <div class="border border-gray-200 p-6 sticky top-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Riepilogo ordine</h2>

                <div class="space-y-3 mb-5">
                    @foreach($cart->items as $item)
                    <div class="flex gap-3">
                        <div class="w-12 h-12 bg-gray-50 flex-shrink-0">
                            @if($item->product->main_image)
                                <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800 leading-tight">{{ $item->product->name }}</p>
                            @if($item->productVariant) <p class="text-xs text-gray-400">{{ $item->productVariant->name }}</p> @endif
                            <p class="text-xs text-gray-500">× {{ $item->quantity }}</p>
                        </div>
                        <p class="text-xs font-semibold text-gray-900 whitespace-nowrap">
                            € {{ number_format($item->product->price * $item->quantity, 2, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 pt-4 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotale</span>
                        <span>€ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Spedizione</span>
                        <span>{{ $shippingCost > 0 ? '€ ' . number_format($shippingCost, 2, ',', '.') : 'Gratuita' }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 border-t border-gray-100 pt-2">
                        <span>Totale</span>
                        <span>€ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($shippingCost > 0)
                <p class="text-xs text-gray-400 mt-3">
                    Spedizione gratuita per ordini superiori a € {{ number_format(50, 2, ',', '.') }}
                </p>
                @endif

                <button type="submit" class="btn-primary w-full mt-6 text-center">
                    Conferma ordine
                </button>

                <p class="text-xs text-center text-gray-400 mt-3">
                    Il pagamento avverrà in un secondo momento.<br>Riceverai una conferma via email.
</p>
            </div>
        </div>

    </form>

</div>
@endsection
