<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Products\RelationManagers\ReviewsRelationManager;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Models\Product;
use BackedEnum;
use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    
    protected static string|UnitEnum|null $navigationGroup = 'Gestione Prodotti';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Prodotto';
    
    protected static ?string $pluralModelLabel = 'Prodotti';

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informazioni Prodotto')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome Prodotto')
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
                                    ->helperText('URL del prodotto, generato automaticamente'),

                                Forms\Components\Textarea::make('short_description')
                                    ->label('Descrizione Breve')
                                    ->rows(2)
                                    ->maxLength(255)
                                    ->helperText('Apparirà nelle liste prodotti'),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrizione Completa')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'bulletList',
                                        'orderedList',
                                        'link',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Immagini')
                            ->schema([
                                Forms\Components\FileUpload::make('main_image')
                                    ->label('Immagine Principale')
                                    ->image()
                                    ->directory('products')
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions([
                                        '1:1',
                                        '4:3',
                                    ])
                                    ->maxSize(2048)
                                    ->helperText('Immagine principale del prodotto'),

                                Forms\Components\Repeater::make('images')
                                    ->label('Galleria Immagini')
                                    ->relationship('images')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->label('Immagine')
                                            ->image()
                                            ->directory('products/gallery')
                                            ->imageEditor()
                                            ->required()
                                            ->maxSize(2048),

                                        Forms\Components\TextInput::make('alt_text')
                                            ->label('Testo Alternativo (SEO)')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('order')
                                            ->label('Ordine')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->orderColumn('order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Immagine')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Varianti Prodotto')
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->label('Varianti')
                                    ->relationship('variants')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome Variante')
                                            ->required()
                                            ->placeholder('es: Rosso, Blu, Large')
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
                                            ->required(),

                                        Forms\Components\FileUpload::make('image')
                                            ->label('Immagine Variante')
                                            ->image()
                                            ->directory('products/variants')
                                            ->maxSize(1024),

                                        Forms\Components\TextInput::make('order')
                                            ->label('Ordine')
                                            ->numeric()
                                            ->default(0),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Attiva')
                                            ->default(true),
                                    ])
                                    ->orderColumn('order')
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuova Variante')
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Prezzi')
                            ->schema([
                                TextInput::make('price')
                                    ->label('Prezzo')
                                    ->required()
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->minValue(0),

                                TextInput::make('compare_at_price')
                                    ->label('Prezzo di Confronto')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->helperText('Prezzo originale per mostrare sconto'),

                                TextInput::make('cost')
                                    ->label('Costo di Acquisto')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01)
                                    ->helperText('Tuo costo (per calcolare margine)'),

                                TextInput::make('vat_rate')
                                    ->label('Aliquota IVA (%)')
                                    ->numeric()
                                    ->default(22)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100),
                            ]),

                        Section::make('Inventario')
                            ->schema([
                                TextInput::make('sku')
                                    ->label('Codice SKU')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Codice univoco del prodotto'),

                                TextInput::make('barcode')
                                    ->label('Codice a Barre')
                                    ->maxLength(255),

                                Toggle::make('manage_stock')
                                    ->label('Gestisci Inventario')
                                    ->default(true)
                                    ->live()
                                    ->helperText('Attiva per tracciare quantità'),

                                TextInput::make('stock_quantity')
                                    ->label('Quantità Stock')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->hidden(fn (Get $get) => !$get('manage_stock')),

                                TextInput::make('low_stock_threshold')
                                    ->label('Soglia Stock Basso')
                                    ->numeric()
                                    ->default(10)
                                    ->helperText('Alert sotto questa quantità')
                                    ->hidden(fn (Get $get) => !$get('manage_stock')),
                            ]),

                        Section::make('Organizzazione')
                            ->schema([
                                Select::make('categories')
                                    ->label('Categorie')
                                    ->relationship('categories', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->required(),

                                Select::make('brand_id')
                                    ->label('Brand')
                                    ->relationship('brand', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('weight')
                                    ->label('Peso')
                                    ->numeric()
                                    ->suffix('g'),

                                TextInput::make('dimensions')
                                    ->label('Dimensioni')
                                    ->placeholder('LxWxH cm'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Prodotto Attivo')
                                    ->default(true),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('In Evidenza')
                                    ->default(false)
                                    ->helperText('Mostra in homepage'),

                                Forms\Components\TextInput::make('order')
                                    ->label('Ordine')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Titolo')
                                    ->maxLength(60)
                                    ->helperText('Max 60 caratteri'),

                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Descrizione')
                                    ->rows(2)
                                    ->maxLength(160)
                                    ->helperText('Max 160 caratteri'),

                                Forms\Components\TagsInput::make('meta_keywords')
                                    ->label('Keywords')
                                    ->separator(',')
                                    ->helperText('Parole chiave separate da virgola'),
                            ])
                            ->collapsed(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Immagine')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder-product.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => $record->short_description ?? '')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categorie')
                    ->badge()
                    ->separator(',')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prezzo')
                    ->money('EUR')
                    ->sortable()
                    ->description(fn (Product $record): ?string => 
                        $record->compare_at_price 
                            ? '€' . number_format($record->compare_at_price, 2) 
                            : null
                    ),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (Product $record): string => match (true) {
                        $record->manage_stock === false => 'success',
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (Product $record): string => 
                        $record->manage_stock 
                            ? $record->stock_quantity . ' pz' 
                            : '∞'
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Evidenza')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->placeholder('Tutti')
                    ->trueLabel('Solo attivi')
                    ->falseLabel('Solo disattivati'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('In Evidenza')
                    ->placeholder('Tutti')
                    ->trueLabel('Solo in evidenza')
                    ->falseLabel('Non in evidenza'),

                Tables\Filters\SelectFilter::make('categories')
                    ->label('Categoria')
                    ->placeholder('Tutte')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('low_stock')
                    ->label('Stock Basso')
                    ->query(fn (Builder $query): Builder => $query->lowStock()),

                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Esauriti')
                    ->query(fn (Builder $query): Builder => $query->outOfStock()),

                Tables\Filters\TrashedFilter::make()
                    ->label('Prodotti eliminati')
                    ->placeholder('Solo attivi')
                    ->trueLabel('Tutti')
                    ->falseLabel('Solo eliminati'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Nessun prodotto trovato')
            ->emptyStateDescription('Crea un nuovo prodotto')
            ->emptyStateIcon('heroicon-o-cube');
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
            ImagesRelationManager::class,
            ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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