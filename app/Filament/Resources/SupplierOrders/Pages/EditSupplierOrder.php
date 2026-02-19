<?php
namespace App\Filament\Resources\SupplierOrders\Pages;
use App\Filament\Resources\SupplierOrders\SupplierOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditSupplierOrder extends EditRecord
{
    protected static string $resource = SupplierOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
