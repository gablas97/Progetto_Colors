<?php

namespace App\Filament\Resources\TransportDocuments\Pages;

use App\Filament\Resources\TransportDocuments\TransportDocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransportDocument extends CreateRecord
{
    protected static string $resource = TransportDocumentResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'DDT creato con successo';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
