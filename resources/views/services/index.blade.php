@extends('layouts.app')

@section('title', 'Servizi - Colors S.r.l.')
@section('description', 'Stampe, fotocopie, grafica personalizzata e timbri - Colors S.r.l. Taranto.')

@section('content')

    {{-- ===== SERVIZI DAL CATALOGO ===== --}}
    @if($services->isNotEmpty())
    <section class="py-16 md:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="section-heading mb-2">I nostri servizi</h1>
            <p class="text-gray-500 text-sm mb-10">Clicca su un servizio per scoprire i dettagli e il listino prezzi.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                <a href="{{ route('services.show', $service->slug) }}"
                   class="group block border border-gray-100 hover:border-primary hover:shadow-sm transition-all">
                    @if($service->main_image)
                        <div class="aspect-[4/3] overflow-hidden bg-gray-50">
                            <img src="{{ Storage::url($service->main_image) }}"
                                 alt="{{ $service->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="aspect-[4/3] bg-primary-50 flex items-center justify-center">
                            <svg class="w-12 h-12 text-primary-light" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="p-5">
                        <h2 class="font-semibold text-gray-900 group-hover:text-primary transition-colors mb-1">{{ $service->name }}</h2>
                        @if($service->short_description)
                            <p class="text-sm text-gray-500 line-clamp-2">{{ $service->short_description }}</p>
                        @endif
                        @if($service->price)
                            <p class="text-sm font-medium text-primary mt-3">a partire da € {{ number_format($service->price, 2, ',', '.') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    <hr class="border-gray-100">
    @endif

    {{-- ===== STAMPE E FOTOCOPIE ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-semibold text-primary mb-6">Stampe e fotocopie</h2>
                    <ul class="space-y-2 text-sm text-gray-600 mb-6">
                        @foreach(['Stampe formato A3, A4, A5', 'Fotocopie formato A3 – A4', 'Stampe grandi formati', 'Stampe su cartoncino lucido', 'Stampe di fotografie'] as $item)
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-500 mt-8">
                        Ricevi le tue fotocopie direttamente a casa<br>
                        <span class="text-gray-400 font-normal normal-case tracking-normal">con un minimo di 15 € di spesa</span>
                    </p>
                </div>
                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                    <img src="{{ asset('images/services-print.jpg') }}"
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
            <div class="max-w-xl mx-auto text-center">
                <h2 class="text-3xl font-semibold text-primary mb-6">Grafica personalizzata</h2>
                <ul class="space-y-2 text-sm text-gray-600 inline-block text-left">
                    @foreach(['Bigliettini da visita', 'Buoni regalo', 'Voucher', 'Locandine e volantini', 'Materiale promozionale'] as $item)
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0"></span>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <p class="text-sm text-gray-500 mt-8 leading-relaxed">
                    Realizziamo materiali grafici su misura per la tua attività o per occasioni speciali.<br>
                    Contattaci per un preventivo personalizzato.
                </p>
            </div>
        </div>
    </section>

    {{-- ===== TIMBRI ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-xl">
                <h2 class="text-3xl font-semibold text-primary mb-6">Timbri</h2>
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    Realizziamo timbri personalizzati per uso professionale e personale: timbri in legno, autoinchiostranti, timbri con logo, timbri datari e molto altro.
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Passa in negozio o contattaci per maggiori informazioni.
                </p>
            </div>
        </div>
    </section>

    {{-- ===== DICONO DI NOI ===== --}}
    <section class="py-16 bg-primary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10 text-center">Dicono di noi</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    'Persona gentilissima, cartoleria super accogliente con ottimi prodotti tra gadget e cancelleria. La mia cartoleria di fiducia ❤️',
                    'Da Colors trovo sempre efficienza e gentilezza: è sempre un piacere tornare! Oltre alla vasta scelta di prodotti di cartoleria, ci sono anche tante idee regalo originali. Consigliatissimo!',
                    'È un\'ottima cartoleria e non ha costi alti. Le fotocopie hanno un ottimo prezzo soprattutto se occorre fare molte copie. Le signore sono precise e molto gentili.',
                    'Cortesia ed efficienza al primo posto 🌟',
                    'Da Colors trovo sempre professionalità, grande disponibilità ed estrema cortesia.',
                ] as $review)
                <div class="bg-white p-6 shadow-sm text-sm text-gray-600 leading-relaxed italic">
                    "{{ $review }}"
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== CONTATTO ===== --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Hai bisogno di un preventivo?</h2>
            <p class="text-sm text-gray-500 mb-8">Scrivici o passa in negozio, siamo sempre felici di aiutarti.</p>
            <a href="mailto:colorstarantosrl@gmail.com" class="btn-primary">Contattaci</a>
        </div>
    </section>

@endsection
