<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        // SCONTO 1: Codice Benvenuto (10% su tutto)
        $benvenuto = Discount::create([
            'name' => 'Sconto Benvenuto 10%',
            'code' => 'BENVENUTO10',
            'type' => 'percentage',
            'value' => 10.00,
            'min_order_amount' => null,
            'usage_limit' => 100,
            'usage_count' => 15, // Già usato 15 volte
            'starts_at' => Carbon::now()->subDays(30),
            'expires_at' => Carbon::now()->addDays(30),
            'is_active' => true,
        ]);

        // SCONTO 2: Spedizione Gratis sopra 50€
        $spedizione = Discount::create([
            'name' => 'Spedizione Gratis sopra 50€',
            'code' => 'FREESHIP50',
            'type' => 'shipping',
            'value' => 5.00, // Costo spedizione standard
            'min_order_amount' => 50.00,
            'usage_limit' => null, // Illimitato
            'usage_count' => 0,
            'starts_at' => null,
            'expires_at' => null,
            'is_active' => true,
        ]);

        // SCONTO 3: 5€ di Sconto Fisso
        $fisso5 = Discount::create([
            'name' => 'Sconto 5€',
            'code' => 'SCONTO5',
            'type' => 'fixed',
            'value' => 5.00,
            'min_order_amount' => 30.00,
            'usage_limit' => 50,
            'usage_count' => 8,
            'starts_at' => Carbon::now()->subDays(10),
            'expires_at' => Carbon::now()->addDays(20),
            'is_active' => true,
        ]);

        // SCONTO 4: Black Friday (sconto specifico su alcuni prodotti)
        $blackFriday = Discount::create([
            'name' => 'Black Friday -20%',
            'code' => 'BLACKFRIDAY20',
            'type' => 'percentage',
            'value' => 20.00,
            'min_order_amount' => null,
            'usage_limit' => 200,
            'usage_count' => 0,
            'starts_at' => Carbon::now()->addDays(60),
            'expires_at' => Carbon::now()->addDays(67),
            'is_active' => false, // Non ancora attivo
        ]);

        // Applica Black Friday solo su prodotti specifici
        $prodottiBlackFriday = Product::whereIn('sku', [
            'QUAD-A4-RIG-80',
            'MAT-COL-12',
        ])->pluck('id');

        $blackFriday->products()->attach($prodottiBlackFriday);

        // SCONTO 5: Sconto Scaduto (per test)
        Discount::create([
            'name' => 'Sconto Scaduto',
            'code' => 'EXPIRED',
            'type' => 'percentage',
            'value' => 15.00,
            'starts_at' => Carbon::now()->subDays(60),
            'expires_at' => Carbon::now()->subDays(30), // Scaduto
            'is_active' => true,
        ]);

        // SCONTO 6: Sconto Esaurito (raggiunto limite utilizzi)
        Discount::create([
            'name' => 'Sconto Esaurito',
            'code' => 'SOLDOUT',
            'type' => 'fixed',
            'value' => 10.00,
            'usage_limit' => 10,
            'usage_count' => 10, // Limite raggiunto
            'is_active' => true,
        ]);
    }
}