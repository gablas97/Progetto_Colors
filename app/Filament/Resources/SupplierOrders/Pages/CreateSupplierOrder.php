<?php
namespace App\Filament\Resources\SupplierOrders\Pages;
use App\Filament\Resources\SupplierOrders\SupplierOrderResource;
use Filament\Resources\Pages\CreateRecord;
class CreateSupplierOrder extends CreateRecord
{
    protected static string $resource = SupplierOrderResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
