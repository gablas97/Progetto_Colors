<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Galleria Immagini';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
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

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                ImageEntry::make('image')
                    ->label('Immagine')
                    ->disk('public')
                    ->height(200)
                    ->columnSpanFull(),

                TextEntry::make('alt_text')
                    ->label('Testo Alternativo')
                    ->placeholder('—'),

                TextEntry::make('order')
                    ->label('Ordine'),

                TextEntry::make('created_at')
                    ->label('Caricata il')
                    ->dateTime('d/m/Y H:i'),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('alt_text')
            ->recordUrl(null)
            ->recordAction('view')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Anteprima')
                    ->disk('public')
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
            ->emptyStateHeading('Nessuna immagine')
            ->emptyStateDescription('Aggiungi immagini alla galleria del prodotto')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Aggiungi Prima Immagine'),
            ]);
    }
}
