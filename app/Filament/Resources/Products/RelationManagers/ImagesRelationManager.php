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

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Galleria Immagini';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Immagine')
                    ->image()
                    ->directory('products/gallery')
                    ->imageEditor()
                    ->required()
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('alt_text')
                    ->label('Testo Alternativo (SEO)')
                    ->maxLength(255)
                    ->helperText('Descrizione dell\'immagine per i motori di ricerca'),

                Forms\Components\TextInput::make('order')
                    ->label('Ordine')
                    ->numeric()
                    ->default(0)
                    ->helperText('Ordine di visualizzazione nella galleria'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt_text')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Anteprima')
                    ->square()
                    ->imageSize(80),

                Tables\Columns\TextColumn::make('alt_text')
                    ->label('Testo Alternativo')
                    ->searchable()
                    ->placeholder('Nessuna descrizione'),

                Tables\Columns\TextColumn::make('order')
                    ->label('Ordine')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Caricata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->headerActions([
                CreateAction::make()
                    ->label('Aggiungi Immagine'),
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
            ->emptyStateHeading('Nessuna immagine')
            ->emptyStateDescription('Aggiungi immagini alla galleria del prodotto')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Aggiungi Prima Immagine'),
            ]);
    }
}