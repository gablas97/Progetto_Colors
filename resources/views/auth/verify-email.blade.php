@extends('layouts.app')
@section('title', 'Verifica la tua email - Colors S.r.l.')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-md text-center">

        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-3">Verifica la tua email</h1>
        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            Ti abbiamo inviato un link di verifica all'indirizzo <strong class="text-gray-700">{{ auth()->user()->email }}</strong>.
            Controlla la posta in arrivo (e la cartella spam) e clicca il link per attivare il tuo account.
        </p>

        @if(session('resent'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 mb-6">
                Email inviata di nuovo. Controlla la tua casella di posta.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
            @csrf
            <button type="submit" class="btn-primary w-full text-center block">
                Invia di nuovo l'email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                Esci dall'account
            </button>
        </form>

    </div>
</div>
@endsection
