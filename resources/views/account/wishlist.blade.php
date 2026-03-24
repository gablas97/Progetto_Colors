@extends('layouts.app')
@section('title', 'Wishlist — Colors S.r.l.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('account.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">La mia wishlist</h1>
    </div>

    @if($wishlist->isEmpty())
        <div class="text-center py-20 text-gray-400 text-sm">
            La tua wishlist è vuota.<br>
            <a href="{{ route('products.index') }}" class="text-primary hover:text-primary-dark mt-2 inline-block">Sfoglia i prodotti</a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($wishlist as $item)
                @if($item->product)
                    <x-product-card :product="$item->product" />
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection
