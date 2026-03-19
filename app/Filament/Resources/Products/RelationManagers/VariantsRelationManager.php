<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Varianti Prodotto';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nome Variante')
                ->required()
                ->placeholder('es: Rosso, Blu, XL')
                ->maxLength(255),

            Forms\Components\TextInput::make('sku')
                ->label('Codice SKU')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\TextInput::make('barcode')
                ->label('Codice a Barre')
                ->maxLength(255),

            Forms\Components\TextInput::make('stock_quantity')
                ->label('Quantità Stock')
                ->numeric()
                ->default(0)
                ->required()
                ->minValue(0),

            Forms\Components\FileUpload::make('image')
                ->label('Immagine Variante')
                ->image()
                ->directory('products/variants')
                ->imageEditor()
                ->maxSize(1024)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('order')
                ->label('Ordine')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_active')
                ->label('Attiva')
                ->default(true),
        ])->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                ImageEntry::make('image')
                    ->label('Immagine')
                    ->height(150)
                    ->columnSpanFull(),

                TextEntry::make('name')
                    ->label('Nome Variante'),

                TextEntry::make('sku')
                    ->label('Codice SKU'),

                TextEntry::make('barcode')
                    ->label('Codice a Barre')
                    ->placeholder('—'),

                TextEntry::make('stock_quantity')
                    ->label('Giacenza')
                    ->suffix(' pz')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),

                TextEntry::make('order')
                    ->label('Ordine'),

                IconEntry::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Immagine')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-product.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Variante')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('SKU copiato!'),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Barcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->suffix(' pz'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutte')
                    ->trueLabel('Solo attive')
                    ->falseLabel('Solo disattivate'),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stock Basso')
                    ->query(fn ($query) => $query->where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)),

                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Esaurite')
                    ->query(fn ($query) => $query->where('stock_quantity', 0)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuova Variante'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Visualizza')
                    ->extraAttributes(['style' => 'display:none']),
                EditAction::make()
                    ->label('Modifica'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionate'),
                ]),
            ])
            ->emptyStateHeading('Nessuna variante')
            ->emptyStateDescription('Crea la prima variante per questo prodotto')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crea Variante'),
            ]);
    }
}
