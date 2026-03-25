@extends('layouts.app')
@section('title', '500 — Errore del server')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <p class="text-7xl font-bold text-primary mb-4">500</p>
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Errore del server</h1>
        <p class="text-gray-500 text-sm mb-8">Si è verificato un errore imprevisto. Riprova tra qualche minuto.</p>
        <a href="{{ route('home') }}" class="btn-primary">Torna alla home</a>
    </div>
</div>
@endsection
