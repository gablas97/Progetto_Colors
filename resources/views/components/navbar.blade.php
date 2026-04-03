@php
    // Cart count: DB per utenti loggati, sessione per ospiti
    if (auth()->check()) {
        $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0;
    } else {
        $cartCount = session('cart_count', 0);
    }
    // Categorie radice per il dropdown navbar
    $navRootCategories = \App\Models\Category::active()->rootCategories()->get();
@endphp

<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo placeholder (sostituire con <img> quando arriva il logo) --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Colors S.r.l." class="h-16 w-auto">
            </a>

            {{-- Link di navigazione (desktop) --}}
            <div class="hidden md:flex items-center gap-8">
                <div class="relative group">
                    <a href="{{ route('products.index') }}"
                       class="nav-link flex items-center gap-1 {{ request()->routeIs('products.*') ? 'text-primary' : '' }}">
                        Prodotti
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full mt-1 bg-white shadow-lg border border-gray-100 py-2 w-52 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-150 z-50">
                        <a href="{{ route('products.index') }}"
                           class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide border-b border-gray-200 mb-1">
                            Tutti i prodotti
                        </a>
                        @foreach($navRootCategories as $cat)
                            <a href="{{ route('products.index', ['categoria' => $cat->slug]) }}"
                               class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('services') }}"
                   class="nav-link {{ request()->routeIs('services') ? 'text-primary' : '' }}">
                    Servizi
                </a>
            </div>

            {{-- Account + Carrello (desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                @auth
                    <div class="relative group">
                        <button class="nav-link flex items-center gap-1 cursor-pointer">
                            <a href="{{ route('account.index') }}">{{ auth()->user()->first_name }}</a>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute right-0 top-full mt-1 bg-white shadow-lg border border-gray-100 py-2 w-44 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-150">
                            <a href="{{ route('account.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide border-b border-gray-200 mb-1">Account</a>
                            <a href="{{ route('account.profile') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">Il mio profilo</a>
                            <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">I miei ordini</a>
                            <a href="{{ route('account.wishlist') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">Wishlist</a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs text-red-500 hover:bg-gray-50 tracking-wide cursor-pointer">
                                    Esci
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Account</a>
                @endauth

                @if(request()->routeIs('cart.index', 'checkout.*'))
                <a href="{{ route('cart.index') }}" class="nav-link flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Carrello (<span data-cart-count>{{ $cartCount }}</span>)</span>
                </a>
                @else
                <div class="relative" id="cart-dropdown-wrapper">
                    <a href="{{ route('cart.index') }}" class="nav-link flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Carrello (<span data-cart-count>{{ $cartCount }}</span>)</span>
                    </a>

                    {{-- Dropdown anteprima carrello --}}
                    <div id="cart-dropdown"
                         class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-100 shadow-lg z-50"
                         style="display:none;">
                        <div id="cart-dropdown-inner" class="p-4">
                            <p class="text-xs text-gray-400 text-center py-4">Caricamento...</p>
                        </div>
                        <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
                            <span class="text-xs text-gray-500">Totale</span>
                            <span id="cart-dropdown-total" class="text-sm font-semibold text-gray-900">—</span>
                        </div>
                        <div class="px-4 pb-4">
                            <a href="{{ route('cart.index') }}" class="btn-primary w-full text-center block">
                                Vai al carrello
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Hamburger (mobile) --}}
            <button id="mobile-menu-btn" class="md:hidden p-2 text-gray-600 hover:text-primary transition-colors" aria-label="Apri menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Menu mobile --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-4 flex flex-col gap-4">
        <a href="{{ route('products.index') }}" class="nav-link">Prodotti</a>
        <a href="{{ route('services') }}"       class="nav-link">Servizi</a>
        <hr class="border-gray-100">
        @auth
            <a href="{{ route('account.index') }}"  class="nav-link">Il mio account</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link text-red-500">Esci</button>
            </form>
        @else
            <a href="{{ route('login') }}"    class="nav-link">Accedi</a>
            <a href="{{ route('register') }}" class="nav-link">Registrati</a>
        @endauth
        <a href="{{ route('cart.index') }}" class="nav-link flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Carrello (<span data-cart-count>{{ $cartCount }}</span>)</span>
        </a>
    </div>
</nav>

