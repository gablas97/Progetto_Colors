<?php

namespace App\Filament\Resources\LoyaltyCards\Pages;

use App\Filament\Resources\LoyaltyCards\LoyaltyCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyCard extends EditRecord
{
    protected static string $resource = LoyaltyCardResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
