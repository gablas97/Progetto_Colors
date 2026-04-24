@extends('layouts.app')
@section('title', 'Pagamento non completato - Colors S.r.l.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">

    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 mb-6">
        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </div>

    <h1 class="text-2xl font-semibold text-gray-900 mb-2">Pagamento non completato</h1>
    <p class="text-gray-500 text-sm mb-8">Il pagamento non è andato a buon fine o è stato annullato. Il tuo carrello è ancora intatto, puoi riprovare quando vuoi.</p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('checkout.index') }}" class="btn-primary">Riprova</a>
        <a href="{{ route('products.index') }}" class="btn-outline-dark">Continua gli acquisti</a>
    </div>

</div>
@endsection
