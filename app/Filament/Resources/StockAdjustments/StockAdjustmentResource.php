<?php

namespace App\Filament\Resources\StockAdjustments;

use App\Filament\Resources\StockAdjustments\Pages;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static string|UnitEnum|null $navigationGroup = 'Magazzino';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Rettifica Stock';

    protected static ?string $pluralModelLabel = 'Rettifiche Stock';

    public static function schema(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Rettifica Stock')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Prodotto')
                        ->options(Product::where('is_active', true)->where('manage_stock', true)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('product_variant_id', null)),

                    Forms\Components\Select::make('product_variant_id')
                        ->label('Variante (opzionale)')
                        ->options(fn (Get $get) => ProductVariant::where('product_id', $get('product_id'))
                            ->where('is_active', true)
                            ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->placeholder('Nessuna variante')
                        ->hidden(fn (Get $get) => !$get('product_id')),

                    Forms\Components\Radio::make('type')
                        ->label('Tipo Operazione')
                        ->options([
                            'manual_load'   => 'Carico (aumenta stock)',
                            'manual_unload' => 'Scarico (diminuisce stock)',
                        ])
                        ->required()
                        ->inline(),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantità')
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->required(),

                    Forms\Components\Textarea::make('reason')
                        ->label('Motivo')
                        ->rows(2)
                        ->placeholder('Descrivi il motivo della rettifica...'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(StockLog::query()
                ->whereIn('type', ['manual_load', 'manual_unload'])
                ->with(['product', 'productVariant', 'user'])
                ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'manual_load'   => 'Carico',
                        'manual_unload' => 'Scarico',
                        default         => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'manual_load'   => 'success',
                        'manual_unload' => 'danger',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Variante')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qtà')
                    ->formatStateUsing(fn (StockLog $record) =>
                        ($record->type === 'manual_load' ? '+' : '-') . $record->quantity
                    )
                    ->color(fn (StockLog $record) =>
                        $record->type === 'manual_load' ? 'success' : 'danger'
                    ),

                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('Prima')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('Dopo')
                    ->badge()
                    ->color(fn (StockLog $record) => $record->quantity_after <= 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Motivo')
                    ->limit(40)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Operatore')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'manual_load'   => 'Carico',
                        'manual_unload' => 'Scarico',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('Dal'),
                        Forms\Components\DatePicker::make('until')->label('Al'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nessuna rettifica')
            ->emptyStateDescription('Usa il bottone "Nuova Rettifica" per caricare o scaricare stock manualmente.')
            ->emptyStateIcon('heroicon-o-arrows-up-down');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStockAdjustments::route('/'),
            'create' => Pages\CreateStockAdjustment::route('/create'),
        ];
    }
}
