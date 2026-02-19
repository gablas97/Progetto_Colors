<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Recensioni Prodotto';

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                TextEntry::make('user.email')
                    ->label('Utente'),
                
                TextEntry::make('rating')
                    ->label('Valutazione')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),
                
                TextEntry::make('title')
                    ->label('Titolo'),
                
                TextEntry::make('comment')
                    ->label('Commento')
                    ->columnSpanFull(),
                
                TextEntry::make('is_approved')
                    ->label('Approvata')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Sì' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Valutazione')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->limit(30)
                    ->searchable()
                    ->placeholder('Nessun titolo'),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approvata')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_verified_purchase')
                    ->label('Verificato')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approvate')
                    ->placeholder('Tutte')
                    ->trueLabel('Solo approvate')
                    ->falseLabel('Solo in attesa'),
            ])
            ->recordActions([
                ViewAction::make(),
                
                Action::make('approve')
                    ->label('Approva')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_approved)
                    ->action(function ($record) {
                        $record->update(['is_approved' => true]);
                        $record->product->updateRatings();
                    }),
                
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nessuna recensione')
            ->emptyStateDescription('Le recensioni dei clienti appariranno qui')
            ->emptyStateIcon('heroicon-o-star');
    }
}