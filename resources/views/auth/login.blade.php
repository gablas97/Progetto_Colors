@extends('layouts.app')

@section('title', 'Accedi - Colors S.r.l.')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md">

        <div class="text-center mb-10">
            <h1 class="text-2xl font-semibold text-gray-900">Accedi al tuo account</h1>
            <p class="text-sm text-gray-500 mt-2">Non hai un account?
                <a href="{{ route('register') }}" class="text-primary hover:text-primary-dark font-medium transition-colors">Registrati</a>
            </p>
        </div>

        @if(session('status'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
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

            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       required autocomplete="current-password"
                       class="input-field @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="accent-primary">
                    <span class="text-xs text-gray-600">Ricordami</span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full text-center block">
                Accedi
            </button>
        </form>

    </div>
</div>

@endsection
