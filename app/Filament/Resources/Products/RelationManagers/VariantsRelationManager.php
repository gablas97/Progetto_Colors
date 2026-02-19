<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Varianti Prodotto';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
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
                    ->maxSize(1024),

                Forms\Components\TextInput::make('order')
                    ->label('Ordine')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Attiva')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Immagine')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return $record->getRelationValue('product')->main_image 
                            ? asset('storage/' . $record->getRelationValue('product')->main_image) 
                            : url('/images/placeholder-product.png');
                    }),

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

                Tables\Columns\TextColumn::make('order')
                    ->label('Ordine')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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