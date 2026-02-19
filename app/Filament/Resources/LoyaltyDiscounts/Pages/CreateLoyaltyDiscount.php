<?php

namespace App\Filament\Resources\LoyaltyDiscounts\Pages;

use App\Filament\Resources\LoyaltyDiscounts\LoyaltyDiscountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoyaltyDiscount extends CreateRecord
{
    protected static string $resource = LoyaltyDiscountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
