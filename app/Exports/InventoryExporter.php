<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExporter implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return Product::query()
            ->where('is_active', true)
            ->where('manage_stock', true)
            ->with(['brand', 'categories'])
            ->orderBy('stock_quantity');
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Prodotto',
            'Brand',
            'Categorie',
            'Giacenza',
            'Soglia Stock Basso',
            'Stato Stock',
            'Costo (€)',
            'Prezzo (€)',
            'Valore Stock (€)',
            'Codice a Barre',
        ];
    }

    public function map($product): array
    {
        $stockStatus = match (true) {
            $product->stock_quantity <= 0                                        => 'Esaurito',
            $product->stock_quantity <= $product->low_stock_threshold            => 'Stock Basso',
            default                                                               => 'Disponibile',
        };

        return [
            $product->sku,
            $product->name,
            $product->brand?->name ?? '-',
            $product->categories->pluck('name')->join(', '),
            $product->stock_quantity,
            $product->low_stock_threshold,
            $stockStatus,
            number_format($product->cost ?? 0, 2),
            number_format($product->price, 2),
            number_format($product->stock_quantity * ($product->cost ?? $product->price), 2),
            $product->barcode ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
