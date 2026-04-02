@extends('layouts.app')
@section('title', 'Checkout - Colors S.r.l.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <h1 class="text-2xl font-semibold text-gray-900 mb-8">Checkout</h1>

    <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}"
          data-discount-apply-url="{{ route('checkout.discount.apply') }}"
          data-discount-remove-url="{{ route('checkout.discount.remove') }}"
          class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        {{-- ===== COLONNA SINISTRA ===== --}}
        <div class="lg:col-span-2 space-y-6">

            @guest
            <div class="border border-gray-200 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Contatto</h2>
                <div>
                    <label for="guest_email" class="form-label">Email *</label>
                    <input id="guest_email" type="email" name="guest_email" value="{{ old('guest_email') }}" required
                        class="input-field @error('guest_email') border-red-400 @enderror"
                        placeholder="La useremo per inviarti la conferma dell'ordine">
                    @error('guest_email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <p class="text-xs text-gray-400 mt-3">
                    Hai già un account? <a href="{{ route('login') }}" class="text-primary hover:underline">Accedi</a>
                </p>
            </div>
            @endguest

            <div class="border border-gray-300 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Indirizzo di spedizione</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_first_name" class="form-label">Nome *</label>
                        <input id="shipping_first_name" type="text" name="shipping_first_name"
                            value="{{ old('shipping_first_name', $defaultAddress?->first_name ?? auth()->user()?->first_name) }}"
                            required class="input-field @error('shipping_first_name') border-red-400 @enderror">
                        @error('shipping_first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="shipping_last_name" class="form-label">Cognome *</label>
                        <input id="shipping_last_name" type="text" name="shipping_last_name"
                            value="{{ old('shipping_last_name', $defaultAddress?->last_name ?? auth()->user()?->last_name) }}"
                            required class="input-field @error('shipping_last_name') border-red-400 @enderror">
                        @error('shipping_last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="shipping_company" class="form-label">Azienda</label>
                    <input id="shipping_company" type="text" name="shipping_company"
                        value="{{ old('shipping_company', $defaultAddress?->company) }}"
                        class="input-field">
                </div>

                <div class="mt-4">
                    <label for="shipping_address" class="form-label">Indirizzo *</label>
                    <input id="shipping_address" type="text" name="shipping_address"
                        value="{{ old('shipping_address', $defaultAddress?->address) }}"
                        required class="input-field @error('shipping_address') border-red-400 @enderror">
                    @error('shipping_address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div class="col-span-2">
                        <label for="shipping_city" class="form-label">Città *</label>
                        <input id="shipping_city" type="text" name="shipping_city"
                            value="{{ old('shipping_city', $defaultAddress?->city) }}"
                            required class="input-field @error('shipping_city') border-red-400 @enderror">
                        @error('shipping_city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="shipping_province" class="form-label">Prov. *</label>
                        <input id="shipping_province" type="text" name="shipping_province"
                            value="{{ old('shipping_province', $defaultAddress?->province) }}"
                            required maxlength="2" class="input-field uppercase @error('shipping_province') border-red-400 @enderror">
                        @error('shipping_province') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="shipping_postal_code" class="form-label">CAP *</label>
                        <input id="shipping_postal_code" type="text" name="shipping_postal_code"
                            value="{{ old('shipping_postal_code', $defaultAddress?->postal_code) }}"
                            required class="input-field @error('shipping_postal_code') border-red-400 @enderror">
                        @error('shipping_postal_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="shipping_phone" class="form-label">Telefono</label>
                        <input id="shipping_phone" type="tel" name="shipping_phone"
                            value="{{ old('shipping_phone', $defaultAddress?->phone ?? auth()->user()?->phone) }}"
                            class="input-field">
                    </div>
                </div>
            </div>

            <div class="border border-gray-300 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-3">Note ordine</h2>
                <textarea id="notes" name="notes" rows="3"
                    placeholder="Istruzioni per la consegna, richieste particolari..."
                    class="input-field resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- ===== METODO DI PAGAMENTO ===== --}}
            <div class="border border-gray-300 p-6">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-gray-400 mb-5">Metodo di pagamento</h2>

                <div class="space-y-3">
                    {{-- Stripe / Carta --}}
                    <label class="cursor-pointer payment-method-option @error('payment_method') border-red-400 @enderror" id="opt-stripe">
                        <input type="radio" name="payment_method" value="stripe" class="sr-only"
                            {{ old('payment_method', 'stripe') === 'stripe' ? 'checked' : '' }}>
                        <span class="payment-method-radio"></span>
                        <span class="flex items-center gap-3 flex-1">
                            <svg class="w-5 h-5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span>
                                <span class="text-sm font-medium text-gray-800">Carta di credito / debito</span>
                                <span class="block text-xs text-gray-400">Visa, Mastercard, American Express</span>
                            </span>
                        </span>
                        <span class="flex gap-1 items-center flex-shrink-0">
                            <span class="payment-card-badge">VISA</span>
                            <span class="payment-card-badge">MC</span>
                            <span class="payment-card-badge">AMEX</span>
                        </span>
                    </label>

                    {{-- PayPal --}}
                    <label class="cursor-pointer payment-method-option" id="opt-paypal">
                        <input type="radio" name="payment_method" value="paypal" class="sr-only"
                            {{ old('payment_method') === 'paypal' ? 'checked' : '' }}>
                        <span class="payment-method-radio"></span>
                        <span class="flex items-center gap-3 flex-1">
                            <img src="{{ asset('images/paypal-logo.svg') }}" alt="PayPal" class="h-5 w-auto flex-shrink-0" data-hide-on-error>
                            <span>
                                <span class="text-sm font-medium text-gray-800">PayPal</span>
                                <span class="block text-xs text-gray-400">Verrai reindirizzato su PayPal per completare il pagamento</span>
                            </span>
                        </span>
                    </label>
                </div>

                @error('payment_method')
                    <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- ===== COLONNA DESTRA: riepilogo ===== --}}
        <div class="lg:col-span-1">
            <div class="border border-gray-300 p-6">
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

                {{-- Codice sconto --}}
                <div class="border-t border-gray-200 pt-4 mb-4">
                    <div id="discount-applied" class="checkout-discount-tag @if(!$discount['code']) hidden @endif">
                        <span class="discount-tag-text">
                            <span id="discount-code-display">{{ $discount['code'] }}</span>
                            <span id="discount-label-display">{{ $discount['label'] }}</span>
                        </span>
                        <button type="button" id="remove-discount-btn" class="discount-tag-remove" aria-label="Rimuovi sconto">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div id="discount-form" class="flex gap-2 @if($discount['code']) hidden @endif">
                        <input type="text" id="discount-input" placeholder="Codice sconto"
                            class="input-field flex-1 text-xs py-2" autocomplete="off" autocapitalize="characters">
                        <button type="button" id="apply-discount-btn"
                            class="px-3 py-2 text-xs font-medium border border-gray-300 text-gray-700 hover:border-gray-400 transition-colors whitespace-nowrap cursor-pointer">
                            Applica
                        </button>
                    </div>
                    <p id="discount-error" class="text-xs text-red-500 mt-1.5 hidden"></p>
                </div>

                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotale</span>
                        <span>€ {{ number_format($subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Spedizione</span>
                        <span>{{ $shippingCost > 0 ? '€ ' . number_format($shippingCost, 2, ',', '.') : 'Gratuita' }}</span>
                    </div>
                    <div id="discount-row" class="checkout-discount-row @if(!($discount['amount'] > 0)) hidden @endif">
                        <span>Sconto</span>
                        <span id="discount-amount-display">@if($discount['amount'] > 0)-€ {{ number_format($discount['amount'], 2, ',', '.') }}@endif</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 border-t border-gray-100 pt-2">
                        <span>Totale</span>
                        <span id="total-display">€ {{ number_format($total, 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($shippingCost > 0)
                <p class="text-xs text-gray-400 mt-3">
                    Spedizione gratuita per ordini superiori a € {{ number_format(50, 2, ',', '.') }}
                </p>
                @endif

                <button type="submit" class="btn-primary w-full mt-6 text-center">
                    Vai al pagamento
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
