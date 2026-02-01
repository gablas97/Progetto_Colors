<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'mario.rossi@example.com')->first();
        $user2 = User::where('email', 'laura.bianchi@example.com')->first();
        
        $quaderno1 = Product::where('sku', 'QUAD-A4-RIG-80')->first();
        $matite2 = Product::where('sku', 'MAT-COL-12')->first();
        
        // Ordini devono esistere
        $order1 = Order::where('order_number', 'ORD-2024-00001')->first();
        $order2 = Order::where('order_number', 'ORD-2024-00002')->first();

        Review::create([
            'product_id' => $quaderno1->id,
            'user_id' => $user1->id,
            'order_id' => $order1->id, // Ora l'ordine esiste!
            'rating' => 5,
            'title' => 'Ottimo quaderno!',
            'comment' => 'Qualità eccellente, fogli resistenti. Lo uso per l\'università.',
            'is_verified_purchase' => true,
            'is_approved' => true,
            'helpful_count' => 3,
        ]);

        Review::create([
            'product_id' => $quaderno1->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'title' => 'Buon prodotto',
            'comment' => 'Va bene, unico difetto è che la copertina si sporca facilmente.',
            'is_verified_purchase' => false,
            'is_approved' => true,
            'helpful_count' => 1,
        ]);

        Review::create([
            'product_id' => $matite2->id,
            'user_id' => $user2->id,
            'order_id' => $order2->id,
            'rating' => 5,
            'title' => 'Colori brillanti',
            'comment' => 'Mia figlia le adora! Colori vivaci e matite resistenti.',
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        // Aggiorna ratings prodotti
        $quaderno1->updateRatings();
        $matite2->updateRatings();
    }
}