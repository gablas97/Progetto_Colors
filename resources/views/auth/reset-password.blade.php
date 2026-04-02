@extends('layouts.app')
@section('title', 'Nuova password - Colors S.r.l.')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <div class="text-center mb-10">
            <h1 class="text-2xl font-semibold text-gray-900">Imposta nuova password</h1>
            <p class="text-sm text-gray-500 mt-2">Scegli una password sicura di almeno 8 caratteri.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $email) }}"
                       required autocomplete="email"
                       class="input-field @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="form-label">Nuova password</label>
                <input type="password" id="password" name="password"
                       required autocomplete="new-password"
                       class="input-field @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Conferma password</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password"
                       class="input-field">
            </div>

            <button type="submit" class="btn-primary w-full text-center block">
                Reimposta password
            </button>
        </form>

    </div>
</div>
@endsection
