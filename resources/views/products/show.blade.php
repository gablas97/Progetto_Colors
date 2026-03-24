@extends('layouts.app')

@section('title', $product->name . ' — Colors S.r.l.')
@section('description', $product->short_description ?? $product->name)

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
        <span class="mx-1.5">&rsaquo;</span>
        <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">Prodotti</a>
        @foreach($product->categories->take(1) as $cat)
            <span class="mx-1.5">&rsaquo;</span>
            <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}" class="hover:text-primary transition-colors">{{ $cat->name }}</a>
        @endforeach
        <span class="mx-1.5">&rsaquo;</span>
        <span class="text-gray-800 font-medium">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">

        {{-- ===== GALLERIA ===== --}}
        <div>
            {{-- Immagine principale --}}
            <div class="relative aspect-square bg-gray-50 overflow-hidden mb-3">
                @php
                    $mainImage = $product->main_image ? Storage::url($product->main_image) : null;
                    $gallery   = $product->images->map(fn($i) => Storage::url($i->image))->prepend($mainImage)->filter()->values();
                @endphp

                @if($mainImage)
                    <img id="main-image"
                         src="{{ $mainImage }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-contain">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                {{-- Wishlist --}}
                <button class="absolute top-3 right-3 w-9 h-9 bg-white rounded-full shadow flex items-center justify-center hover:scale-110 transition-transform"
                        aria-label="Aggiungi alla wishlist"
                        data-action="wishlist"
                        data-product-id="{{ $product->id }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Thumbnails --}}
            @if($gallery->count() > 1)
            <div class="flex gap-2 overflow-x-auto pb-1">
                @foreach($gallery as $i => $imgUrl)
                    <button onclick="document.getElementById('main-image').src='{{ $imgUrl }}'"
                            class="flex-shrink-0 w-16 h-16 border-2 border-transparent hover:border-primary transition-colors overflow-hidden bg-gray-50">
                        <img src="{{ $imgUrl }}" alt="Immagine {{ $i + 1 }}" class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ===== INFO PRODOTTO ===== --}}
        <div class="flex flex-col">
            {{-- Brand --}}
            @if($product->brand)
                <p class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-2">{{ $product->brand->name }}</p>
            @endif

            <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 leading-snug mb-4">
                {{ $product->name }}
            </h1>

            <hr class="border-gray-100 mb-4">

            {{-- Prezzo --}}
            <div class="flex items-baseline gap-3 mb-6">
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="text-lg text-gray-400 line-through">
                        € {{ number_format($product->compare_at_price, 2, ',', '.') }}
                    </span>
                @endif
                <span class="text-2xl font-semibold text-gray-900">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </span>
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    @php $discount = round((1 - $product->price / $product->compare_at_price) * 100) @endphp
                    <span class="bg-primary text-white text-xs font-semibold px-2 py-0.5">-{{ $discount }}%</span>
                @endif
            </div>

            {{-- Varianti --}}
            @if($product->variants->isNotEmpty())
            <div class="mb-6">
                <p class="text-xs font-semibold tracking-wider uppercase text-gray-500 mb-3">Variante</p>
                <div class="flex flex-wrap gap-2" id="variant-selector">
                    @foreach($product->variants as $variant)
                        <button type="button"
                                class="variant-btn border border-gray-200 px-3 py-1.5 text-xs text-gray-700 hover:border-primary hover:text-primary transition-colors"
                                data-variant-id="{{ $variant->id }}"
                                data-variant-name="{{ $variant->name }}"
                                @if(!$variant->isInStock()) disabled @endif>
                            {{ $variant->name }}
                            @if(!$variant->isInStock())
                                <span class="text-gray-300 ml-1">(esaurito)</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantità + CTA --}}
            <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-3 mb-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" id="selected-variant" value="">

                {{-- Selettore quantità --}}
                <div class="flex items-center border border-gray-200">
                    <button type="button" id="qty-minus"
                            class="px-3 py-2.5 text-gray-500 hover:text-gray-900 transition-colors text-lg leading-none"
                            aria-label="Diminuisci">−</button>
                    <input type="number" name="quantity" id="qty-input"
                           value="1" min="1" max="99"
                           class="w-12 text-center text-sm border-0 focus:outline-none py-2.5">
                    <button type="button" id="qty-plus"
                            class="px-3 py-2.5 text-gray-500 hover:text-gray-900 transition-colors text-lg leading-none"
                            aria-label="Aumenta">+</button>
                </div>

                <button type="submit" class="btn-primary flex-1 text-center">
                    Aggiungi al carrello
                </button>
            </form>

            {{-- Stock --}}
            @if($product->manage_stock)
                @if($product->stock_quantity > 0)
                    <p class="text-xs text-green-600 mb-4">✓ Disponibile ({{ $product->stock_quantity }} in magazzino)</p>
                @else
                    <p class="text-xs text-red-500 mb-4">✕ Esaurito</p>
                @endif
            @endif

            {{-- Descrizione breve --}}
            @if($product->short_description)
                <p class="text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-6">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- SKU --}}
            @if($product->sku)
                <p class="text-xs text-gray-400 mt-4">SKU: {{ $product->sku }}</p>
            @endif
        </div>
    </div>

    {{-- ===== DESCRIZIONE COMPLETA ===== --}}
    @if($product->description)
    <div class="mt-16 border-t border-gray-100 pt-12">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrizione</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            {!! nl2br(e($product->description)) !!}
        </div>
    </div>
    @endif

    {{-- ===== PRODOTTI CORRELATI ===== --}}
    @if($related->isNotEmpty())
    <div class="mt-16 border-t border-gray-100 pt-12">
        <h2 class="section-heading mb-8">Potrebbe interessarti</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($related as $relProduct)
                <x-product-card :product="$relProduct" />
            @endforeach
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // Selettore quantità
    const qtyInput = document.getElementById('qty-input');
    document.getElementById('qty-minus').addEventListener('click', () => {
        if (qtyInput.value > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
    });
    document.getElementById('qty-plus').addEventListener('click', () => {
        if (qtyInput.value < 99) qtyInput.value = parseInt(qtyInput.value) + 1;
    });

    // Selettore variante
    document.querySelectorAll('.variant-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('border-primary', 'text-primary'));
            btn.classList.add('border-primary', 'text-primary');
            document.getElementById('selected-variant').value = btn.dataset.variantId;
        });
    });
</script>
@endpush

@endsection
