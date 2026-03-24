<?php

namespace App\Filament\Resources\Categories;

use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\Categories\Pages\ListCategories;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';
    
    protected static string|UnitEnum|null $navigationGroup = 'Catalogo';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Categoria';
    
    protected static ?string $pluralModelLabel = 'Categorie';

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informazioni Base')
                ->icon('heroicon-o-folder')->iconColor('primary')
                ->schema([
                    TextEntry::make('name')->label('Nome')->weight('bold'),
                    TextEntry::make('parent.name')
                        ->label('Categoria Padre')
                        ->badge()
                        ->color('gray')
                        ->placeholder('Categoria Principale'),
                    IconEntry::make('is_active')->label('Attiva')->boolean()->trueColor('success')->falseColor('danger'),
                    TextEntry::make('description')
                        ->label('Descrizione')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ])->columns(3)->columnSpanFull(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Base')
                    ->icon('heroicon-o-folder')->iconColor('primary')
                    ->description('Informazioni generali')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('parent_id')
                            ->label('Categoria Padre')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Lascia vuoto per categoria principale'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Attiva')
                            ->default(true)
                            ->helperText('Mostra la categoria nel sito'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)->columnSpanFull(),

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
                    ->description(fn (Category $record): string => $record->description ?? '')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categoria Padre')
                    ->searchable()
                    ->placeholder('Categoria Principale')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Prodotti')
                    ->counts('products')
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutte')
                    ->trueLabel('Solo attive')
                    ->falseLabel('Solo disattivate'),

                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->placeholder('Tutte')
                    ->options([
                        'padre'  => 'Solo categorie padre',
                        'figlia' => 'Solo categorie figlie',
                    ])
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query, array $data) => match ($data['value'] ?? null) {
                        'padre'  => $query->whereNull('parent_id'),
                        'figlia' => $query->whereNotNull('parent_id'),
                        default  => $query,
                    }),

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Categoria Padre')
                    ->placeholder('Tutte')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\TrashedFilter::make()
                    ->label('Mostra Eliminate')
                    ->placeholder('No')
                    ->trueLabel('Si')
                    ->falseLabel('Solo Eliminate'),

            ])
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none']),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),

                    Action::make('toggle_status')
                        ->label(fn (Category $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (Category $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Category $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(fn (Category $record) => $record->update(['is_active' => !$record->is_active])),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ])->label('Azioni'),
            ])
            ->emptyStateHeading('Nessuna categoria trovata')
            ->emptyStateDescription('Crea una nuova categoria')
            ->emptyStateIcon('heroicon-o-folder');
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
            'index' => ListCategories::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}