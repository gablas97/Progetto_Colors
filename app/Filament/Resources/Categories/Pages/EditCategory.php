<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $navigationLabel = 'Modifica Categoria';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Elimina categoria'),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Categoria aggiornata con successo';
    }

    public static function getNavigationLabel(): string
    {
        return 'Modifica Categoria';
    }
}
