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
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
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

    protected static string|UnitEnum|null $navigationGroup = 'Vendite';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Sconto';

    protected static ?string $pluralModelLabel = 'Sconti';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Sconto')
                    ->icon('heroicon-o-tag')->iconColor('primary')
                    ->description('Dati principali del codice sconto')
                    ->aside()
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
                            ->placeholder('Seleziona il tipo')
                            ->selectablePlaceholder(false)
                            ->options([
                                'percentage' => 'Percentuale',
                                'fixed' => 'Importo Fisso',
                                'shipping' => 'Spedizione Gratuita',
                            ])
                            ->default('percentage')
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('value')
                            ->label(fn (Get $get) => $get('type') === 'percentage' ? 'Percentuale (%)' : 'Importo (€)')
                            ->required(fn (Get $get) => $get('type') !== 'shipping')
                            ->hidden(fn (Get $get) => $get('type') === 'shipping')
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
                    ->columns(3)->columnSpanFull(),

                Section::make('Limitazioni e Validità')
                    ->icon('heroicon-o-shield-check')->iconColor('warning')
                    ->description('Utilizzi, date di validità e restrizioni')
                    ->aside()
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
                            ->dehydrated(false)
                            ->hidden(fn (string $operation) => $operation === 'create'),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Data Inizio')
                            ->nullable()
                            ->helperText('Lascia vuoto per attivo da subito'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Data Scadenza')
                            ->nullable()
                            ->helperText('Lascia vuoto per nessuna scadenza'),

                        Forms\Components\Toggle::make('is_single_use_per_user')
                            ->label('Un solo utilizzo per account')
                            ->default(false)
                            ->helperText('Ogni account può usare questo sconto una sola volta')
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true)
                            ->helperText('Attiva/disattiva lo sconto'),
                    ])
                    ->columns(3)->columnSpanFull(),

                Section::make('Applicabilità')
                    ->icon('heroicon-o-cube')->iconColor('gray')
                    ->description('Lascia tutto vuoto per applicare lo sconto a tutti i prodotti')
                    ->aside()
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Categorie')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Applica a tutte le categorie selezionate'),

                        Forms\Components\Select::make('brands')
                            ->label('Marche')
                            ->relationship('brands', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Applica a tutte le marche selezionate'),

                        Forms\Components\Select::make('products')
                            ->label('Prodotti Specifici')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Applica a prodotti specifici'),
                    ])
                    ->columns(1)
                    ->collapsed()->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Sconto')
                    ->icon('heroicon-o-tag')->iconColor('primary')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome Sconto')
                            ->weight('bold'),

                        TextEntry::make('code')
                            ->label('Codice Coupon')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->placeholder('Sconto automatico'),

                        TextEntry::make('type')
                            ->label('Tipo')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'percentage' => 'Percentuale',
                                'fixed' => 'Importo Fisso',
                                'shipping' => 'Spedizione Gratuita',
                                default => $state,
                            })
                            ->badge()
                            ->color('info'),

                        TextEntry::make('value')
                            ->label('Valore')
                            ->formatStateUsing(fn (Discount $record): string => match ($record->type) {
                                'percentage' => $record->value . '%',
                                'shipping'   => '—',
                                default      => '€ ' . number_format($record->value, 2, ',', '.'),
                            })
                            ->weight('bold'),

                        TextEntry::make('min_order_amount')
                            ->label('Importo Minimo Ordine')
                            ->formatStateUsing(fn ($state): string => $state ? '€ ' . number_format($state, 2, ',', '.') : '—')
                            ->placeholder('Nessun minimo'),
                    ])
                    ->columns(3)->columnSpanFull(),

                Section::make('Limitazioni e Validità')
                    ->icon('heroicon-o-shield-check')->iconColor('warning')
                    ->schema([
                        TextEntry::make('usage_count')
                            ->label('Utilizzi')
                            ->formatStateUsing(fn (Discount $record): string =>
                                $record->usage_count . ($record->usage_limit ? ' / ' . $record->usage_limit : ' (illimitato)')
                            )
                            ->badge()
                            ->color(fn (Discount $record): string =>
                                $record->usage_limit && $record->usage_count >= $record->usage_limit ? 'danger' : 'success'
                            ),

                        IconEntry::make('is_active')
                            ->label('Attivo')
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger'),

                        IconEntry::make('is_single_use_per_user')
                            ->label('Un solo utilizzo per account')
                            ->boolean()
                            ->trueIcon('heroicon-o-user-circle')
                            ->trueColor('warning'),

                        TextEntry::make('starts_at')
                            ->label('Data Inizio')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Subito'),

                        TextEntry::make('expires_at')
                            ->label('Data Scadenza')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Nessuna scadenza')
                            ->color(fn ($state): string =>
                                $state && now()->gt($state) ? 'danger' : 'gray'
                            ),
                    ])
                    ->columns(3)->columnSpanFull(),

                Section::make('Cronologia Utilizzi')
                    ->icon('heroicon-o-clock')->iconColor('gray')
                    ->schema([
                        RepeatableEntry::make('usages')
                            ->label('Utilizzi')
                            ->placeholder('Nessun utilizzo')
                            ->schema([
                                TextEntry::make('user.first_name')
                                    ->label('Nome')
                                    ->formatStateUsing(fn ($state, $record) => trim(($record->user->first_name ?? '') . ' ' . ($record->user->last_name ?? '')) ?: '—'),
                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->placeholder('—'),
                                TextEntry::make('order_id')
                                    ->label('Ordine #')
                                    ->placeholder('—'),
                                TextEntry::make('used_at')
                                    ->label('Data utilizzo')
                                    ->dateTime('d/m/Y H:i'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(fn (Discount $record) => $record->usages()->count() === 0),
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
                    ->weight('medium')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->placeholder('—')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percentuale',
                        'fixed' => 'Fisso',
                        'shipping' => 'Spedizione',
                        default => 'N/A',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valore')
                    ->formatStateUsing(fn (Discount $record): string => match ($record->type) {
                        'percentage' => number_format($record->value, 2, ',', '.') . '%',
                        'shipping'   => '—',
                        default      => number_format($record->value, 2, ',', '.') . ' €',
                    })
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

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
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Scadenza')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Mai')
                    ->sortable()
                    ->color(fn ($state): string =>
                        $state && now()->gt($state) ? 'danger' : 'gray'
                    )
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_single_use_per_user')
                    ->label('1x account')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('expires_at', 'asc')
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
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
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalWidth('5xl'),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),

                    Action::make('toggle_status')
                        ->label(fn (Discount $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (Discount $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Discount $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Discount $record) => $record->is_active ? 'Disattiva sconto' : 'Attiva sconto')
                        ->modalDescription(fn (Discount $record) => $record->is_active
                            ? 'Lo sconto non sarà più utilizzabile. Vuoi continuare?'
                            : 'Lo sconto tornerà utilizzabile sul sito. Vuoi continuare?')
                        ->action(fn (Discount $record) => $record->update(['is_active' => !$record->is_active]))
                        ->successNotificationTitle(fn(Discount $record) => ($record->is_active ? 'Sconto attivato' : 'Sconto disattivato')),
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
