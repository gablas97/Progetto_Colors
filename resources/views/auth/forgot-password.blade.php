@extends('layouts.app')
@section('title', 'Reimposta password - Colors S.r.l.')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <div class="text-center mb-10">
            <h1 class="text-2xl font-semibold text-gray-900">Password dimenticata?</h1>
            <p class="text-sm text-gray-500 mt-2">
                Inserisci la tua email e ti manderemo un link per reimpostare la password.
            </p>
        </div>

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

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

            <button type="submit" class="btn-primary w-full text-center block">
                Invia link di reset
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            <a href="{{ route('login') }}" class="hover:text-gray-600 transition-colors">Torna al login</a>
        </p>

    </div>
</div>
@endsection
