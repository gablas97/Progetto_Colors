<?php

namespace App\Filament\Resources\TransportDocuments\Pages;

use App\Filament\Resources\TransportDocuments\TransportDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransportDocuments extends ListRecords
{
    protected static string $resource = TransportDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Nuovo DDT')];
    }
}
