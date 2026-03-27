@php
    // Cart count: DB per utenti loggati, sessione per ospiti
    if (auth()->check()) {
        $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0;
    } else {
        $cartCount = session('cart_count', 0);
    }
@endphp

<nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo placeholder (sostituire con <img> quando arriva il logo) --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <svg width="46" height="34" viewBox="0 0 46 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <line x1="23" y1="30" x2="3"  y2="4"  stroke="#E8845A" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="23" y1="30" x2="11" y2="2"  stroke="#F5C842" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="23" y1="30" x2="23" y2="1"  stroke="#4CAF50" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="23" y1="30" x2="35" y2="2"  stroke="#2196F3" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="23" y1="30" x2="43" y2="4"  stroke="#9C27B0" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
                <div class="leading-tight">
                    <p class="font-bold tracking-widest text-gray-900 text-sm leading-none">COLORS</p>
                    <p class="text-[9px] text-gray-400 tracking-widest uppercase leading-none mt-0.5">Sogna la vita a colori</p>
                </div>
            </a>

            {{-- Link di navigazione (desktop) --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'text-primary' : '' }}">
                    Prodotti
                </a>
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
                            Account
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute right-0 top-full mt-1 bg-white shadow-lg border border-gray-100 py-2 w-44 invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-150">
                            <a href="{{ route('account.index') }}"     class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">Il mio profilo</a>
                            <a href="{{ route('account.orders') }}"    class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">I miei ordini</a>
                            <a href="{{ route('account.wishlist') }}"  class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">Wishlist</a>
                            <a href="{{ route('account.addresses') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 tracking-wide">Indirizzi</a>
                            <hr class="my-1 border-gray-100">
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

                <a href="{{ route('cart.index') }}" class="nav-link flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Carrello (<span data-cart-count>{{ $cartCount }}</span>)</span>
                </a>
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
            Carrello (<span data-cart-count>{{ $cartCount }}</span>)
        </a>
    </div>
</nav>

