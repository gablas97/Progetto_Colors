@extends('layouts.app')

@section('title', 'Registrati — Colors S.r.l.')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg">

        <div class="text-center mb-10">
            <h1 class="text-2xl font-semibold text-gray-900">Crea il tuo account</h1>
            <p class="text-sm text-gray-500 mt-2">Hai già un account?
                <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Accedi</a>
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="form-label">Nome</label>
                    <input type="text" id="first_name" name="first_name"
                           value="{{ old('first_name') }}"
                           required autocomplete="given-name"
                           class="input-field @error('first_name') border-red-400 @enderror">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="form-label">Cognome</label>
                    <input type="text" id="last_name" name="last_name"
                           value="{{ old('last_name') }}"
                           required autocomplete="family-name"
                           class="input-field @error('last_name') border-red-400 @enderror">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       required autocomplete="email"
                       class="input-field @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="form-label">Telefono <span class="text-gray-400 font-normal normal-case">(opzionale)</span></label>
                <input type="tel" id="phone" name="phone"
                       value="{{ old('phone') }}"
                       autocomplete="tel"
                       class="input-field @error('phone') border-red-400 @enderror">
                @error('phone')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       required autocomplete="new-password"
                       class="input-field @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Conferma Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password"
                       class="input-field">
            </div>

            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="newsletter" class="mt-0.5 accent-primary">
                <span class="text-xs text-gray-600 leading-relaxed">Desidero ricevere offerte e novità via email (newsletter)</span>
            </label>

            <button type="submit" class="btn-primary w-full text-center block">
                Crea account
            </button>
        </form>

    </div>
</div>

@endsection
