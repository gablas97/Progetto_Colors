@extends('layouts.app')

@section('title', 'Colors S.r.l. - Cartoleria, Stampe e Articoli Regalo a Taranto')

@section('content')

    {{-- ===== HERO ===== --}}
    <section class="relative h-[420px] md:h-[520px] bg-gray-200 overflow-hidden flex items-end justify-center pb-12"
             style="background-image: url('{{ asset('images/hero.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 text-center">
            <a href="{{ route('products.index') }}" class="btn-white">
                Scopri i nostri prodotti
            </a>
        </div>
    </section>

    {{-- ===== QUOTE ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <blockquote class="text-3xl md:text-4xl font-light leading-snug text-gray-800 mb-6">
                        Sogna la tua vita a colori.<br>
                        <em>È il segreto della felicità.</em>
                    </blockquote>
                    <p class="text-sm text-gray-400 mb-10">- Walt Disney</p>
                    <a href="{{ route('products.index') }}" class="btn-outline-dark">
                        Scopri i nostri prodotti
                    </a>
                </div>
                <div class="overflow-hidden">
                    <img src="{{ asset('images/quote-image.png') }}"
                        alt="Colori e creatività"
                        class="w-full h-full object-cover"
                        data-hide-on-error>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== SERVIZI ===== --}}
    <section class="py-16 bg-primary/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10">
                I nostri <span class="underline-accent">servizi</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach([
                    ['Stampe / fotocopie', 'Stampe A3, A4, A5, fotocopie, stampe su cartoncino lucido e fotografie.', 'heroicon-printer'],
                    ['Grafica personalizzata', 'Bigliettini da visita, buoni regalo, voucher e materiale promozionale.', 'heroicon-sparkles'],
                    ['Timbri', 'Realizzazione di timbri personalizzati per uso professionale e personale.', 'heroicon-stamp'],
                ] as $service)
                <div class="bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-gray-900 mb-3">{{ $service[0] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $service[1] }}</p>
                    <a href="{{ route('services') }}" class="inline-block mt-6 text-xs font-semibold tracking-wider uppercase text-primary hover:text-primary-dark transition-colors">
                        Scopri di più →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== PRODOTTI / CATEGORIE ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10">
                I nostri <span class="underline-accent">prodotti</span>
            </h2>

            @php
            $shopCats = [
                ['name' => 'Cartoleria',         'slug' => 'cartoleria',         'img' => 'hero.jpg',  'wide' => true],
                ['name' => 'Cancelleria',         'slug' => 'cancelleria',        'img' => 'cat-cancelleria.jpg', 'wide' => false],
                ['name' => 'Ufficio',             'slug' => 'ufficio',            'img' => 'cat-ufficio.jpg',     'wide' => false],
                ['name' => 'Scuola',              'slug' => 'scuola',             'img' => 'cat-scuola.jpg',      'wide' => true],
                ['name' => 'Promozioni',          'slug' => 'promozioni',         'img' => 'cat-promozioni.jpg',  'wide' => true],
                ['name' => 'Articoli da regalo',  'slug' => 'articoli-da-regalo', 'img' => 'cat-regalo.jpg',      'wide' => false],
            ];
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3" style="grid-auto-rows:220px">
                @foreach($shopCats as $cat)
                <a href="{{ route('products.index', ['categoria' => $cat['slug']]) }}"
                   class="cat-card relative overflow-hidden bg-gray-100 block {{ $cat['wide'] ? 'lg:col-span-2' : '' }}">

                    <img src="{{ asset('images/' . $cat['img']) }}"
                         alt="{{ $cat['name'] }}"
                         class="cat-card-img absolute inset-0 w-full h-full object-cover"
                         data-hide-on-error>

                    <div class="absolute inset-0"
                         style="background:linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 60%, transparent 100%)"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
                        <h3 class="text-white font-semibold leading-tight {{ $cat['wide'] ? 'text-xl md:text-2xl' : 'text-sm md:text-base' }}">
                            {{ $cat['name'] }}
                        </h3>
                        <p class="cat-card-label text-xs mt-2">Scopri di più</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== PRODOTTI IN EVIDENZA ===== --}}
    @if($featured->isNotEmpty())
    <section class="py-16 bg-primary-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10">In evidenza</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($featured as $product)
                    <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistIds)" />
                @endforeach
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="btn-outline-dark">
                    Vedi tutti i prodotti
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== CTA NEGOZIO ===== --}}
    <section class="relative flex items-center justify-end overflow-hidden"
            style="height:500px; background-image: url('{{ asset('images/store-cta.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0"></div>
        <div class="relative z-10 text-center" style="margin-left:auto; margin-right:25%; width:280px;">
            <h2 class="text-3xl md:text-4xl font-light text-white mb-6">
                Visita il nostro negozio
            </h2>
            <a href="https://maps.app.goo.gl/8gU5VBw9cQyEzHc87" target="_blank" rel="noopener" class="btn-outline-white">
                Come raggiungerci
            </a>
        </div>
    </section>

    {{-- ===== FAQ ===== --}}
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                {{-- Info azienda --}}
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-2">Chi siamo</p>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Colors S.r.l.</h2>
                    <p class="text-sm text-gray-500 leading-relaxed mb-4">Via Umbria 35, 74121 Taranto</p>
                    <p class="text-sm text-gray-500 leading-relaxed mb-1">Lun – Ven: 08:00 – 13:30 / 16:30 – 20:00</p>
                    <p class="text-sm text-gray-500 leading-relaxed">Sabato: 08:00 – 13:00</p>
                </div>

                {{-- FAQ accordion --}}
                <div class="space-y-0 divide-y divide-gray-100">
                    @foreach([
                        ['Fate bigliettini personalizzati?', 'Sì, realizziamo bigliettini da visita, buoni regalo e voucher personalizzati. Contattaci per un preventivo.'],
                        ['Quanto tempo ci vuole per la consegna?', 'I tempi variano in base al prodotto. Per gli ordini online la spedizione avviene entro 2–5 giorni lavorativi.'],
                        ['Fate spedizioni in tutta Italia?', 'Sì, spediamo su tutto il territorio nazionale. Offriamo anche la ricezione diretta in negozio.'],
                        ['Posso ricevere le fotocopie a casa?', 'Sì! Offriamo il servizio di stampa e fotocopie con spedizione a domicilio per ordini superiori a 15 €.'],
                    ] as [$question, $answer])
                    <details class="group py-5 cursor-pointer">
                        <summary class="flex justify-between items-center text-sm font-semibold text-gray-800 list-none">
                            {{ $question }}
                            <svg class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </summary>
                        <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $answer }}</p>
                    </details>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

@endsection
