@extends('layouts.app')

@section('title', $service->name . ' - Colors S.r.l.')
@section('description', $service->short_description ?? $service->name)

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
        <span class="mx-1.5">&rsaquo;</span>
        <a href="{{ route('services') }}" class="hover:text-primary transition-colors">Servizi</a>
        <span class="mx-1.5">&rsaquo;</span>
        <span class="text-gray-800 font-medium">{{ $service->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

        {{-- Immagine --}}
        <div>
            @if($service->main_image)
                <div class="bg-gray-50 overflow-hidden h-64 sm:h-80 lg:h-96">
                    <img src="{{ Storage::url($service->main_image) }}"
                         alt="{{ $service->name }}"
                         class="w-full h-full object-cover">
                </div>
            @endif

            @if($service->images->isNotEmpty())
                <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                    @foreach($service->images as $img)
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-50 overflow-hidden">
                            <img src="{{ Storage::url($img->image) }}"
                                 alt="{{ $img->alt_text ?? $service->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Dettagli --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h1>

            @if($service->price)
                <p class="text-lg font-medium text-primary mb-6">
                    a partire da € {{ number_format($service->price, 2, ',', '.') }}
                </p>
            @endif

            @if($service->short_description)
                <p class="text-gray-600 text-sm leading-relaxed mb-6">{{ $service->short_description }}</p>
            @endif

            <a href="mailto:colorstarantosrl@gmail.com?subject=Richiesta+{{ urlencode($service->name) }}"
               class="btn-primary block text-center w-full mb-3">
                Richiedi informazioni
            </a>
            <a href="{{ route('services') }}" class="block text-center text-sm text-gray-500 hover:text-primary transition-colors">
                ← Torna ai servizi
            </a>
        </div>
    </div>

    {{-- Descrizione estesa --}}
    @if($service->description && strip_tags($service->description))
        <div class="mt-12 border-t border-gray-100 pt-10">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrizione</h2>
            <div class="prose prose-sm max-w-none text-gray-600">
                {!! $service->description !!}
            </div>
        </div>
    @endif

</div>
@endsection
