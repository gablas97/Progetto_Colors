<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->rootCategories()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderBy('order')
            ->limit(6)
            ->get();

        $featured = Product::active()
            ->where('is_featured', true)
            ->with(['variants' => fn ($q) => $q->active()])
            ->orderBy('order')
            ->get();

        $shopCats = [
            ['name' => 'Cancelleria',         'slug' => 'cancelleria',        'img' => 'cat-cancelleria.jpg', 'wide' => true],
            ['name' => 'Ufficio',             'slug' => 'ufficio',            'img' => 'cat-ufficio.jpg',     'wide' => false],
            ['name' => 'Scuola',              'slug' => 'scuola',             'img' => 'cat-scuola.jpg',      'wide' => false],
            ['name' => 'Promozioni',          'slug' => 'promozioni',         'img' => 'cat-promozioni.jpg',  'wide' => false],
            ['name' => 'Articoli da regalo',  'slug' => 'articoli-da-regalo', 'img' => 'cat-regalo.jpg',      'wide' => false],
        ];

        $services = [
                [
                    'name' => 'Stampe / fotocopie',
                    'description' => 'Stampe A3, A4, A5, fotocopie, stampe su cartoncino lucido e fotografie.',
                    'icon' => 'printer'
                ],
                [
                    'name' => 'Grafica personalizzata',
                    'description' => 'Bigliettini da visita, buoni regalo, voucher e materiale promozionale.',
                    'icon' => 'sparkles'
                ],
                [
                    'name' => 'Timbri',
                    'description' => 'Realizzazione di timbri personalizzati per uso professionale e personale.',
                    'icon' => 'stamp'
                ],
        ];

        $reviews = [
            ['text' => 'Persona gentilissima, cartoleria super accogliente con ottimi prodotti tra gadget e cancelleria. La mia cartoleria di fiducia ❤️', 'author' => 'Floriana P.'],
            ['text' => 'Da Colors trovo sempre efficienza e gentilezza: è sempre un piacere tornare! Oltre alla vasta scelta di prodotti di cartoleria, ci sono anche tante idee regalo originali. Consigliatissimo!', 'author' => 'Maria Vittoria M.'],
            ['text' => 'È un\'ottima cartoleria e non ha costi alti. Le fotocopie hanno un ottimo prezzo soprattutto se occorre fare molte copie. Le signore sono precise e molto gentili.', 'author' => 'Marilena N.'],
            ['text' => 'Cortesia ed efficienza al primo posto 🌟', 'author' => 'Gabriella B.'],
            ['text' => 'Very kind and helpful staff! Not only affordable goods and services but also great help for custom designs.', 'author' => 'Christopher F.'],
        ];

        return view('home', compact('categories', 'featured', 'shopCats', 'services', 'reviews'));
    }
}
