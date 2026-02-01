<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use App\Models\Wishlist;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Categorie
        $quaderni = Category::where('slug', 'quaderni')->first();
        $quaderniaA4 = Category::where('slug', 'quaderni-a4')->first();
        $penne = Category::where('slug', 'penne')->first();
        $penneBiro = Category::where('slug', 'penne-biro')->first();
        $matite = Category::where('slug', 'matite')->first();
        $scuola = Category::where('slug', 'scuola')->first();
        $promozioni = Category::where('slug', 'promozioni')->first();

        // PRODOTTO 1: Quaderno A4 Righe
        $quaderno1 = Product::create([
            'name' => 'Quaderno A4 80 Fogli Righe',
            'slug' => 'quaderno-a4-80-fogli-righe',
            'description' => 'Quaderno formato A4 con 80 fogli a righe. Copertina rigida disponibile in vari colori. Ideale per scuola e università.',
            'short_description' => 'Quaderno A4 80 fogli a righe, copertina rigida',
            'sku' => 'QUAD-A4-RIG-80',
            'price' => 3.50,
            'compare_at_price' => 4.50,
            'cost' => 2.00,
            'stock_quantity' => 0, // Stock gestito dalle varianti
            'low_stock_threshold' => 20,
            'vat_rate' => 22.00,
            'barcode' => '8001234567890',
            'is_active' => true,
            'is_featured' => true,
            'manage_stock' => false, // Stock gestito dalle varianti
            'order' => 1,
        ]);

        $quaderno1->categories()->attach([$quaderni->id, $quaderniaA4->id, $scuola->id, $promozioni->id]);

        // Varianti colore per Quaderno 1
        ProductVariant::create([
            'product_id' => $quaderno1->id,
            'name' => 'Rosso',
            'sku' => 'QUAD-A4-RIG-80-RED',
            'barcode' => '8001234567891',
            'stock_quantity' => 50,
            'order' => 1,
        ]);

        ProductVariant::create([
            'product_id' => $quaderno1->id,
            'name' => 'Blu',
            'sku' => 'QUAD-A4-RIG-80-BLU',
            'barcode' => '8001234567892',
            'stock_quantity' => 30,
            'order' => 2,
        ]);

        ProductVariant::create([
            'product_id' => $quaderno1->id,
            'name' => 'Verde',
            'sku' => 'QUAD-A4-RIG-80-GRN',
            'barcode' => '8001234567893',
            'stock_quantity' => 25,
            'order' => 3,
        ]);

        ProductVariant::create([
            'product_id' => $quaderno1->id,
            'name' => 'Nero',
            'sku' => 'QUAD-A4-RIG-80-BLK',
            'barcode' => '8001234567894',
            'stock_quantity' => 5, // Low stock
            'order' => 4,
        ]);

        // PRODOTTO 2: Quaderno A4 Quadretti
        $quaderno2 = Product::create([
            'name' => 'Quaderno A4 100 Fogli Quadretti 5mm',
            'slug' => 'quaderno-a4-100-fogli-quadretti',
            'description' => 'Quaderno formato A4 con 100 fogli a quadretti da 5mm. Perfetto per matematica e disegno tecnico.',
            'short_description' => 'Quaderno A4 100 fogli quadretti 5mm',
            'sku' => 'QUAD-A4-QUA-100',
            'price' => 4.00,
            'cost' => 2.50,
            'stock_quantity' => 80,
            'low_stock_threshold' => 15,
            'vat_rate' => 22.00,
            'is_active' => true,
            'is_featured' => false,
            'manage_stock' => true,
        ]);

        $quaderno2->categories()->attach([$quaderni->id, $quaderniaA4->id, $scuola->id]);

        // PRODOTTO 3: Penna Biro Blu (con varianti)
        $penna1 = Product::create([
            'name' => 'Penna Biro Cristal',
            'slug' => 'penna-biro-cristal',
            'description' => 'La classica penna biro BIC Cristal. Punta media 1.0mm, scrittura scorrevole e affidabile. Disponibile in vari colori.',
            'short_description' => 'Penna biro BIC Cristal punta media',
            'sku' => 'PEN-BIC-CRIS',
            'price' => 0.50,
            'cost' => 0.20,
            'stock_quantity' => 0,
            'low_stock_threshold' => 50,
            'vat_rate' => 22.00,
            'is_active' => true,
            'is_featured' => true,
            'manage_stock' => false,
        ]);

        $penna1->categories()->attach([$penne->id, $penneBiro->id, $scuola->id]);

        // Varianti penna
        ProductVariant::create([
            'product_id' => $penna1->id,
            'name' => 'Blu',
            'sku' => 'PEN-BIC-CRIS-BLU',
            'stock_quantity' => 200,
            'order' => 1,
        ]);

        ProductVariant::create([
            'product_id' => $penna1->id,
            'name' => 'Nero',
            'sku' => 'PEN-BIC-CRIS-BLK',
            'stock_quantity' => 150,
            'order' => 2,
        ]);

        ProductVariant::create([
            'product_id' => $penna1->id,
            'name' => 'Rosso',
            'sku' => 'PEN-BIC-CRIS-RED',
            'stock_quantity' => 100,
            'order' => 3,
        ]);

        // PRODOTTO 4: Matita HB
        $matita1 = Product::create([
            'name' => 'Matita Grafite HB con Gomma',
            'slug' => 'matita-grafite-hb-gomma',
            'description' => 'Matita in grafite gradazione HB con gomma incorporata. Ideale per scrittura e disegno.',
            'short_description' => 'Matita HB con gomma',
            'sku' => 'MAT-HB-001',
            'price' => 0.80,
            'compare_at_price' => 1.20,
            'cost' => 0.30,
            'stock_quantity' => 300,
            'low_stock_threshold' => 100,
            'vat_rate' => 22.00,
            'is_active' => true,
            'is_featured' => false,
            'manage_stock' => true,
        ]);

        $matita1->categories()->attach([$matite->id, $scuola->id, $promozioni->id]);

        // PRODOTTO 5: Set 12 Matite Colorate
        $matite2 = Product::create([
            'name' => 'Set 12 Matite Colorate',
            'slug' => 'set-12-matite-colorate',
            'description' => 'Set da 12 matite colorate di alta qualità. Colori vivaci e brillanti, perfette per disegno e colorare.',
            'short_description' => 'Set 12 matite colorate professionali',
            'sku' => 'MAT-COL-12',
            'price' => 5.90,
            'cost' => 3.00,
            'stock_quantity' => 45,
            'low_stock_threshold' => 20,
            'vat_rate' => 22.00,
            'is_active' => true,
            'is_featured' => true,
            'manage_stock' => true,
        ]);

        $matite2->categories()->attach([$matite->id, $scuola->id]);

        // PRODOTTO 6: Quaderno A5 (dimensione diversa = prodotto diverso)
        $quaderno3 = Product::create([
            'name' => 'Quaderno A5 60 Fogli Righe',
            'slug' => 'quaderno-a5-60-fogli-righe',
            'description' => 'Quaderno compatto formato A5 con 60 fogli a righe. Perfetto per appunti veloci.',
            'short_description' => 'Quaderno A5 60 fogli righe',
            'sku' => 'QUAD-A5-RIG-60',
            'price' => 2.50, // Prezzo diverso perché dimensione diversa
            'cost' => 1.20,
            'stock_quantity' => 60,
            'low_stock_threshold' => 30,
            'vat_rate' => 22.00,
            'is_active' => true,
            'manage_stock' => true,
        ]);

        $quaderno3->categories()->attach([
            Category::where('slug', 'quaderni-a5')->first()->id,
            $scuola->id
        ]);

        // PRODOTTO 7: Gomma per Matita
        $gomma = Product::create([
            'name' => 'Gomma Bianca per Matita',
            'slug' => 'gomma-bianca-matita',
            'description' => 'Gomma bianca di alta qualità. Non lascia residui e cancella perfettamente.',
            'short_description' => 'Gomma bianca professionale',
            'sku' => 'GOM-001',
            'price' => 0.60,
            'cost' => 0.25,
            'stock_quantity' => 8, // Low stock
            'low_stock_threshold' => 50,
            'vat_rate' => 22.00,
            'is_active' => true,
            'manage_stock' => true,
        ]);

        $gomma->categories()->attach([$scuola->id]);

        // PRODOTTO 8: Temperamatite
        $temperamatite = Product::create([
            'name' => 'Temperamatite Doppio Foro',
            'slug' => 'temperamatite-doppio-foro',
            'description' => 'Temperamatite in metallo con doppio foro per matite normali e jumbo. Con contenitore per i residui.',
            'short_description' => 'Temperamatite metallo 2 fori',
            'sku' => 'TEM-002',
            'price' => 1.20,
            'cost' => 0.60,
            'stock_quantity' => 0, // Out of stock
            'low_stock_threshold' => 30,
            'vat_rate' => 22.00,
            'is_active' => true,
            'manage_stock' => true,
        ]);

        $temperamatite->categories()->attach([$scuola->id]);

        // PRODOTTO 9: Prodotto Disattivato (per test)
        $prodottoDisattivato = Product::create([
            'name' => 'Prodotto Test Disattivato',
            'sku' => 'TEST-DISABLED',
            'price' => 10.00,
            'stock_quantity' => 100,
            'is_active' => false, // NON ATTIVO
            'manage_stock' => true,
        ]);

        // WISHLIST per utente 3
        $user3 = \App\Models\User::where('email', 'giuseppe.verdi@example.com')->first();
        if ($user3) {
            Wishlist::create([
                'user_id' => $user3->id,
                'product_id' => $quaderno1->id,
            ]);
            Wishlist::create([
                'user_id' => $user3->id,
                'product_id' => $penna1->id,
            ]);
            Wishlist::create([
                'user_id' => $user3->id,
                'product_id' => $matite2->id,
            ]);
        }

        // IMMAGINI (placeholder - in produzione caricherai vere immagini)
        // Per il momento creiamo solo i record, le immagini andranno poi uploadate
        ProductImage::create([
            'product_id' => $quaderno1->id,
            'image' => 'products/quaderno-a4-righe-1.jpg',
            'alt_text' => 'Quaderno A4 righe - vista frontale',
            'order' => 1,
        ]);

        ProductImage::create([
            'product_id' => $quaderno1->id,
            'image' => 'products/quaderno-a4-righe-2.jpg',
            'alt_text' => 'Quaderno A4 righe - interno',
            'order' => 2,
        ]);
    }
}