<?php

namespace App\Filament\Resources\Discounts\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Discounts\DiscountResource;

class CreateDiscount extends CreateRecord
{
    protected static string $resource = DiscountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sconto creato con successo';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Converte il codice in maiuscolo
        if (!empty($data['code'])) {
            $data['code'] = strtoupper(Str::slug($data['code'], ''));
        }

        return $data;
    }
}
