<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Product::active()
            ->where('is_service', true)
            ->with(['images' => fn ($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get();

        return view('services.index', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Product::active()
            ->where('is_service', true)
            ->where('slug', $slug)
            ->with(['images' => fn ($q) => $q->orderBy('order')])
            ->firstOrFail();

        return view('services.show', compact('service'));
    }
}
