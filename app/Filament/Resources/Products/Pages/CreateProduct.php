<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Prodotto creato con successo';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['name'];
        }

        if (empty($data['meta_description']) && !empty($data['short_description'])) {
            $data['meta_description'] = $data['short_description'];
        }

        return $data;
    }
}
