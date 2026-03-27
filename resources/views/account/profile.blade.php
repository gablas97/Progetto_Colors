@extends('layouts.app')
@section('title', 'Profilo - Colors S.r.l.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

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
                <label class="form-label">Nome</label>
                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required class="input-field @error('first_name') border-red-400 @enderror">
                @error('first_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Cognome</label>
                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required class="input-field @error('last_name') border-red-400 @enderror">
                @error('last_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="form-label">Email</label>
            <input type="email" value="{{ $user->email }}" disabled class="input-field bg-gray-50 text-gray-400 cursor-not-allowed">
            <p class="text-xs text-gray-400 mt-1">L'email non può essere modificata.</p>
        </div>

        <div>
            <label class="form-label">Telefono</label>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field">
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="newsletter_subscribed" class="accent-primary" @checked($user->newsletter_subscribed)>
            <span class="text-xs text-gray-600">Iscritto alla newsletter</span>
        </label>

        <hr class="border-gray-100">

        <p class="text-xs font-semibold tracking-wider uppercase text-gray-400">Cambia password <span class="font-normal text-gray-400 normal-case tracking-normal">(lascia vuoto per non modificarla)</span></p>

        <div>
            <label class="form-label">Password attuale</label>
            <input type="password" name="current_password" class="input-field @error('current_password') border-red-400 @enderror">
            @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nuova password</label>
                <input type="password" name="new_password" class="input-field @error('new_password') border-red-400 @enderror">
                @error('new_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Conferma nuova password</label>
                <input type="password" name="new_password_confirmation" class="input-field">
            </div>
        </div>

        <button type="submit" class="btn-primary">Salva modifiche</button>
    </form>

    {{-- Zona pericolo: eliminazione account --}}
    <div class="mt-12 border border-red-100 p-6">
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
                <label class="form-label">Conferma con la tua password</label>
                <input type="password" name="password" required
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
