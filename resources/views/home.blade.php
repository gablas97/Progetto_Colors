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
    <section class="py-16 bg-primary-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10">
                I nostri <span class="underline-accent">servizi</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($services as $service)
                <div class="bg-white shadow-sm hover:shadow-md transition-shadow flex flex-col text-center" style="min-height:460px">
                    <div class="flex-1 px-8 pt-12 pb-8">
                        <div class="flex justify-center mb-5">
                            @if($service['icon'] === 'printer')
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                                </svg>
                            @elseif($service['icon'] === 'sparkles')
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.745 3.745 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/>
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $service['name'] }}</h3>
                    </div>
                    <div class="service-divider"></div>
                    <div class="flex-1 flex flex-col justify-end px-8 pt-8 pb-12">
                        <p class="text-sm text-gray-500 leading-relaxed mb-6">{{ $service['description'] }}</p>
                        <a href="{{ route('services') }}" class="text-xs font-semibold tracking-wider uppercase text-primary hover:text-primary-dark transition-colors">
                            Scopri di più
                        </a>
                    </div>
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

            <div class="categories-grid grid grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($shopCats as $cat)
                <a href="{{ route('products.index', ['categoria' => $cat['slug']]) }}"
                   class="cat-card relative overflow-hidden bg-gray-100 block {{ $cat['wide'] ? 'col-span-2' : '' }}">

                    <img src="{{ asset('images/' . $cat['img']) }}"
                         alt="{{ $cat['name'] }}"
                         class="cat-card-img absolute inset-0 w-full h-full object-cover"
                         data-hide-on-error>

                    <div class="absolute inset-0" style="background:rgba(0,0,0,0.5)"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
                        <h3 class="text-white font-semibold leading-tight uppercase {{ $cat['wide'] ? 'text-2xl lg:text-3xl' : 'text-sm lg:text-base' }}">
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
    <section class="py-16 bg-primary-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-carousel>
            <div class="flex items-center justify-between mb-10">
                <h2 class="section-heading mb-10">
                    In <span class="underline-accent">evidenza</span>
                </h2>
                <div class="flex items-center gap-2">
                    <button data-carousel-prev
                            class="w-9 h-9 flex items-center justify-center border border-gray-600 text-gray-600 hover:border-gray-900 hover:text-gray-900 transition-colors cursor-pointer"
                            aria-label="Precedente">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button data-carousel-next
                            class="w-9 h-9 flex items-center justify-center border border-gray-600 text-gray-600 hover:border-gray-900 hover:text-gray-900 transition-colors cursor-pointer"
                            aria-label="Successivo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

                <div class="carousel-track">
                    @foreach($featured as $product)
                        <div class="carousel-card">
                            <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistIds)" />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('products.index') }}" class="btn-outline-dark">
                    Vedi tutti i prodotti
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== DICONO DI NOI ===== --}}
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="section-heading mb-10">
                Dicono di <span class="underline-accent">noi</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($reviews as $review)
                <div class="bg-white p-6 shadow-sm text-sm text-gray-600 leading-relaxed">
                    <p class="italic">"{{ $review['text'] }}"</p>
                    <p class="mt-4 text-xs font-semibold text-gray-400 not-italic">— {{ $review['author'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

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
                    <details class="group py-5">
                        <summary class="flex justify-between items-center text-sm font-semibold text-gray-800 list-none cursor-pointer">
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
