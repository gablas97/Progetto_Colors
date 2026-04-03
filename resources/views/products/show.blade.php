@extends('layouts.app')

@section('title', $product->name . ' - Colors S.r.l.')
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
            <div class="relative h-48 sm:h-64 md:h-72 lg:h-80 bg-gray-50 overflow-hidden mb-3">
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

                {{-- Badge esaurito --}}
                @if($product->manage_stock && !$product->isInStock())
                    <div class="absolute inset-0 bg-white/60 flex items-center justify-center pointer-events-none">
                        <span class="bg-gray-800 text-white text-sm font-medium px-4 py-1.5 tracking-wide uppercase">Esaurito</span>
                    </div>
                @endif

                {{-- Wishlist --}}
                <button class="absolute top-3 right-3 w-9 h-9 bg-white rounded-full shadow flex items-center justify-center hover:scale-110 transition-transform cursor-pointer"
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
                    <button data-thumb="{{ $imgUrl }}"
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
            @php
            $colorMap = ['rosso'=>'#e53e3e','blu'=>'#3182ce','verde'=>'#38a169','nero'=>'#1a202c','bianco'=>'#ffffff','giallo'=>'#ecc94b','arancione'=>'#ed8936','viola'=>'#805ad5','rosa'=>'#ed64a6','grigio'=>'#718096','marrone'=>'#744210','azzurro'=>'#63b3ed'];
            @endphp
            <div class="mb-6">
                <p class="text-xs font-semibold tracking-wider uppercase text-gray-500 mb-2">Variante: <span id="selected-variant-label" class="normal-case font-normal text-gray-700"></span></p>
                <div class="flex flex-wrap gap-2" id="variant-selector">
                    @foreach($product->variants as $variant)
                        @php $color = $colorMap[strtolower($variant->name)] ?? '#9ca3af'; @endphp
                        <button type="button"
                                class="variant-btn variant-swatch w-5 h-5"
                                style="background-color:{{ $color }}"
                                data-variant-id="{{ $variant->id }}"
                                data-variant-name="{{ $variant->name }}"
                                title="{{ $variant->name }}{{ !$variant->isInStock() ? ' (esaurito)' : '' }}"
                                @if(!$variant->isInStock()) disabled @endif>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantità + CTA --}}
            @php
                $outOfStock = $product->manage_stock && !$product->isInStock();
                $allVariantsOut = $product->variants->isNotEmpty() && $product->variants->every(fn($v) => !$v->isInStock());
                $disableCart = $outOfStock || $allVariantsOut;
            @endphp
            @if($product->variants->isNotEmpty() && !$disableCart)
            <p id="variant-required-msg" class="hidden text-xs text-red-500 mb-3">Seleziona una variante prima di aggiungere al carrello.</p>
            @endif
            <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-3 mb-6" id="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="product_variant_id" id="selected-variant" value="">

                {{-- Selettore quantità --}}
                <div class="flex items-center border border-gray-200 {{ $disableCart ? 'opacity-40' : '' }}">
                    <button type="button" id="qty-minus"
                            class="px-3 py-2.5 text-gray-500 text-lg leading-none {{ $disableCart ? 'cursor-not-allowed' : 'hover:text-gray-900 transition-colors cursor-pointer' }}"
                            aria-label="Diminuisci" {{ $disableCart ? 'disabled' : '' }}>−</button>
                    <input type="number" name="quantity" id="qty-input"
                           value="1" min="1" max="99"
                           class="w-12 text-center text-sm border-0 focus:outline-none py-2.5 {{ $disableCart ? 'cursor-not-allowed' : '' }}"
                           {{ $disableCart ? 'disabled' : '' }}>
                    <button type="button" id="qty-plus"
                            class="px-3 py-2.5 text-gray-500 text-lg leading-none {{ $disableCart ? 'cursor-not-allowed' : 'hover:text-gray-900 transition-colors cursor-pointer' }}"
                            aria-label="Aumenta" {{ $disableCart ? 'disabled' : '' }}>+</button>
                </div>

                <button type="submit"
                        class="{{ $disableCart ? 'inline-block px-8 py-3 text-xs font-semibold tracking-widest uppercase bg-gray-300 text-gray-500 cursor-not-allowed' : 'btn-primary' }}"
                        {{ $disableCart ? 'disabled' : '' }}>
                    {{ $disableCart ? 'Esaurito' : 'Aggiungi al carrello' }}
                </button>
            </form>


            {{-- Descrizione breve --}}
            @if($product->short_description)
                <p class="text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-6">
                    {{ $product->short_description }}
                </p>
            @endif
        </div>
    </div>

    {{-- ===== DESCRIZIONE COMPLETA ===== --}}
    @if($product->description && strip_tags($product->description))
    <div class="mt-16 border-t border-gray-100 pt-12">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrizione</h2>
        <div class="prose prose-sm max-w-none text-gray-600">
            {!! $product->description !!}
        </div>
    </div>
    @endif

    {{-- ===== RECENSIONI ===== --}}
    <div class="mt-16 border-t border-gray-100 pt-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Recensioni</h2>
                @if($reviewsCount > 0)
                    <div class="flex items-center gap-2">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($averageRating))
                                    <svg class="w-5 h-5 review-star-filled" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 review-star-empty" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.601a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm font-semibold text-gray-800">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-sm text-gray-400">({{ $reviewsCount }} recension{{ $reviewsCount === 1 ? 'e' : 'i' }})</span>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Nessuna recensione ancora. Sii il primo!</p>
                @endif
            </div>
        </div>

        {{-- Form recensione --}}
        <div id="recensioni"></div>
        @if(session('review_sent'))
            <div class="bg-green-50 border border-green-200 px-5 py-4 mb-8 flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">Recensione inviata!</p>
                    <p class="text-sm text-green-700 mt-0.5">Grazie per il tuo feedback. La tua recensione sarà pubblicata dopo l'approvazione.</p>
                </div>
            </div>
        @elseif($alreadyReviewed)
            <div class="bg-gray-50 border border-gray-200 px-5 py-4 mb-8 text-sm text-gray-600">
                Hai già lasciato una recensione per questo prodotto.
            </div>
        @elseif($canReview)
            <div class="border border-gray-100 p-6 mb-8">
                {{-- Banner incentivo sconto --}}
                <div class="bg-primary-50 border border-primary px-4 py-3 mb-6 flex items-center gap-3">
                    <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        @if(!$hasReceivedReward)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        @endif
                    </svg>
                    <p class="text-sm text-primary font-medium">
                        @if(!$hasReceivedReward)
                            <strong>Lascia una recensione e ricevi il 15% di sconto sul tuo prossimo ordine!</strong> (uno sconto per account)
                        @else
                            La tua opinione è importante per noi. Lascia una recensione su questo prodotto!
                        @endif
                    </p>
                </div>

                <h3 class="text-sm font-semibold text-gray-900 mb-4">Lascia una recensione</h3>

                <form action="{{ route('reviews.store', $product->slug) }}" method="POST">
                    @csrf

                    {{-- Selezione stelle --}}
                    <div class="mb-4">
                        <label class="form-label">Valutazione *</label>
                        <div class="flex gap-1" id="star-rating-selector">
                            @for($i = 1; $i <= 5; $i++)
                                <label for="star-{{ $i }}"
                                       class="star-label cursor-pointer transition-colors"
                                       data-value="{{ $i }}">
                                    <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}" class="sr-only" required>
                                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Titolo --}}
                    <div class="mb-4">
                        <label for="review-title" class="form-label">Titolo (opzionale)</label>
                        <input type="text" name="title" id="review-title"
                               value="{{ old('title') }}"
                               placeholder="Riassumi la tua esperienza"
                               maxlength="100"
                               class="input-field">
                        @error('title')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Commento --}}
                    <div class="mb-5">
                        <label for="review-comment" class="form-label">Commento (opzionale)</label>
                        <textarea name="comment" id="review-comment"
                                  rows="4"
                                  placeholder="Descrivi la tua esperienza con questo prodotto..."
                                  maxlength="2000"
                                  class="input-field resize-none">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary">Invia recensione</button>
                </form>
            </div>
        @endif

        {{-- Lista recensioni approvate --}}
        @if($reviewsCount > 0)
        <div class="space-y-6">
            @foreach($product->reviews as $review)
            <div class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        {{-- Stelle --}}
                        <div class="flex gap-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="w-4 h-4 review-star-filled" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 review-star-empty" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.601a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>

                        {{-- Nome + data --}}
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900">{{ $review->user->first_name }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>

                        {{-- Titolo --}}
                        @if($review->title)
                            <p class="text-sm font-semibold text-gray-800 mb-1">{{ $review->title }}</p>
                        @endif

                        {{-- Commento --}}
                        @if($review->comment)
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- ===== PRODOTTI CORRELATI ===== --}}
    @if($related->isNotEmpty())
    <div class="mt-16 border-t border-gray-200 pt-12">
        <div data-carousel>
            <div class="flex items-center justify-between mb-8">
                <h2 class="section-heading">Potrebbe interessarti</h2>
                <div class="flex items-center gap-2">
                    <button data-carousel-prev
                            class="w-9 h-9 flex items-center justify-center border border-primary-600 text-primary-600 hover:border-primary-900 hover:text-primary-900 transition-colors cursor-pointer"
                            aria-label="Precedente">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button data-carousel-next
                            class="w-9 h-9 flex items-center justify-center border border-primary-600 text-primary-600 hover:border-primary-900 hover:text-primary-900 transition-colors cursor-pointer"
                            aria-label="Successivo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="carousel-track">
                @foreach($related as $relProduct)
                    <div class="carousel-card">
                        <x-product-card :product="$relProduct" :wishlisted="in_array($relProduct->id, $wishlistIds)" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
