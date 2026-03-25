<footer>
    {{-- Footer principale --}}
    <div class="bg-dark text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                {{-- Brand --}}
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                        <svg width="46" height="34" viewBox="0 0 46 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <line x1="23" y1="30" x2="3"  y2="4"  stroke="#E8845A" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="23" y1="30" x2="11" y2="2"  stroke="#F5C842" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="23" y1="30" x2="23" y2="1"  stroke="#4CAF50" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="23" y1="30" x2="35" y2="2"  stroke="#2196F3" stroke-width="2.5" stroke-linecap="round"/>
                            <line x1="23" y1="30" x2="43" y2="4"  stroke="#9C27B0" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <span class="font-bold tracking-widest text-white text-sm">COLORS</span>
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        La tua cartoleria di fiducia a Taranto. Prodotti di qualità, stampe, grafica personalizzata e articoli regalo.
                    </p>
                </div>

                {{-- Contatti --}}
                <div>
                    <h3 class="text-xs font-semibold tracking-widest uppercase text-gray-300 mb-4">Contatti</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <a href="https://maps.app.goo.gl/8gU5VBw9cQyEzHc87" target="_blank" class="hover:text-white transition-colors">Via Umbria 35, 74121 Taranto</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:+390997364061" class="hover:text-white transition-colors">+39 099 736 4061</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:colorstarantosrl@gmail.com" class="hover:text-white transition-colors">colorstarantosrl@gmail.com</a>
                        </li>
                    </ul>
                </div>

                {{-- Orari --}}
                <div>
                    <h3 class="text-xs font-semibold tracking-widest uppercase text-gray-300 mb-4">Orari</h3>
                    <ul class="space-y-1 text-sm text-gray-400">
                        <li>Lunedì – Venerdì</li>
                        <li class="text-white font-medium">08:00 – 13:30 &nbsp;/&nbsp; 16:30 – 20:00</li>
                        <li class="mt-3">Sabato</li>
                        <li class="text-white font-medium">08:00 – 13:00</li>
                        <li class="mt-3 text-gray-500 italic">Domenica: chiuso</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra inferiore --}}
    <div class="bg-dark border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} Colors S.r.l. &mdash; Tutti i diritti riservati
            </p>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Seguici su:</span>
                <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors" aria-label="Instagram">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
