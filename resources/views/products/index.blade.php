@extends('layouts.app')

@section('title', ($activeCategory ? $activeCategory->name . ' — ' : '') . 'Prodotti — Colors S.r.l.')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb + filtri --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
            <span class="mx-1.5">&rsaquo;</span>
            @if($activeCategory)
                <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">Prodotti</a>
                <span class="mx-1.5">&rsaquo;</span>
                <span class="text-gray-800 font-medium">{{ $activeCategory->name }}</span>
            @else
                <span class="text-gray-800 font-medium">Prodotti</span>
            @endif
        </nav>

        {{-- Filtri --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
            <select name="categoria"
                    onchange="this.form.submit()"
                    class="border border-gray-200 text-sm px-3 py-2 text-gray-700 focus:outline-none focus:border-primary bg-white">
                <option value="">Tutte le categorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('categoria') === $cat->slug)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="marca"
                    onchange="this.form.submit()"
                    class="border border-gray-200 text-sm px-3 py-2 text-gray-700 focus:outline-none focus:border-primary bg-white">
                <option value="">Tutte le marche</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->slug }}" @selected(request('marca') === $brand->slug)>
                        {{ $brand->name }}
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

            @if(request()->hasAny(['categoria', 'marca', 'q']))
                <a href="{{ route('products.index') }}" class="text-xs text-gray-400 hover:text-red-400 transition-colors whitespace-nowrap">
                    ✕ Azzera
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

        {{-- Paginazione --}}
        @if($products->hasPages())
            <div class="mt-12">
                {{ $products->links('pagination::tailwind') }}
            </div>
        @endif
    @else
        <div class="text-center py-24">
            <p class="text-gray-400 text-sm">Nessun prodotto trovato con i filtri selezionati.</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-4 text-xs font-semibold tracking-wider uppercase text-primary hover:text-primary-dark transition-colors">
                Vedi tutti i prodotti
            </a>
        </div>
    @endif

</div>

@endsection
