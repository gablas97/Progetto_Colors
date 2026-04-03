@extends('layouts.app')

@section('title', 'Indirizzi - Colors S.r.l.')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">


    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">I miei indirizzi</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    {{-- Indirizzi salvati --}}
    @if($addresses->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
        @foreach($addresses as $address)
        <div class="border border-gray-300 p-5 relative flex flex-col {{ $address->is_default ? 'border-primary' : '' }}">
            @if($address->is_default)
                <span class="absolute top-3 right-3 text-[10px] bg-primary text-white px-2 py-0.5 font-semibold tracking-wider">Predefinito</span>
            @else
                <form method="POST" action="{{ route('account.addresses.setDefault', $address->id) }}" class="absolute top-3 right-3">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs text-primary uppercase font-semibold hover:text-primary-dark transition-colors cursor-pointer">Imposta predefinito</button>
                </form>
            @endif
            <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 mb-2">
                {{ match($address->type) { 'shipping' => 'Spedizione', 'billing' => 'Fatturazione', default => 'Entrambi' } }}
            </p>
            <p class="text-sm font-medium text-gray-800">{{ $address->first_name }} {{ $address->last_name }}</p>
            @if($address->company) <p class="text-xs text-gray-500">{{ $address->company }}</p> @endif
            <p class="text-sm text-gray-600 mt-1">{{ $address->address }}</p>
            <p class="text-sm text-gray-600">{{ $address->postal_code }} {{ $address->city }} ({{ $address->province }})</p>
            @if($address->phone) <p class="text-xs text-gray-400 mt-1">+39 {{ $address->phone }}</p> @endif

            <div class="mt-auto pt-4">
                <form method="POST" action="{{ route('account.addresses.destroy', $address->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition-colors cursor-pointer">Elimina</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Form nuovo indirizzo --}}
    <details class="border border-gray-300" {{ $addresses->isEmpty() ? 'open' : '' }}>
        <summary class="px-5 py-4 text-sm font-semibold text-gray-700 cursor-pointer hover:bg-gray-50">
            Aggiungi nuovo indirizzo
        </summary>
        <form method="POST" action="{{ route('account.addresses.store') }}" class="p-5 border-t border-gray-100 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nome *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="input-field">
                </div>
                <div>
                    <label class="form-label">Cognome *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="input-field">
                </div>
            </div>

            <div>
                <label class="form-label">Tipo indirizzo *</label>
                <select name="type" required class="input-field">
                    <option value="shipping">Spedizione</option>
                    <option value="billing">Fatturazione</option>
                    <option value="both">Entrambi</option>
                </select>
            </div>

            <div>
                <label class="form-label">Indirizzo *</label>
                <input type="text" name="address" value="{{ old('address') }}" required class="input-field">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Città *</label>
                    <input type="text" name="city" value="{{ old('city') }}" required class="input-field">
                </div>
                <div>
                    <label class="form-label">Prov. *</label>
                    <input type="text" name="province" value="{{ old('province') }}" required maxlength="2" class="input-field uppercase">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">CAP *</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" required class="input-field">
                </div>
                <div>
                    <label class="form-label">Telefono</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">+39</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="input-field flex-1 min-w-0">
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_default" class="accent-primary">
                <span class="text-xs text-gray-600">Imposta come indirizzo predefinito</span>
            </label>

            <button type="submit" class="btn-primary">Salva indirizzo</button>
        </form>
    </details>
</div>
@endsection