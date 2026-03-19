<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\TransportDocument;
use App\Models\TransportDocumentItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoicingSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::first();
        $orders  = Order::with('items.product')->take(4)->get();
        $products = Product::take(5)->get();

        // ─── FATTURE ────────────────────────────────────────────────────────────

        $invoices = [
            [
                'data' => [
                    'type'                => 'invoice',
                    'order_id'            => $orders->get(0)?->id,
                    'user_id'             => $admin->id,
                    'client_name'         => 'Mario Rossi',
                    'client_email'        => 'mario.rossi@email.it',
                    'client_company'      => null,
                    'client_vat_number'   => null,
                    'client_tax_code'     => 'RSSMRA80A01H501U',
                    'client_address'      => 'Via Roma 12',
                    'client_city'         => 'Milano',
                    'client_province'     => 'MI',
                    'client_postal_code'  => '20121',
                    'subtotal'            => 145.00,
                    'discount_amount'     => 0.00,
                    'tax_amount'          => 31.90,
                    'total'               => 176.90,
                    'status'              => 'paid',
                    'payment_method'      => 'bank_transfer',
                    'issue_date'          => now()->subDays(20)->toDateString(),
                    'due_date'            => now()->subDays(5)->toDateString(),
                    'paid_date'           => now()->subDays(8)->toDateString(),
                    'notes'               => 'Pagamento ricevuto. Grazie.',
                    'created_by'          => $admin->id,
                ],
                'items' => [
                    ['description' => 'Pittura murale bianca 5L', 'quantity' => 2, 'unit_price' => 45.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 90.00, 'tax_amount' => 19.80, 'total' => 109.80, 'product_id' => $products->get(0)?->id],
                    ['description' => 'Pennello professionale set 3 pz', 'quantity' => 1, 'unit_price' => 55.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 55.00, 'tax_amount' => 12.10, 'total' => 67.10, 'product_id' => $products->get(1)?->id],
                ],
            ],
            [
                'data' => [
                    'type'                => 'invoice',
                    'order_id'            => $orders->get(1)?->id,
                    'user_id'             => $admin->id,
                    'client_name'         => 'Costruzioni Bianchi S.r.l.',
                    'client_email'        => 'admin@costruzionibianchi.it',
                    'client_company'      => 'Costruzioni Bianchi S.r.l.',
                    'client_vat_number'   => 'IT02345678901',
                    'client_tax_code'     => null,
                    'client_sdi_code'     => 'ABCDEF1',
                    'client_address'      => 'Via Industriale 55',
                    'client_city'         => 'Bergamo',
                    'client_province'     => 'BG',
                    'client_postal_code'  => '24122',
                    'subtotal'            => 620.00,
                    'discount_amount'     => 50.00,
                    'tax_amount'          => 123.20,
                    'total'               => 693.20,
                    'status'              => 'sent',
                    'payment_method'      => 'bank_transfer',
                    'issue_date'          => now()->subDays(10)->toDateString(),
                    'due_date'            => now()->addDays(20)->toDateString(),
                    'paid_date'           => null,
                    'notes'               => 'Pagamento a 30 giorni data fattura.',
                    'created_by'          => $admin->id,
                ],
                'items' => [
                    ['description' => 'Vernice esterna impermeabile 10L', 'quantity' => 4, 'unit_price' => 89.00, 'vat_rate' => 22, 'discount_percentage' => 10, 'subtotal' => 320.40, 'tax_amount' => 70.49, 'total' => 390.89, 'product_id' => $products->get(2)?->id],
                    ['description' => 'Primer isolante 5L', 'quantity' => 3, 'unit_price' => 38.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 114.00, 'tax_amount' => 25.08, 'total' => 139.08, 'product_id' => $products->get(3)?->id],
                    ['description' => 'Nastro mascherante professionale 50mt', 'quantity' => 5, 'unit_price' => 8.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 40.00, 'tax_amount' => 8.80, 'total' => 48.80, 'product_id' => $products->get(4)?->id],
                ],
            ],
            [
                'data' => [
                    'type'                => 'invoice',
                    'order_id'            => $orders->get(2)?->id,
                    'user_id'             => $admin->id,
                    'client_name'         => 'Lucia Ferri',
                    'client_email'        => 'lucia.ferri@gmail.com',
                    'client_company'      => null,
                    'client_vat_number'   => null,
                    'client_tax_code'     => 'FRRLCU90B45E625M',
                    'client_address'      => 'Corso Venezia 8',
                    'client_city'         => 'Torino',
                    'client_province'     => 'TO',
                    'client_postal_code'  => '10121',
                    'subtotal'            => 78.00,
                    'discount_amount'     => 0.00,
                    'tax_amount'          => 17.16,
                    'total'               => 95.16,
                    'status'              => 'overdue',
                    'payment_method'      => 'credit_card',
                    'issue_date'          => now()->subDays(45)->toDateString(),
                    'due_date'            => now()->subDays(15)->toDateString(),
                    'paid_date'           => null,
                    'notes'               => 'Sollecito inviato il ' . now()->subDays(5)->format('d/m/Y') . '.',
                    'created_by'          => $admin->id,
                ],
                'items' => [
                    ['description' => 'Rullo schiuma media densità set 2 pz', 'quantity' => 3, 'unit_price' => 26.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 78.00, 'tax_amount' => 17.16, 'total' => 95.16, 'product_id' => $products->get(0)?->id],
                ],
            ],
            [
                'data' => [
                    'type'                => 'credit_note',
                    'order_id'            => null,
                    'user_id'             => $admin->id,
                    'client_name'         => 'Mario Rossi',
                    'client_email'        => 'mario.rossi@email.it',
                    'client_tax_code'     => 'RSSMRA80A01H501U',
                    'client_address'      => 'Via Roma 12',
                    'client_city'         => 'Milano',
                    'client_province'     => 'MI',
                    'client_postal_code'  => '20121',
                    'subtotal'            => 45.00,
                    'discount_amount'     => 0.00,
                    'tax_amount'          => 9.90,
                    'total'               => 54.90,
                    'status'              => 'sent',
                    'payment_method'      => null,
                    'issue_date'          => now()->subDays(5)->toDateString(),
                    'due_date'            => null,
                    'paid_date'           => null,
                    'notes'               => 'Nota di credito per reso merce parziale.',
                    'created_by'          => $admin->id,
                ],
                'items' => [
                    ['description' => 'Pittura murale bianca 5L (reso)', 'quantity' => 1, 'unit_price' => 45.00, 'vat_rate' => 22, 'discount_percentage' => 0, 'subtotal' => 45.00, 'tax_amount' => 9.90, 'total' => 54.90, 'product_id' => $products->get(0)?->id],
                ],
            ],
        ];

        foreach ($invoices as $entry) {
            $invoice = Invoice::create($entry['data']);
            foreach ($entry['items'] as $item) {
                $invoice->items()->create($item);
            }
        }

        // ─── DOCUMENTI DI TRASPORTO ──────────────────────────────────────────────

        $ddts = [
            [
                'data' => [
                    'order_id'             => $orders->get(0)?->id,
                    'user_id'              => $admin->id,
                    'sender_name'          => 'Colors S.r.l.',
                    'sender_address'       => 'Via delle Industrie 10',
                    'sender_city'          => 'Milano',
                    'sender_province'      => 'MI',
                    'sender_postal_code'   => '20143',
                    'recipient_name'       => 'Mario Rossi',
                    'recipient_address'    => 'Via Roma 12',
                    'recipient_city'       => 'Milano',
                    'recipient_province'   => 'MI',
                    'recipient_postal_code'=> '20121',
                    'shipping_method'      => 'corriere',
                    'carrier_name'         => 'BRT',
                    'tracking_number'      => 'BRT' . rand(100000, 999999),
                    'packages_count'       => 2,
                    'total_weight'         => 12.50,
                    'appearance'           => 'Scatole integre',
                    'reason'               => 'vendita',
                    'shipping_date'        => now()->subDays(18),
                    'delivery_date'        => now()->subDays(16),
                    'status'               => 'delivered',
                    'notes'               => 'Consegna avvenuta regolarmente.',
                    'created_by'           => $admin->id,
                ],
                'items' => [
                    ['description' => 'Pittura murale bianca 5L', 'quantity' => 2, 'unit' => 'pz', 'weight' => 5500.00, 'product_id' => $products->get(0)?->id],
                    ['description' => 'Pennello professionale set 3 pz', 'quantity' => 1, 'unit' => 'pz', 'weight' => 350.00, 'product_id' => $products->get(1)?->id],
                ],
            ],
            [
                'data' => [
                    'order_id'             => $orders->get(1)?->id,
                    'user_id'              => $admin->id,
                    'sender_name'          => 'Colors S.r.l.',
                    'sender_address'       => 'Via delle Industrie 10',
                    'sender_city'          => 'Milano',
                    'sender_province'      => 'MI',
                    'sender_postal_code'   => '20143',
                    'recipient_name'       => 'Costruzioni Bianchi S.r.l.',
                    'recipient_address'    => 'Via Industriale 55',
                    'recipient_city'       => 'Bergamo',
                    'recipient_province'   => 'BG',
                    'recipient_postal_code'=> '24122',
                    'shipping_method'      => 'corriere',
                    'carrier_name'         => 'GLS',
                    'tracking_number'      => 'GLS' . rand(100000, 999999),
                    'packages_count'       => 5,
                    'total_weight'         => 48.20,
                    'appearance'           => 'Pallet filmato',
                    'reason'               => 'vendita',
                    'shipping_date'        => now()->subDays(8),
                    'delivery_date'        => null,
                    'status'               => 'shipped',
                    'notes'               => null,
                    'created_by'           => $admin->id,
                ],
                'items' => [
                    ['description' => 'Vernice esterna impermeabile 10L', 'quantity' => 4, 'unit' => 'pz', 'weight' => 11000.00, 'product_id' => $products->get(2)?->id],
                    ['description' => 'Primer isolante 5L', 'quantity' => 3, 'unit' => 'pz', 'weight' => 5500.00, 'product_id' => $products->get(3)?->id],
                    ['description' => 'Nastro mascherante professionale 50mt', 'quantity' => 5, 'unit' => 'pz', 'weight' => 200.00, 'product_id' => $products->get(4)?->id],
                ],
            ],
            [
                'data' => [
                    'order_id'             => null,
                    'user_id'              => $admin->id,
                    'sender_name'          => 'Colors S.r.l.',
                    'sender_address'       => 'Via delle Industrie 10',
                    'sender_city'          => 'Milano',
                    'sender_province'      => 'MI',
                    'sender_postal_code'   => '20143',
                    'recipient_name'       => 'Mario Rossi',
                    'recipient_address'    => 'Via Roma 12',
                    'recipient_city'       => 'Milano',
                    'recipient_province'   => 'MI',
                    'recipient_postal_code'=> '20121',
                    'shipping_method'      => 'ritiro',
                    'carrier_name'         => null,
                    'tracking_number'      => null,
                    'packages_count'       => 1,
                    'total_weight'         => 5.50,
                    'appearance'           => 'Scatola integra',
                    'reason'               => 'reso',
                    'shipping_date'        => now()->subDays(4),
                    'delivery_date'        => null,
                    'status'               => 'ready',
                    'notes'               => 'Reso merce per nota di credito.',
                    'created_by'           => $admin->id,
                ],
                'items' => [
                    ['description' => 'Pittura murale bianca 5L (reso)', 'quantity' => 1, 'unit' => 'pz', 'weight' => 5500.00, 'product_id' => $products->get(0)?->id],
                ],
            ],
        ];

        foreach ($ddts as $entry) {
            $ddt = TransportDocument::create($entry['data']);
            foreach ($entry['items'] as $item) {
                $ddt->items()->create($item);
            }
        }
    }
}
