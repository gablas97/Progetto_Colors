<?php

namespace App\Filament\Resources\LoyaltyDiscounts\Pages;

use App\Filament\Resources\LoyaltyDiscounts\LoyaltyDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyDiscounts extends ListRecords
{
    protected static string $resource = LoyaltyDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Nuovo Sconto Fedeltà')];
    }
}
