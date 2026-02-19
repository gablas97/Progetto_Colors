<?php

namespace App\Filament\Resources\Discounts;

use App\Filament\Resources\Discounts\Pages\CreateDiscount;
use App\Filament\Resources\Discounts\Pages\EditDiscount;
use App\Filament\Resources\Discounts\Pages\ListDiscounts;
use App\Models\Discount;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    
    protected static string|UnitEnum|null $navigationGroup = 'Marketing';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Sconto';
    
    protected static ?string $pluralModelLabel = 'Sconti';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Sconto')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Sconto')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nome descrittivo interno'),

                        Forms\Components\TextInput::make('code')
                            ->label('Codice Coupon')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Lascia vuoto se sconto automatico')
                            ->nullable(),

                        Forms\Components\Select::make('type')
                            ->label('Tipo Sconto')
                            ->options([
                                'percentage' => 'Percentuale',
                                'fixed' => 'Importo Fisso',
                                'shipping' => 'Spedizione Gratuita',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('value')
                            ->label(fn (Get $get) => match ($get('type')) {
                                'percentage' => 'Percentuale (%)',
                                'fixed' => 'Importo (€)',
                                'shipping' => 'Costo Spedizione (€)',
                                default => 'Valore',
                            })
                            ->required()
                            ->numeric()
                            ->suffix(fn (Get $get) => $get('type') === 'percentage' ? '%' : '€')
                            ->minValue(0)
                            ->maxValue(fn (Get $get) => $get('type') === 'percentage' ? 100 : null),

                        Forms\Components\TextInput::make('min_order_amount')
                            ->label('Importo Minimo Ordine')
                            ->numeric()
                            ->prefix('€')
                            ->nullable()
                            ->helperText('Ordine minimo per applicare lo sconto'),
                    ])
                    ->columns(2),

                Section::make('Limitazioni')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Limite Utilizzi Totali')
                            ->numeric()
                            ->nullable()
                            ->helperText('Lascia vuoto per illimitato'),

                        Forms\Components\TextInput::make('usage_count')
                            ->label('Utilizzi Attuali')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Data Inizio')
                            ->nullable()
                            ->helperText('Lascia vuoto per attivo da subito'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Data Scadenza')
                            ->nullable()
                            ->helperText('Lascia vuoto per nessuna scadenza'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true)
                            ->helperText('Attiva/disattiva lo sconto'),
                    ])
                    ->columns(2),

                Section::make('Prodotti Specifici')
                    ->schema([
                        Forms\Components\Select::make('products')
                            ->label('Applica Solo a Prodotti Specifici')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Lascia vuoto per applicare a tutti i prodotti'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->placeholder('Auto'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percentuale',
                        'fixed' => 'Fisso',
                        'shipping' => 'Spedizione',
                        default => 'N/A',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'gray',
                        'fixed' => 'gray',
                        'shipping' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valore')
                    ->formatStateUsing(fn (Discount $record): string => 
                        $record->type === 'percentage' 
                            ? $record->value . '%' 
                            : '€' . number_format($record->value, 2)
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Utilizzi')
                    ->formatStateUsing(fn (Discount $record): string => 
                        $record->usage_count . ($record->usage_limit ? ' / ' . $record->usage_limit : '')
                    )
                    ->badge()
                    ->color(fn (Discount $record): string => 
                        $record->usage_limit && $record->usage_count >= $record->usage_limit 
                            ? 'danger' 
                            : 'success'
                    )
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Scadenza')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Mai')
                    ->sortable()
                    ->color(fn ($state): string => 
                        $state && now()->gt($state) ? 'danger' : 'gray'
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('expires_at', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutti')
                    ->trueLabel('Solo attivi')
                    ->falseLabel('Solo disattivati'),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->placeholder('Tutti')
                    ->options([
                        'percentage' => 'Percentuale',
                        'fixed' => 'Fisso',
                        'shipping' => 'Spedizione',
                    ]),

                Tables\Filters\Filter::make('expired')
                    ->label('Scaduti')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),

                Tables\Filters\Filter::make('active_valid')
                    ->label('Attivi e Validi')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                              ->where(function ($q) {
                                  $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>=', now());
                              })
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    
                    Action::make('toggle_status')
                        ->label(fn (Discount $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (Discount $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Discount $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn (Discount $record) => $record->update(['is_active' => !$record->is_active])),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Nessuno sconto trovato')
            ->emptyStateDescription('Aggiungi un nuovo sconto')
            ->emptyStateIcon('heroicon-o-tag');
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
            'index' => ListDiscounts::route('/'),
            'create' => CreateDiscount::route('/create'),
            'edit' => EditDiscount::route('/{record}/edit'),
        ];
    }
}