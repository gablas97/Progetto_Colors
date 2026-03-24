@extends('layouts.app')

@section('title', 'Servizi — Colors S.r.l.')
@section('description', 'Stampe, fotocopie, grafica personalizzata e timbri — Colors S.r.l. Taranto.')

@section('content')

    {{-- ===== STAMPE E FOTOCOPIE ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-3xl font-semibold text-primary mb-6">Stampe e fotocopie</h1>
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
                         onerror="this.style.display='none'">
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
            <a href="mailto:colorstarantosrl@gmail.com" class="btn-primary">
                Contattaci
            </a>
        </div>
    </section>

@endsection
