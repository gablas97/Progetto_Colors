<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentReviews extends TableWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Ultime Recensioni')
            ->query(fn (): Builder => Review::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Valutazione')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('Titolo')
                    ->limit(40)
                    ->placeholder('Nessun titolo'),

                IconColumn::make('is_verified_purchase')
                    ->label('Verificato')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_approved')
                    ->label('Approvata')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approva')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_approved)
                    ->action(function ($record) {
                        $record->update(['is_approved' => true]);
                        $record->product->updateRatings();
                    }),
                
                Action::make('view')
                    ->label('Visualizza')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => 'Recensione: ' . $record->product->name)
                    ->modalContent(fn ($record) => view('filament.widgets.review-modal', ['review' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Chiudi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->paginated(false);
    }
}
