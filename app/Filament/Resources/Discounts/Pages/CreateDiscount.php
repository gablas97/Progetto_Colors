<?php

namespace App\Filament\Resources\Discounts\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Discounts\DiscountResource;

class CreateDiscount extends CreateRecord
{
    protected static string $resource = DiscountResource::class;

    protected static bool $canCreateAnother = false;

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
        if (!empty($data['code'])) {
            $data['code'] = strtoupper(Str::slug($data['code'], ''));
        }

        if (($data['type'] ?? null) === 'shipping') {
            $data['value'] = 0;
        }

        return $data;
    }
}
