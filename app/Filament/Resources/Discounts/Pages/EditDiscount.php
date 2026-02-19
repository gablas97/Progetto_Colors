<?php

namespace App\Filament\Resources\Discounts\Pages;

use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Discounts\DiscountResource;

class EditDiscount extends EditRecord
{
    protected static string $resource = DiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Elimina sconto'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sconto aggiornato con successo';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Converte il codice in maiuscolo
        if (!empty($data['code'])) {
            $data['code'] = strtoupper(Str::slug($data['code'], ''));
        }

        return $data;
    }
}
