<?php


namespace App\Filament\Resources\Invoices\Pages;

use BackedEnum;
use UnitEnum;
use App\Models\Invoice;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class RecurringInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;
    protected static ?string $title = 'Fatture Ricorrenti';
    protected static ?string $navigationLabel = 'Ricorrenti';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected static string|UnitEnum|null $navigationGroup = 'Fatturazione';
    protected static ?int $navigationSort = 2;

    protected function getTableQuery(): Builder
    {
        return Invoice::query()->where('is_recurring', true);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return true;
    }
}
