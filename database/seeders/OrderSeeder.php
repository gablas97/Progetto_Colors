<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer1 = User::where('email', 'mario.rossi@example.com')->first();
        $customer2 = User::where('email', 'laura.bianchi@example.com')->first();

        // ORDINE 1: Ordine Consegnato (Mario Rossi)
        $order1 = Order::create([
            'order_number' => 'ORD-2024-00001',
            'user_id' => $customer1->id,
            'shipping_first_name' => 'Mario',
            'shipping_last_name' => 'Rossi',
            'shipping_address' => 'Via Roma 123',
            'shipping_city' => 'Milano',
            'shipping_province' => 'MI',
            'shipping_postal_code' => '20100',
            'shipping_country' => 'IT',
            'shipping_phone' => '3331234567',
            'billing_same_as_shipping' => true,
            'subtotal' => 11.48,
            'discount_amount' => 1.15,
            'discount_code' => 'BENVENUTO10',
            'shipping_cost' => 5.00,
            'tax_amount' => 3.41,
            'total' => 18.74,
            'payment_method' => 'credit_card',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subDays(15),
            'payment_transaction_id' => 'TXN-123456789',
            'status' => 'delivered',
            'shipped_at' => Carbon::now()->subDays(13),
            'delivered_at' => Carbon::now()->subDays(10),
            'notes' => 'Consegnare al portiere',
            'created_at' => Carbon::now()->subDays(15),
        ]);

        // Items Ordine 1
        $quaderno1 = Product::where('sku', 'QUAD-A4-RIG-80')->first();
        $varianteRossa = ProductVariant::where('sku', 'QUAD-A4-RIG-80-RED')->first();
        $penna1 = Product::where('sku', 'PEN-BIC-CRIS')->first();
        $varianteBlu = ProductVariant::where('sku', 'PEN-BIC-CRIS-BLU')->first();

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $quaderno1->id,
            'product_variant_id' => $varianteRossa->id,
            'product_name' => $quaderno1->name,
            'product_sku' => $varianteRossa->sku,
            'variant_name' => 'Rosso',
            'price' => 3.50,
            'quantity' => 2,
            'vat_rate' => 22.00,
            'subtotal' => 7.00,
            'tax_amount' => 1.54,
            'total' => 8.54,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $penna1->id,
            'product_variant_id' => $varianteBlu->id,
            'product_name' => $penna1->name,
            'product_sku' => $varianteBlu->sku,
            'variant_name' => 'Blu',
            'price' => 0.50,
            'quantity' => 10,
            'vat_rate' => 22.00,
            'subtotal' => 5.00,
            'tax_amount' => 1.10,
            'total' => 6.10,
        ]);

        // ORDINE 2: Ordine in Preparazione (Laura Bianchi con fattura)
        $order2 = Order::create([
            'order_number' => 'ORD-2024-00002',
            'user_id' => $customer2->id,
            'shipping_first_name' => 'Laura',
            'shipping_last_name' => 'Bianchi',
            'shipping_address' => 'Corso Vittorio Emanuele 789',
            'shipping_city' => 'Torino',
            'shipping_province' => 'TO',
            'shipping_postal_code' => '10100',
            'shipping_country' => 'IT',
            'shipping_phone' => '3339876543',
            'billing_same_as_shipping' => false,
            'billing_first_name' => 'Laura',
            'billing_last_name' => 'Bianchi',
            'billing_company' => 'Bianchi SRL',
            'billing_vat_number' => 'IT12345678901',
            'billing_tax_code' => 'BNCLAR80A01H501Z',
            'billing_address' => 'Via Ufficio 1',
            'billing_city' => 'Torino',
            'billing_province' => 'TO',
            'billing_postal_code' => '10100',
            'billing_country' => 'IT',
            'subtotal' => 9.90,
            'discount_amount' => 0,
            'shipping_cost' => 5.00,
            'tax_amount' => 3.28,
            'total' => 18.18,
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subDays(2),
            'payment_transaction_id' => 'PAYPAL-987654321',
            'status' => 'processing',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        $matite2 = Product::where('sku', 'MAT-COL-12')->first();

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $matite2->id,
            'product_name' => $matite2->name,
            'product_sku' => $matite2->sku,
            'price' => 5.90,
            'quantity' => 1,
            'vat_rate' => 22.00,
            'subtotal' => 5.90,
            'tax_amount' => 1.30,
            'total' => 7.20,
        ]);

        $quaderno2 = Product::where('sku', 'QUAD-A4-QUA-100')->first();

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $quaderno2->id,
            'product_name' => $quaderno2->name,
            'product_sku' => $quaderno2->sku,
            'price' => 4.00,
            'quantity' => 1,
            'vat_rate' => 22.00,
            'subtotal' => 4.00,
            'tax_amount' => 0.88,
            'total' => 4.88,
        ]);

        // ORDINE 3: Ordine Guest (senza user_id)
        $order3 = Order::create([
            'order_number' => 'ORD-2024-00003',
            'user_id' => null,
            'guest_email' => 'guest@example.com',
            'shipping_first_name' => 'Cliente',
            'shipping_last_name' => 'Ospite',
            'shipping_address' => 'Via Guest 999',
            'shipping_city' => 'Roma',
            'shipping_province' => 'RM',
            'shipping_postal_code' => '00100',
            'shipping_country' => 'IT',
            'shipping_phone' => '3331112222',
            'billing_same_as_shipping' => true,
            'subtotal' => 0.80,
            'discount_amount' => 0,
            'shipping_cost' => 5.00,
            'tax_amount' => 1.28,
            'total' => 6.98,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => 'Ordine guest in attesa di bonifico',
            'created_at' => Carbon::now()->subHours(5),
        ]);

        $matita1 = Product::where('sku', 'MAT-HB-001')->first();

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $matita1->id,
            'product_name' => $matita1->name,
            'product_sku' => $matita1->sku,
            'price' => 0.80,
            'quantity' => 1,
            'vat_rate' => 22.00,
            'subtotal' => 0.80,
            'tax_amount' => 0.18,
            'total' => 0.98,
        ]);

        // ORDINE 4: Ordine Spedito
        $order4 = Order::create([
            'order_number' => 'ORD-2024-00004',
            'user_id' => $customer1->id,
            'shipping_first_name' => 'Mario',
            'shipping_last_name' => 'Rossi',
            'shipping_address' => 'Via Milano 456',
            'shipping_city' => 'Roma',
            'shipping_province' => 'RM',
            'shipping_postal_code' => '00100',
            'shipping_country' => 'IT',
            'shipping_phone' => '3331234567',
            'billing_same_as_shipping' => true,
            'subtotal' => 4.00,
            'discount_amount' => 0,
            'shipping_cost' => 0, // Spedizione gratis applicata
            'tax_amount' => 0.88,
            'total' => 4.88,
            'payment_method' => 'credit_card',
            'payment_status' => 'paid',
            'paid_at' => Carbon::now()->subDays(5),
            'payment_transaction_id' => 'TXN-555666777',
            'status' => 'shipped',
            'shipped_at' => Carbon::now()->subDays(3),
            'admin_notes' => 'Spedito con corriere express',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $quaderno2->id,
            'product_name' => $quaderno2->name,
            'product_sku' => $quaderno2->sku,
            'price' => 4.00,
            'quantity' => 1,
            'vat_rate' => 22.00,
            'subtotal' => 4.00,
            'tax_amount' => 0.88,
            'total' => 4.88,
        ]);

        // ORDINE 5: Ordine Annullato
        $order5 = Order::create([
            'order_number' => 'ORD-2024-00005',
            'user_id' => $customer2->id,
            'shipping_first_name' => 'Laura',
            'shipping_last_name' => 'Bianchi',
            'shipping_address' => 'Corso Vittorio Emanuele 789',
            'shipping_city' => 'Torino',
            'shipping_province' => 'TO',
            'shipping_postal_code' => '10100',
            'shipping_country' => 'IT',
            'billing_same_as_shipping' => true,
            'subtotal' => 1.20,
            'discount_amount' => 0,
            'shipping_cost' => 5.00,
            'tax_amount' => 1.36,
            'total' => 7.56,
            'payment_method' => 'paypal',
            'payment_status' => 'refunded',
            'paid_at' => Carbon::now()->subDays(7),
            'status' => 'cancelled',
            'cancelled_at' => Carbon::now()->subDays(6),
            'admin_notes' => 'Annullato per richiesta cliente',
            'created_at' => Carbon::now()->subDays(7),
        ]);

        $temperamatite = Product::where('sku', 'TEM-002')->first();

        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $temperamatite->id,
            'product_name' => $temperamatite->name,
            'product_sku' => $temperamatite->sku,
            'price' => 1.20,
            'quantity' => 1,
            'vat_rate' => 22.00,
            'subtotal' => 1.20,
            'tax_amount' => 0.26,
            'total' => 1.46,
        ]);
    }
}