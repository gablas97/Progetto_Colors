<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::first();
        $products = Product::where('manage_stock', true)->take(6)->get();

        $suppliers = [
            [
                'data' => [
                    'company_name'  => 'ColorMax S.p.A.',
                    'slug'          => 'colormax-spa',
                    'contact_name'  => 'Giovanni Martini',
                    'email'         => 'ordini@colormax.it',
                    'phone'         => '02 1234567',
                    'mobile'        => '333 1234567',
                    'vat_number'    => 'IT01234567890',
                    'tax_code'      => null,
                    'address'       => 'Via dell\'Industria 24',
                    'city'          => 'Bergamo',
                    'province'      => 'BG',
                    'postal_code'   => '24100',
                    'website'       => 'https://www.colormax.it',
                    'payment_terms' => '30 giorni fine mese',
                    'notes'         => 'Fornitore principale per vernici murali e primer.',
                    'is_active'     => true,
                ],
                'orders' => [
                    [
                        'status'                 => 'received',
                        'expected_delivery_date' => now()->subDays(20)->toDateString(),
                        'received_date'          => now()->subDays(18)->toDateString(),
                        'shipping_cost'          => 15.00,
                        'notes'                  => 'Prima fornitura stagionale.',
                        'items' => [
                            ['product' => 0, 'qty_ordered' => 50, 'qty_received' => 50, 'unit_price' => 18.00],
                            ['product' => 1, 'qty_ordered' => 30, 'qty_received' => 30, 'unit_price' => 22.50],
                        ],
                    ],
                    [
                        'status'                 => 'confirmed',
                        'expected_delivery_date' => now()->addDays(7)->toDateString(),
                        'received_date'          => null,
                        'shipping_cost'          => 20.00,
                        'notes'                  => 'Riordino urgente — scorte basse.',
                        'items' => [
                            ['product' => 0, 'qty_ordered' => 100, 'qty_received' => 0, 'unit_price' => 17.50],
                            ['product' => 2, 'qty_ordered' => 40,  'qty_received' => 0, 'unit_price' => 35.00],
                        ],
                    ],
                ],
            ],
            [
                'data' => [
                    'company_name'  => 'Pittori & Co. S.r.l.',
                    'slug'          => 'pittori-co-srl',
                    'contact_name'  => 'Francesca Neri',
                    'email'         => 'acquisti@pittorieco.it',
                    'phone'         => '011 9876543',
                    'mobile'        => null,
                    'vat_number'    => 'IT09876543210',
                    'tax_code'      => null,
                    'address'       => 'Corso Torino 88',
                    'city'          => 'Torino',
                    'province'      => 'TO',
                    'postal_code'   => '10100',
                    'website'       => null,
                    'payment_terms' => '60 giorni data fattura',
                    'notes'         => 'Specializzata in pennelli, rulli e accessori.',
                    'is_active'     => true,
                ],
                'orders' => [
                    [
                        'status'                 => 'sent',
                        'expected_delivery_date' => now()->addDays(14)->toDateString(),
                        'received_date'          => null,
                        'shipping_cost'          => 8.00,
                        'notes'                  => null,
                        'items' => [
                            ['product' => 3, 'qty_ordered' => 60, 'qty_received' => 0, 'unit_price' => 9.50],
                            ['product' => 4, 'qty_ordered' => 25, 'qty_received' => 0, 'unit_price' => 14.00],
                        ],
                    ],
                ],
            ],
            [
                'data' => [
                    'company_name'  => 'Techno Vernici S.r.l.',
                    'slug'          => 'techno-vernici-srl',
                    'contact_name'  => 'Roberto Esposito',
                    'email'         => 'r.esposito@technovernici.com',
                    'phone'         => '081 5554433',
                    'mobile'        => '347 9988776',
                    'vat_number'    => 'IT05544332211',
                    'tax_code'      => null,
                    'address'       => 'Via Napoli 15',
                    'city'          => 'Napoli',
                    'province'      => 'NA',
                    'postal_code'   => '80100',
                    'website'       => 'https://www.technovernici.com',
                    'payment_terms' => 'Pagamento anticipato',
                    'notes'         => 'Vernici tecniche e impermeabilizzanti. Minimi d\'ordine elevati.',
                    'is_active'     => true,
                ],
                'orders' => [
                    [
                        'status'                 => 'partially_received',
                        'expected_delivery_date' => now()->subDays(5)->toDateString(),
                        'received_date'          => null,
                        'shipping_cost'          => 35.00,
                        'notes'                  => 'Consegna parziale — in attesa del resto.',
                        'items' => [
                            ['product' => 2, 'qty_ordered' => 20, 'qty_received' => 12, 'unit_price' => 48.00],
                            ['product' => 5, 'qty_ordered' => 15, 'qty_received' => 15, 'unit_price' => 62.00],
                        ],
                    ],
                    [
                        'status'                 => 'draft',
                        'expected_delivery_date' => null,
                        'received_date'          => null,
                        'shipping_cost'          => 0.00,
                        'notes'                  => 'Bozza in preparazione per la prossima stagione.',
                        'items' => [
                            ['product' => 0, 'qty_ordered' => 80, 'qty_received' => 0, 'unit_price' => 16.00],
                        ],
                    ],
                    [
                        'status'                 => 'cancelled',
                        'expected_delivery_date' => now()->subDays(30)->toDateString(),
                        'received_date'          => null,
                        'shipping_cost'          => 0.00,
                        'notes'                  => 'Annullato per problemi di fornitura del prodotto.',
                        'items' => [
                            ['product' => 1, 'qty_ordered' => 10, 'qty_received' => 0, 'unit_price' => 55.00],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $supplier = Supplier::create($supplierData['data']);

            foreach ($supplierData['orders'] as $orderData) {
                $items       = $orderData['items'];
                $subtotal    = collect($items)->sum(fn ($i) => $i['qty_ordered'] * $i['unit_price']);
                $taxAmount   = round($subtotal * 0.22, 2);
                $total       = $subtotal + $taxAmount + $orderData['shipping_cost'];

                $year = now()->format('Y');
                $last = SupplierOrder::withTrashed()->where('order_number', 'like', "OF-{$year}-%")->count();
                $orderNumber = sprintf("OF-%s-%05d", $year, $last + 1);

                $order = SupplierOrder::create([
                    'order_number'           => $orderNumber,
                    'supplier_id'            => $supplier->id,
                    'status'                 => $orderData['status'],
                    'expected_delivery_date' => $orderData['expected_delivery_date'],
                    'received_date'          => $orderData['received_date'],
                    'shipping_cost'          => $orderData['shipping_cost'],
                    'subtotal'               => $subtotal,
                    'tax_amount'             => $taxAmount,
                    'total'                  => $total,
                    'notes'                  => $orderData['notes'],
                    'created_by'             => $admin->id,
                ]);

                foreach ($items as $item) {
                    $product = $products->get($item['product']);
                    if (!$product) continue;

                    $order->items()->create([
                        'product_id'        => $product->id,
                        'product_variant_id'=> null,
                        'quantity_ordered'  => $item['qty_ordered'],
                        'quantity_received' => $item['qty_received'],
                        'unit_price'        => $item['unit_price'],
                        'total_price'       => $item['qty_ordered'] * $item['unit_price'],
                    ]);
                }
            }
        }
    }
}
