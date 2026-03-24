<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Gestisci Ordine #' . $this->record->order_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('accept')
                ->label('Accetta Ordine')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accetta ordine')
                ->modalDescription('L\'ordine passerà in stato "In Elaborazione". Continuare?')
                ->visible(fn () => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->update(['status' => 'processing']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Ordine accettato')->success()->send();
                }),

            Actions\Action::make('reject')
                ->label('Rifiuta Ordine')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Rifiuta ordine')
                ->modalDescription('L\'ordine verrà annullato. Questa azione non può essere annullata.')
                ->visible(fn () => in_array($this->record->status, ['pending', 'processing']))
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Ordine rifiutato')->warning()->send();
                }),

            Actions\Action::make('mark_as_shipped')
                ->label('Segna come Spedito')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'processing')
                ->action(function () {
                    if ($this->record->payment_status !== 'paid') {
                        Notification::make()
                            ->title('Pagamento non ricevuto')
                            ->body('Non è possibile spedire un ordine non ancora pagato.')
                            ->danger()
                            ->send();
                        return;
                    }
                    $this->record->markAsShipped();
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Ordine segnato come spedito')->success()->send();
                }),

            Actions\Action::make('mark_as_delivered')
                ->label('Segna come Consegnato')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'shipped')
                ->action(function () {
                    $this->record->markAsDelivered();
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Ordine consegnato')->success()->send();
                }),

            Actions\Action::make('download_invoice')
                ->label('Scarica Fattura PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    $invoice = OrderResource::buildInvoiceData($this->record);
                    return response()->streamDownload(function () use ($invoice) {
                        echo Pdf::loadView('pdf.invoice', ['invoice' => $invoice])->output();
                    }, "FATTURA-{$this->record->order_number}.pdf");
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $newStatus = $this->data['status'] ?? null;

        if (
            in_array($newStatus, ['shipped', 'delivered']) &&
            $this->record->payment_status !== 'paid'
        ) {
            Notification::make()
                ->title('Operazione non consentita')
                ->body('Non è possibile impostare questo stato per un ordine non ancora pagato.')
                ->danger()
                ->send();

            throw new Halt;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Ordine aggiornato con successo';
    }
}
