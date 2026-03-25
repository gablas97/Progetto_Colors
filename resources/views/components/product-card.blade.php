@props(['product', 'wishlisted' => false])

<div class="group">
    {{-- Immagine + wishlist --}}
    <div class="relative aspect-square overflow-hidden bg-gray-50">
        <a href="{{ route('products.show', $product->slug) }}">
            @if($product->main_image)
                <img src="{{ Storage::url($product->main_image) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </a>

        {{-- Badge esaurito --}}
        @if(!$product->isInStock())
            <div class="absolute inset-0 bg-white/60 flex items-center justify-center pointer-events-none">
                <span class="bg-gray-800 text-white text-xs font-medium px-3 py-1 tracking-wide uppercase">Esaurito</span>
            </div>
        @endif

        {{-- Wishlist --}}
        <button
            class="absolute top-2.5 right-2.5 w-8 h-8 bg-white rounded-full shadow-sm flex items-center justify-center hover:scale-110 transition-transform cursor-pointer"
            aria-label="Aggiungi alla wishlist"
            data-action="wishlist"
            data-product-id="{{ $product->id }}">
            <svg class="w-4 h-4 transition-colors {{ $wishlisted ? 'text-red-400 fill-red-400' : 'text-gray-400' }}" fill="{{ $wishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    </div>

    {{-- Varianti (pallini) --}}
    @if($product->variants->isNotEmpty())
        <div class="flex gap-1 mt-2">
            @foreach($product->variants->take(5) as $variant)
                <span
                    class="w-3.5 h-3.5 rounded-full border border-gray-200 bg-gray-300 cursor-default"
                    title="{{ $variant->name }}">
                </span>
            @endforeach
            @if($product->variants->count() > 5)
                <span class="text-[10px] text-gray-400 self-center">+{{ $product->variants->count() - 5 }}</span>
            @endif
        </div>
    @else
        <div class="mt-2 h-3.5"></div>
    @endif

    {{-- Info prodotto --}}
    <div class="mt-1.5 flex items-start justify-between gap-2">
        <div class="min-w-0">
            <a href="{{ route('products.show', $product->slug) }}"
               class="block text-sm font-medium text-gray-800 hover:text-primary transition-colors leading-snug line-clamp-2">
                {{ $product->name }}
            </a>
            <div class="flex items-baseline gap-2 mt-1">
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="text-xs text-gray-400 line-through">
                        € {{ number_format($product->compare_at_price, 2, ',', '.') }}
                    </span>
                @endif
                <span class="text-sm font-semibold text-gray-900">
                    € {{ number_format($product->price, 2, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Aggiungi al carrello --}}
        <button
            class="flex-shrink-0 w-8 h-8 text-white flex items-center justify-center transition-colors {{ $product->isInStock() ? 'bg-gray-900 hover:bg-primary cursor-pointer' : 'bg-gray-300 cursor-not-allowed' }}"
            aria-label="Aggiungi al carrello"
            @if($product->isInStock()) data-action="add-to-cart" data-product-id="{{ $product->id }}" @endif
            @disabled(!$product->isInStock())>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </button>
    </div>
</div>
