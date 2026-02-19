<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500">Prodotto</p>
            <p class="text-base font-semibold">{{ $review->product->name }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Utente</p>
            <p class="text-base">{{ $review->user->full_name ?? $review->user->email }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Valutazione</p>
            <p class="text-xl">{{ str_repeat('⭐', $review->rating) }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500">Data</p>
            <p class="text-base">{{ $review->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if($review->title)
    <div>
        <p class="text-sm font-medium text-gray-500">Titolo</p>
        <p class="text-lg font-semibold">{{ $review->title }}</p>
    </div>
    @endif

    @if($review->comment)
    <div>
        <p class="text-sm font-medium text-gray-500">Commento</p>
        <p class="text-base text-gray-700">{{ $review->comment }}</p>
    </div>
    @endif

    <div class="flex gap-4">
        @if($review->is_verified_purchase)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                ✓ Acquisto Verificato
            </span>
        @endif
        @if($review->is_approved)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                ✓ Approvata
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                ⏳ In Attesa
            </span>
        @endif
    </div>
</div>