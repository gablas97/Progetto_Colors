@extends('layouts.app')

@section('title', 'Profilo - Colors S.r.l.')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Il mio profilo</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-5">
        @csrf @method('PATCH')

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="form-label">Nome</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required autocomplete="given-name" class="input-field @error('first_name') border-red-400 @enderror">
                @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="last_name" class="form-label">Cognome</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required autocomplete="family-name" class="input-field @error('last_name') border-red-400 @enderror">
                @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="profile_email" class="form-label">Email</label>
            <input id="profile_email" type="email" value="{{ $user->email }}" disabled class="input-field bg-gray-50 text-gray-400 cursor-not-allowed">
            <p class="text-xs text-gray-400 mt-1">L'email non può essere modificata.</p>
        </div>

        <div>
            <label for="phone" class="form-label">Telefono</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel" class="input-field">
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="newsletter_subscribed" class="accent-primary" @checked($user->newsletter_subscribed)>
            <span class="text-xs text-gray-600">Iscritto alla newsletter</span>
        </label>

        {{-- Sezione cambio password --}}
        <div>
            <div class="flex items-center justify-between">
                <p class="form-label">Password</p>
                <button type="button" id="toggle-password-section"
                        class="text-xs text-primary uppercase font-semibold hover:text-primary-dark transition-colors cursor-pointer">
                    Cambia password
                </button>
            </div>

            <div id="password-section" class="{{ $errors->has('current_password') || $errors->has('new_password') ? '' : 'hidden' }} mt-4 space-y-4">
                <p class="text-xs text-gray-500">Inserisci la tua password attuale per confermare l'identità, poi scegli la nuova password.</p>
                <div>
                    <label for="current_password" class="form-label">Password attuale</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password" class="input-field @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="form-label">Nuova password</label>
                        <input id="new_password" type="password" name="new_password" autocomplete="new-password" class="input-field @error('new_password') border-red-400 @enderror">
                        @error('new_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="form-label">Conferma nuova password</label>
                        <input id="new_password_confirmation" type="password" name="new_password_confirmation" autocomplete="new-password" class="input-field">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary">Salva modifiche</button>

        <hr class="border-gray-300">
    </form>

    {{-- Zona pericolo: eliminazione account --}}
    <div class="mt-12 border border-red-300 p-6">
        <h2 class="text-sm font-semibold tracking-wider uppercase text-red-400 mb-2">Elimina account</h2>
        <p class="text-sm text-gray-500 mb-5">
            Eliminando l'account non potrai più accedere alla tua area personale. I dati degli ordini precedenti verranno conservati.
            Riceverai un'email con un link per annullare l'operazione entro 7 giorni.
        </p>

        <button type="button" id="delete-toggle-btn"
                class="text-xs font-semibold tracking-wider uppercase text-red-400 hover:text-red-600 transition-colors border border-red-200 hover:border-red-400 px-5 py-2.5 cursor-pointer">
            Procedi
        </button>

        <form id="delete-account-form" method="POST" action="{{ route('account.destroy') }}"
            class="hidden mt-6 space-y-4">
            @csrf @method('DELETE')
            <div>
                <label for="delete_password" class="form-label">Conferma con la tua password</label>
                <input id="delete_password" type="password" name="password" required autocomplete="current-password"
                    placeholder="La tua password attuale"
                    class="input-field @error('password', 'deleteAccount') border-red-400 @enderror">
                @error('password', 'deleteAccount')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-danger">
                Elimina il mio account
            </button>
        </form>
    </div>

</div>
@endsection
