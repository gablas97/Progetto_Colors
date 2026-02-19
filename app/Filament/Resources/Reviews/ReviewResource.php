<?php

namespace App\Filament\Resources\Reviews;

use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use App\Models\Review;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\Reviews\Pages\ListReviews;
use Filament\Actions\ActionGroup;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';
    
    protected static string|UnitEnum|null $navigationGroup = 'Vendite';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Recensione';
    
    protected static ?string $pluralModelLabel = 'Recensioni';

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                Section::make('Dettagli Recensione')
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Prodotto')
                            ->color('primary')
                            ->weight('bold'),
                        
                        TextEntry::make('user.full_name')
                            ->label('Utente')
                            ->default(fn ($record) => $record->user->full_name ?? $record->user->email),
                        
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        
                        TextEntry::make('order.order_number')
                            ->label('Ordine')
                            ->color('primary')
                            ->placeholder('Nessun ordine collegato'),

                        TextEntry::make('created_at')
                            ->label('Data Creazione')
                            ->dateTime('d/m/Y H:i'),
                        
                        TextEntry::make('updated_at')
                            ->label('Ultimo Aggiornamento')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->created_at != $record->updated_at),    
                        
                        TextEntry::make('rating')
                            ->label('Valutazione')
                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . " ($state/5)")
                            ->color('warning')
                            ->weight('bold'),
                        
                        TextEntry::make('is_verified_purchase')
                            ->label('Acquisto Verificato')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Sì' : 'No')
                            ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                        
                        TextEntry::make('is_approved')
                            ->label('Stato')
                            ->badge()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Approvata' : 'In Attesa')
                            ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                        
                        TextEntry::make('helpful_count')
                            ->label('Voti "Utile"')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),

                Section::make('Contenuto Recensione')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Titolo')
                            ->placeholder('Nessun titolo')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),
                        
                        TextEntry::make('comment')
                            ->label('Commento')
                            ->placeholder('Nessun commento')
                            ->columnSpanFull()
                            ->prose(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.sku')
                    ->label('Prodotto')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('user.email')
                    ->label('Utente')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('rating')
                    ->label('Valutazione')
                    ->sortable()
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('Nessun titolo'),

                TextColumn::make('comment')
                    ->label('Commento')
                    ->limit(50)
                    ->placeholder('Nessun commento')
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_verified_purchase')
                    ->label('Verificato')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->tooltip(fn ($record) => $record->is_verified_purchase ? 'Acquisto verificato' : 'Non verificato'),

                IconColumn::make('is_approved')
                    ->label('Approvata')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('helpful_count')
                    ->label('Voti "Utile"')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? (string) $state : '0'),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Valutazione')
                    ->placeholder('Tutte')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ 5 stelle',
                        4 => '⭐⭐⭐⭐ 4 stelle',
                        3 => '⭐⭐⭐ 3 stelle',
                        2 => '⭐⭐ 2 stelle',
                        1 => '⭐ 1 stella',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Stato Approvazione')
                    ->placeholder('Tutte')
                    ->trueLabel('Solo approvate')
                    ->falseLabel('Solo in attesa'),

                Tables\Filters\TernaryFilter::make('is_verified_purchase')
                    ->label('Acquisto Verificato')
                    ->placeholder('Tutte')
                    ->trueLabel('Solo verificate')
                    ->falseLabel('Solo non verificate'),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Prodotto')
                    ->placeholder('Tutti')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Al'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordAction(ViewAction::class)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    
                    Action::make('approve')
                        ->label('Approva')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => !$record->is_approved)
                        ->action(function ($record) {
                            $record->update(['is_approved' => true]);
                            $record->product->updateRatings();
                        })
                        ->successNotificationTitle('Recensione approvata'),
                    
                    Action::make('reject')
                        ->label('Rifiuta')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->is_approved)
                        ->action(function ($record) {
                            $record->update(['is_approved' => false]);
                            $record->product->updateRatings();
                        })
                        ->successNotificationTitle('Recensione rifiutata'),
                    
                    DeleteAction::make()
                        ->requiresConfirmation(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approva Selezionate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_approved' => true]);
                                $record->product->updateRatings();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Recensioni approvate'),
                    
                    BulkAction::make('reject_selected')
                        ->label('Rifiuta Selezionate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_approved' => false]);
                                $record->product->updateRatings();
                            }
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Recensioni rifiutate'),
                    
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionate'),
                ])->label('Azioni'),
            ])
            ->emptyStateHeading('Nessuna recensione trovata')
            ->emptyStateDescription('Le recensioni appariranno qui quando i clienti le scriveranno')
            ->emptyStateIcon('heroicon-o-star');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('is_approved', false)->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}