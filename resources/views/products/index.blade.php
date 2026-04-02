@extends('layouts.app')

@section('title', ($activeCategory ? $activeCategory->name . ' - Colors S.r.l.' : 'Prodotti - Colors S.r.l.'))

@section('content')

@php
    $baseParams = array_filter([
        'marca' => request('marca'),
        'q'     => request('q'),
    ]);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Pill categorie - riga radice --}}
    <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 mb-2">Categorie</p>
    <div class="flex flex-wrap items-center gap-2 {{ empty($subLevels) ? 'mb-6' : 'mb-3' }}">
        <a href="{{ route('products.index', $baseParams) }}"
           class="pill {{ !$activeCategory ? 'pill-active' : '' }}">
            Tutte
        </a>
        @foreach($rootCategories as $cat)
            <a href="{{ route('products.index', array_merge($baseParams, ['categoria' => $cat->slug])) }}"
               class="pill {{ $activeRoot?->slug === $cat->slug ? 'pill-active' : '' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    {{-- Pill sottocategorie - uno o più livelli --}}
    @if(!empty($subLevels))
        <p class="text-xs font-semibold tracking-wider uppercase text-gray-400 mb-2">Sottocategorie</p>
        @foreach($subLevels as $level)
        <div class="flex flex-wrap items-center gap-2 {{ $loop->last ? 'mb-6' : 'mb-3' }}">
            <a href="{{ route('products.index', array_merge($baseParams, ['categoria' => $level['parentSlug']])) }}"
               class="pill {{ !$level['selected'] ? 'pill-active' : '' }}">
                Tutte
            </a>
            @foreach($level['items'] as $cat)
                <a href="{{ route('products.index', array_merge($baseParams, ['categoria' => $cat->slug])) }}"
                   class="pill {{ $level['selected']?->slug === $cat->slug ? 'pill-active' : '' }}">
                    {{ ucfirst(strtolower($cat->name)) }}
                </a>
            @endforeach
        </div>
        @endforeach
    @endif

    {{-- Filtri brand + ricerca --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2 flex-wrap">
            @if($activeCategory)
                <input type="hidden" name="categoria" value="{{ $activeCategory->slug }}">
            @endif

            <select name="marca"
                    data-auto-submit
                    class="border border-gray-200 text-sm px-3 py-2 text-gray-700 focus:outline-none focus:border-primary bg-white">
                <option value="">Tutte le marche</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->slug }}" @selected(request('marca') === $brand->slug)>
                        {{ ucfirst(strtolower($brand->name)) }}
                    </option>
                @endforeach
            </select>

            <div class="flex">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Cerca..."
                       class="border border-gray-200 text-sm px-3 py-2 text-gray-700 focus:outline-none focus:border-primary w-40 sm:w-56">
                <button type="submit" class="bg-gray-900 text-white px-3 py-2 hover:bg-primary transition-colors" aria-label="Cerca">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>

            @if(request('marca') || request('q'))
                <a href="{{ route('products.index', array_filter(['categoria' => request('categoria')])) }}"
                   class="text-xs text-gray-400 hover:text-red-400 transition-colors whitespace-nowrap">
                    ✕ Azzera filtri
                </a>
            @endif
        </form>
    </div>

    {{-- Griglia prodotti --}}
    @if($products->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
            @foreach($products as $product)
                <x-product-card :product="$product" :wishlisted="in_array($product->id, $wishlistIds)" />
            @endforeach
        </div>

        @if($products->hasPages())
            <div class="mt-12">
                {{ $products->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <div class="text-center py-24">
            <p class="text-gray-400 text-sm">Nessun prodotto trovato con i filtri selezionati.</p>
        </div>
    @endif

</div>

@endsection
