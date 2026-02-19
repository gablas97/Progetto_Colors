<?php

namespace App\Filament\Resources\TransportDocuments\Pages;

use App\Filament\Resources\TransportDocuments\TransportDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportDocument extends EditRecord
{
    protected static string $resource = TransportDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
