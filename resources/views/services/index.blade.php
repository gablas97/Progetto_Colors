@extends('layouts.app')

@section('title', 'Servizi - Colors S.r.l.')
@section('description', 'Stampe, fotocopie, grafica personalizzata e timbri - Colors S.r.l. Taranto.')

@section('content')

    {{-- ===== INTRO ===== --}}
    <section class="flex items-center justify-center text-center relative overflow-hidden" style="min-height:500px; background-image:url('{{ asset('images/services-hero.jpg') }}'); background-size:cover; background-position:center;">
        <div class="absolute inset-0 bg-white/80"></div>
        <div class="relative z-10 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-2xl font-semibold tracking-widest uppercase text-primary mb-4">I nostri servizi</p>
            <h1 class="font-semibold text-3xl text-gray-900 leading-tight mb-6">
                Molto più di una cartoleria
            </h1>
            <p class="text-gray-500 leading-relaxed">
                Da Colors non ci limitiamo a vendere prodotti — offriamo servizi pensati per semplificarti la vita.
                Stampe professionali, fotocopie, grafica personalizzata e timbri su misura:
                tutto sotto lo stesso tetto, con la cura e l'attenzione che ci contraddistinguono da sempre.
                Che tu sia un privato, uno studente o un professionista, siamo qui per aiutarti.
            </p>
        </div>
    </section>

    {{-- ===== STAMPE E FOTOCOPIE ===== --}}
    <section class="py-16 md:py-24 bg-primary-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-6">Stampe e fotocopie</h2>
                    <ul class="space-y-2 text-sm text-gray-700 mb-6">
                        @foreach(['Stampe formato A3, A4, A5', 'Fotocopie formato A3 – A4', 'Stampe grandi formati', 'Stampe su cartoncino lucido', 'Stampe di fotografie'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="bullet"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-600 mt-8">
                        Ricevi le tue fotocopie direttamente a casa<br>
                        <span class="text-gray-500 font-normal normal-case tracking-normal">con un minimo di 15 € di spesa</span>
                    </p>
                </div>
                <div class="aspect-[4/3] bg-gray-200 overflow-hidden">
                    <img src="{{ asset('images/services-print.png') }}"
                         alt="Stampe e fotocopie"
                         class="w-full h-full object-cover"
                         data-hide-on-error>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== GRAFICA ===== --}}
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="aspect-[4/3] bg-gray-200 overflow-hidden">
                    <img src="{{ asset('images/services-graphic.jpg') }}"
                         alt="Grafica personalizzata"
                         class="w-full h-full object-cover"
                         data-hide-on-error>
                </div>
                <div>
                    <h2 class="text-3xl font-semibold text-primary mb-6">Grafica personalizzata</h2>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        @foreach(['Bigliettini da visita', 'Buoni regalo', 'Voucher', 'Locandine e volantini', 'Materiale promozionale'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Realizziamo materiali grafici su misura per la tua attività o per occasioni speciali.<br>
                        Contattaci per un preventivo personalizzato.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== TIMBRI ===== --}}
    <section class="py-16 md:py-24 bg-primary-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-semibold text-gray-900 mb-6">Timbri</h2>
                    <ul class="space-y-2 text-sm text-gray-700 mb-6">
                        @foreach(['Timbri in legno', 'Timbri autoinchiostranti', 'Timbri con logo personalizzato', 'Timbri datari', 'Timbri per uso professionale e personale'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="bullet"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-600 mt-8">
                        Passa in negozio o contattaci<br>
                        <span class="text-gray-500 font-normal normal-case tracking-normal">per maggiori informazioni e preventivi</span>
                    </p>
                </div>
                <div class="aspect-[4/3] bg-gray-200 overflow-hidden">
                    <img src="{{ asset('images/services-stamps.jpg') }}"
                         alt="Timbri"
                         class="w-full h-full object-cover"
                         data-hide-on-error>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CONTATTO ===== --}}
    <section class="py-24 md:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Hai bisogno di un preventivo?</h2>
            <p class="text-sm text-gray-500 mb-8">Scrivici o passa in negozio, siamo sempre felici di aiutarti.</p>
            <a href="mailto:colorstarantosrl@gmail.com" class="btn-primary">Contattaci</a>
        </div>
    </section>

@endsection
