<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::where('email', 'mario.rossi@example.com')->first();
        $user3 = User::where('email', 'giuseppe.verdi@example.com')->first();

        // Carrello utente 1
        $cart1 = Cart::create(['user_id' => $user1->id]);
        
        $quaderno2 = Product::where('sku', 'QUAD-A4-QUA-100')->first();
        $penna1 = Product::where('sku', 'PEN-BIC-CRIS')->first();
        $varianteBlu = ProductVariant::where('sku', 'PEN-BIC-CRIS-BLU')->first();
        
        $cart1->addItem($quaderno2, null, 2);
        $cart1->addItem($penna1, $varianteBlu, 5);

        // Carrello utente 3
        $cart3 = Cart::create(['user_id' => $user3->id]);
        
        $matite = Product::where('sku', 'MAT-COL-12')->first();
        $cart3->addItem($matite, null, 1);

        // Carrello guest (sessione)
        $cartGuest = Cart::create(['session_id' => 'guest-session-12345']);
        
        $matita1 = Product::where('sku', 'MAT-HB-001')->first();
        $cartGuest->addItem($matita1, null, 3);
    }
}