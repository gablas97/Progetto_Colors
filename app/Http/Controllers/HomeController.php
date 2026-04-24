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
                    'description' => 'Dalla singola pagina alle grandi tirature. Qualità tipografica, prezzi competitivi.

                    ✓ Stampa digitale e offset
                    ✓ Fotocopie B/N e colori
                    ✓ Rilegature (spirale, termiche, hardcover)
                    ✓ Plastificazioni e laminazioni
                    ✓ Stampe di grande formato (poster, banner)',
                    'icon' => 'printer'
                ],
                [
                    'name' => 'Grafica personalizzata',
                    'description' => 'Creiamo la tua identità visiva da zero: loghi, biglietti da visita, brochure, manifesti e qualsiasi materiale grafico di cui hai bisogno.

                    ✓ Progettazione professionale
                    ✓ Revisioni illimitate fino alla tua soddisfazione
                    ✓ File pronti per la stampa e il web
                    ✓ Tempi di consegna rapidi',
                    'icon' => 'sparkles'
                ],
                [
                    'name' => 'Timbri',
                    'description' => 'Realizziamo timbri professionali su misura per aziende, professionisti e privati. Ogni timbro è creato con precisione e cura artigianale.

                    ✓ Timbri autoinchiostranti e tradizionali
                    ✓ Design personalizzato incluso
                    ✓ Qualità garantita
                    ✓ Consegna in 48-72 ore',
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
