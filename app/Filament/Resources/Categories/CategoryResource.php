<?php

namespace App\Filament\Resources\Categories;

use Closure;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ForceDeleteBulkAction;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';
    
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Prodotti';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Categoria';
    
    protected static ?string $pluralModelLabel = 'Categorie';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Base')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Closure $set, ?string $context = null) {
                                if ($context === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL della categoria'),

                        Forms\Components\Select::make('parent_id')
                            ->label('Categoria Padre')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Lascia vuoto per categoria principale'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Titolo')
                            ->maxLength(60)
                            ->helperText('Max 60 caratteri'),

                        Textarea::make('meta_description')
                            ->label('Meta Descrizione')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Max 160 caratteri'),
                    ])
                    ->collapsed()
                    ->columns(1),

                Section::make('Impostazioni')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Immagine')
                            ->image()
                            ->directory('categories')
                            ->imageEditor()
                            ->maxSize(2048),

                        Forms\Components\TextInput::make('order')
                            ->label('Ordine')
                            ->numeric()
                            ->default(0)
                            ->helperText('Ordine di visualizzazione (0 = primo)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Attiva')
                            ->default(true)
                            ->helperText('Mostra categoria nel sito'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Immagine')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Category $record): string => $record->description ?? ''),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Categoria Padre')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Categoria Principale')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Prodotti')
                    ->counts('products')
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->color('info'),

                Tables\Columns\TextColumn::make('order')
                    ->label('Ordine')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

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

                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Categorie')
                    ->placeholder('Tutte')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),

                Tables\Filters\TrashedFilter::make()
                    ->label('Categorie eliminate')
                    ->placeholder('Solo attive')
                    ->trueLabel('Tutte')
                    ->falseLabel('Solo eliminate'),

            ])
            ->recordAction(EditAction::class)
            ->recordActions([
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
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
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