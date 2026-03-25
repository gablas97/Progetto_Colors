@extends('layouts.app')
@section('title', '404 — Pagina non trovata')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-md">
        <p class="text-7xl font-bold text-primary mb-4">404</p>
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Pagina non trovata</h1>
        <p class="text-gray-500 text-sm mb-8">La pagina che cerchi non esiste o è stata spostata.</p>
        <a href="{{ route('home') }}" class="btn-primary">Torna alla home</a>
    </div>
</div>
@endsection
