<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
    
    protected static string|UnitEnum|null $navigationGroup = 'Catalogo';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Prodotto';
    
    protected static ?string $pluralModelLabel = 'Prodotti';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make()
                    ->tabs([

                        Tab::make('Informazioni')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Dati principali')
                                    ->description('Nome e URL del prodotto nel sito.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nome prodotto')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                                if (empty($get('sku'))) {
                                                    $set('sku', strtoupper(Str::slug($state ?? '')));
                                                }
                                            }),
                                    ]),

                                Section::make('Descrizioni')
                                    ->description('Testi mostrati nella pagina prodotto e nelle anteprime.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Textarea::make('short_description')
                                            ->label('Descrizione breve')
                                            ->rows(3)
                                            ->maxLength(255)
                                            ->helperText('Visibile nelle liste prodotti e come anteprima. Usata anche come meta description SEO se non specificata.'),

                                        Forms\Components\RichEditor::make('description')
                                            ->label('Descrizione completa')
                                            ->toolbarButtons([
                                                'bold', 'italic', 'underline',
                                                'bulletList', 'orderedList',
                                                'link', 'undo', 'redo',
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Prezzi & Inventario')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Prezzi')
                                    ->description('Definisci il prezzo di vendita e i riferimenti interni.')
                                    ->aside()
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Prezzo di vendita')
                                            ->required()
                                            ->numeric()
                                            ->prefix('€')
                                            ->minValue(0),

                                        TextInput::make('compare_at_price')
                                            ->label('Prezzo barrato')
                                            ->numeric()
                                            ->prefix('€')
                                            ->helperText('Se compilato, viene mostrato come "era X €" per evidenziare lo sconto.'),

                                        TextInput::make('cost')
                                            ->label('Costo di acquisto')
                                            ->numeric()
                                            ->prefix('€')
                                            ->helperText('Solo uso interno — serve per calcolare il margine. Non visibile ai clienti.'),

                                        TextInput::make('vat_rate')
                                            ->label('Aliquota IVA')
                                            ->numeric()
                                            ->default(22)
                                            ->suffix('%')
                                            ->minValue(0)
                                            ->maxValue(100),
                                    ]),

                                Section::make('Inventario')
                                    ->description('Codici identificativi e gestione delle scorte.')
                                    ->aside()
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('Codice SKU')
                                            ->unique(ignoreRecord: true)
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Generato automaticamente dal nome del prodotto. Modificabile manualmente se necessario.'),

                                        TextInput::make('barcode')
                                            ->label('Codice a barre / EAN')
                                            ->maxLength(255)
                                            ->helperText('EAN-13 o codice personalizzato. Facoltativo.'),

                                        Toggle::make('manage_stock')
                                            ->label('Gestione scorte attiva')
                                            ->default(true)
                                            ->live()
                                            ->helperText('Disattiva per prodotti digitali o con disponibilità illimitata.'),

                                        TextInput::make('stock_quantity')
                                            ->label('Quantità in magazzino')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->hidden(fn (Get $get) => ! $get('manage_stock')),

                                        TextInput::make('low_stock_threshold')
                                            ->label('Soglia scorta bassa')
                                            ->numeric()
                                            ->default(10)
                                            ->helperText('Riceverai una notifica quando la quantità scende sotto questo valore.')
                                            ->hidden(fn (Get $get) => ! $get('manage_stock')),
                                    ]),
                            ]),

                        Tab::make('Organizzazione')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Section::make('Classificazione')
                                    ->description('Categorie e brand a cui appartiene il prodotto.')
                                    ->aside()
                                    ->schema([
                                        Select::make('categories')
                                            ->label('Categorie')
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descrizione')
                                                    ->rows(2),
                                            ])
                                            ->createOptionUsing(fn (array $data) => Category::create($data)->getKey()),

                                        Select::make('brand_id')
                                            ->label('Brand')
                                            ->relationship('brand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('website')
                                                    ->label('Sito Web')
                                                    ->url()
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Descrizione')
                                                    ->rows(2),
                                            ])
                                            ->createOptionUsing(fn (array $data) => Brand::create($data)->getKey()),
                                    ]),

                                Section::make('Dettagli fisici')
                                    ->description('Peso e dimensioni — usati per calcolare i costi di spedizione.')
                                    ->aside()
                                    ->schema([
                                        TextInput::make('weight')
                                            ->label('Peso')
                                            ->numeric()
                                            ->suffix('g')
                                            ->helperText('Inserisci il peso in grammi.'),

                                        TextInput::make('dimensions')
                                            ->label('Dimensioni')
                                            ->placeholder('es: 30x20x10')
                                            ->helperText('Lunghezza × Larghezza × Altezza in cm.'),
                                    ]),

                                Section::make('Visibilità')
                                    ->description('Controlla dove e come appare il prodotto sul sito.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Prodotto attivo')
                                            ->default(true)
                                            ->live()
                                            ->helperText('Se disattivato, il prodotto non sarà visibile ai clienti.')
                                            ->afterStateUpdated(function (bool $state, Set $set) {
                                                if (! $state) {
                                                    $set('is_featured', false);
                                                }
                                            }),

                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('In evidenza')
                                            ->default(false)
                                            ->disabled(fn (Get $get) => ! $get('is_active'))
                                            ->helperText(fn (Get $get) => ! $get('is_active')
                                                ? 'Attiva il prodotto per poterlo mettere in evidenza.'
                                                : 'Mostrato in homepage e nelle sezioni "Prodotti in evidenza".'),

                                    ]),
                            ]),

                        Tab::make('Immagini')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Immagine principale')
                                    ->description('Immagine mostrata nella lista prodotti e in cima alla pagina prodotto.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\FileUpload::make('main_image')
                                            ->label('Immagine principale')
                                            ->image()
                                            ->required()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('products')
                                            ->imageEditor()
                                            ->imageEditorAspectRatioOptions(['1:1', '4:3'])
                                            ->maxSize(2048)
                                            ->helperText('Formato consigliato: 800×800px · Max 2 MB.'),
                                    ]),

                                Section::make('Galleria immagini')
                                    ->description('Immagini aggiuntive mostrate nel carosello della pagina prodotto.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Repeater::make('images')
                                            ->label('Immagini')
                                            ->relationship('images')
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Immagine')
                                                    ->image()
                                                    ->disk('public')
                                                    ->visibility('public')
                                                    ->directory('products/gallery')
                                                    ->imageEditor()
                                                    ->required()
                                                    ->maxSize(2048)
                                                    ->columnSpanFull(),

                                                Forms\Components\TextInput::make('alt_text')
                                                    ->label('Testo alternativo')
                                                    ->maxLength(255)
                                                    ->helperText('Breve descrizione dell\'immagine (accessibilità e SEO).'),

                                                Forms\Components\TextInput::make('order')
                                                    ->label('Ordine')
                                                    ->numeric()
                                                    ->default(0),
                                            ])
                                            ->orderColumn('order')
                                            ->reorderable()
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Immagine')
                                            ->columns(3)
                                            ->addActionLabel('Aggiungi immagine'),
                                    ]),
                            ]),

                        Tab::make('Varianti')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                Section::make('Varianti prodotto')
                                    ->description('Versioni dello stesso prodotto con caratteristiche diverse (colore, formato, taglia, ecc.). Lascia vuoto se il prodotto non ha varianti.')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Repeater::make('variants')
                                            ->label('Varianti')
                                            ->relationship('variants')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nome variante')
                                                    ->required()
                                                    ->placeholder('es: Rosso, 500ml, Large')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('sku')
                                                    ->label('SKU variante')
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('barcode')
                                                    ->label('Codice a barre')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('stock_quantity')
                                                    ->label('Quantità')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required()
                                                    ->hidden(fn (Get $get) => ! $get('/manage_stock')),

                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Immagine variante')
                                                    ->image()
                                                    ->disk('public')
                                                    ->visibility('public')
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
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nuova variante')
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Aggiungi variante'),
                                    ]),
                            ]),


                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Tabs::make()
                ->tabs([

                    Tab::make('Panoramica')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Group::make([
                                Section::make('Dettagli')->icon('heroicon-o-information-circle')->iconColor('primary')->schema([
                                    ImageEntry::make('main_image')
                                        ->label('Immagine principale')
                                        ->disk('public')
                                        ->imageHeight(220)
                                        ->columnSpanFull()
                                        ->placeholder('—'),

                                    TextEntry::make('name')
                                        ->label('Nome')
                                        ->weight('bold')
                                        ->size('lg'),

                                    TextEntry::make('sku')
                                        ->label('SKU')
                                        ->badge()
                                        ->color('gray'),

                                    TextEntry::make('categories.name')
                                        ->label('Categorie')
                                        ->badge()
                                        ->separator(', '),

                                    TextEntry::make('brand.name')
                                        ->label('Brand')
                                        ->color('primary')
                                        ->placeholder('—'),

                                    IconEntry::make('is_active')
                                        ->label('Attivo')
                                        ->boolean()
                                        ->trueColor('success')
                                        ->falseColor('danger'),

                                    IconEntry::make('is_featured')
                                        ->label('In evidenza')
                                        ->boolean()
                                        ->trueColor('warning')
                                        ->falseColor('gray'),
                                ])->columns(2),

                                Section::make('Descrizioni')->icon('heroicon-o-document-text')->iconColor('gray')->schema([
                                    TextEntry::make('short_description')
                                        ->label('Breve')
                                        ->placeholder('—')
                                        ->columnSpanFull(),

                                    TextEntry::make('description')
                                        ->label('Completa')
                                        ->html()
                                        ->placeholder('—')
                                        ->columnSpanFull(),
                                ]),

                                Section::make('Prezzi')->icon('heroicon-o-currency-euro')->iconColor('success')->schema([
                                    TextEntry::make('price')
                                        ->label('Prezzo finale')
                                        ->money('EUR')
                                        ->weight('bold')
                                        ->color('success'),

                                    TextEntry::make('compare_at_price')
                                        ->label('Prezzo originale')
                                        ->money('EUR')
                                        ->placeholder('—'),

                                    TextEntry::make('cost')
                                        ->label('Costo acquisto')
                                        ->money('EUR')
                                        ->placeholder('—'),

                                    TextEntry::make('vat_rate')
                                        ->label('IVA')
                                        ->suffix('%'),
                                ])->columns(4),
                            ])->columnSpan(2),

                            Group::make([
                                Section::make('Scorte')->icon('heroicon-o-archive-box')->iconColor('warning')->schema([
                                    TextEntry::make('stock_quantity')
                                        ->label('Giacenza')
                                        ->badge()
                                        ->color(fn (Product $record): string => match (true) {
                                            ! $record->manage_stock => 'gray',
                                            $record->stock_quantity === 0 => 'danger',
                                            $record->stock_quantity <= $record->low_stock_threshold => 'warning',
                                            default => 'success',
                                        })
                                        ->formatStateUsing(fn (Product $record): string =>
                                            $record->manage_stock ? $record->stock_quantity . ' pz' : 'Non tracciato'
                                        ),

                                    TextEntry::make('low_stock_threshold')
                                        ->label('Soglia bassa')
                                        ->placeholder('—'),

                                    TextEntry::make('weight')
                                        ->label('Peso')
                                        ->suffix(' g')
                                        ->placeholder('—'),

                                    TextEntry::make('dimensions')
                                        ->label('Dimensioni')
                                        ->placeholder('—'),
                                ]),

                                Section::make('Statistiche')->icon('heroicon-o-chart-bar')->iconColor('info')->schema([
                                    TextEntry::make('average_rating')
                                        ->label('Rating')
                                        ->formatStateUsing(fn ($state): string =>
                                            $state > 0 ? number_format($state, 1) . ' / 5 ★' : '—'
                                        ),

                                    TextEntry::make('reviews_count')
                                        ->label('Recensioni')
                                        ->numeric(),

                                    TextEntry::make('sales_count')
                                        ->label('Vendute')
                                        ->numeric(),

                                    TextEntry::make('views_count')
                                        ->label('Visualizzazioni')
                                        ->numeric(),
                                ])->columns(2),
                            ])->columnSpan(1),
                        ])->columns(3),

                    Tab::make('Varianti')
                        ->icon('heroicon-o-squares-2x2')
                        ->schema([
                            Section::make('Varianti prodotto')->icon('heroicon-o-squares-2x2')->iconColor('primary')->schema([
                                RepeatableEntry::make('variants')
                                    ->label('Varianti')
                                    ->placeholder('—')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Variante')
                                            ->weight('medium'),

                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('barcode')
                                            ->label('Barcode')
                                            ->placeholder('—'),

                                        TextEntry::make('stock_quantity')
                                            ->label('Stock')
                                            ->badge()
                                            ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),

                                        IconEntry::make('is_active')
                                            ->label('Attiva')
                                            ->boolean()
                                            ->trueColor('success')
                                            ->falseColor('danger'),
                                    ])
                                    ->columns(5)
                                    ->columnSpanFull(),
                            ]),
                        ]),

                    Tab::make('Recensioni')
                        ->icon('heroicon-o-star')
                        ->schema([
                            Section::make('Recensioni clienti')->icon('heroicon-o-star')->iconColor('warning')->schema([
                                RepeatableEntry::make('reviews')
                                    ->label('Recensioni')
                                    ->placeholder('—')
                                    ->schema([
                                        TextEntry::make('user.full_name')
                                            ->label('Cliente'),

                                        TextEntry::make('rating')
                                            ->label('Voto')
                                            ->badge()
                                            ->color(fn ($state): string => match (true) {
                                                $state >= 4 => 'success',
                                                $state >= 3 => 'warning',
                                                default     => 'danger',
                                            })
                                            ->formatStateUsing(fn ($state): string => $state . ' ★'),

                                        TextEntry::make('title')
                                            ->label('Titolo')
                                            ->placeholder('—'),

                                        TextEntry::make('comment')
                                            ->label('Testo')
                                            ->placeholder('—')
                                            ->limit(120),

                                        IconEntry::make('is_approved')
                                            ->label('Approvata')
                                            ->boolean()
                                            ->trueColor('success')
                                            ->falseColor('warning'),

                                        TextEntry::make('created_at')
                                            ->label('Data')
                                            ->dateTime('d/m/Y'),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull(),
                            ]),
                        ]),

                    Tab::make('SEO')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Section::make('SEO & URL')->icon('heroicon-o-magnifying-glass')->iconColor('gray')->schema([
                                TextEntry::make('meta_title')
                                    ->label('Titolo SEO')
                                    ->placeholder('(auto-generato)'),

                                TextEntry::make('meta_description')
                                    ->label('Descrizione SEO')
                                    ->placeholder('(auto-generata)'),

                                TextEntry::make('meta_keywords')
                                    ->label('Parole chiave')
                                    ->placeholder('—'),

                                TextEntry::make('slug')
                                    ->label('URL prodotto')
                                    ->prefix('/prodotti/'),
                            ])->columns(2),
                        ]),

                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Immagine')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(url('/images/image-placeholder.jpg'))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => \Illuminate\Support\Str::limit($record->short_description ?? '', 40))
                    ->weight('medium')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categorie')
                    ->badge()
                    ->separator(',')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('price')
                    ->label('Prezzo')
                    ->money('EUR')
                    ->sortable()
                    ->description(fn (Product $record): ?string => 
                        $record->compare_at_price 
                            ? number_format($record->compare_at_price, 2, ',', '.') . ' €'
                            : null
                    )
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (Product $record): string => match (true) {
                        $record->manage_stock === false => 'gray',
                        $record->stock_quantity === 0 => 'danger',
                        $record->stock_quantity <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn (Product $record): string => 
                        $record->manage_stock 
                            ? $record->stock_quantity . ' pz' 
                            : '—'
                    )
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Evidenza')
                    ->boolean()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state): string => $state > 0 ? number_format($state, 1) . ' ★' : '—')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state): string => $state >= 4 ? 'success' : ($state >= 2.5 ? 'warning' : 'gray'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Recensioni')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Vendite')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('order')
            ->defaultSort('order', 'asc')
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

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marca')
                    ->placeholder('Tutte')
                    ->relationship('brand', 'name')
                    ->preload(),

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
                    ->label('Mostra Eliminati')
                    ->placeholder('No')
                    ->trueLabel('Si')
                    ->falseLabel('Solo Eliminati'),
            ])
            ->recordUrl(null)
            ->recordAction('view')
            ->recordActions([
                ViewAction::make()
                    ->extraAttributes(['style' => 'display:none'])
                    ->modalHeading(fn (Product $record) : string => "Prodotto: $record->name")
                    ->modalWidth('5xl'),
                ActionGroup::make([
                    EditAction::make()
                        ->label('Modifica')
                        ->color('info'),

                    Action::make('toggle_active')
                        ->label(fn (Product $record) => $record->is_active ? 'Disattiva' : 'Attiva')
                        ->icon(fn (Product $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Product $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Product $record) => $record->is_active ? 'Disattiva prodotto' : 'Attiva prodotto')
                        ->modalDescription(fn (Product $record) => $record->is_active
                            ? 'Il prodotto non sarà più visibile. Vuoi continuare?'
                            : 'Il prodotto tornerà visibile sul sito. Vuoi continuare?')
                        ->visible(fn (Product $record) => ! $record->trashed())
                        ->action(fn (Product $record) => $record->update(['is_active' => !$record->is_active]))
                        ->successNotificationTitle(fn (Product $record) => $record->is_active ? 'Prodotto attivato' : 'Prodotto disattivato'),

                    Action::make('toggle_featured')
                        ->label(fn (Product $record) => $record->is_featured ? 'Rimuovi da evidenza' : 'Metti in evidenza')
                        ->icon('heroicon-o-star')
                        ->color(fn (Product $record) => $record->is_featured ? 'warning' : 'success')
                        ->disabled(fn (Product $record) => ! $record->is_active)
                        ->tooltip(fn (Product $record) => ! $record->is_active ? 'Attiva il prodotto per poterlo mettere in evidenza' : null)
                        ->visible(fn (Product $record) => ! $record->trashed())
                        ->action(fn (Product $record) => $record->update(['is_featured' => !$record->is_featured]))
                        ->successNotificationTitle(fn (Product $record) => $record->is_featured ? 'Prodotto messo in evidenza' : 'Prodotto tolto da evidenza'),

                    DeleteAction::make()
                        ->label('Elimina')
                        ->modalHeading('Elimina prodotto')
                        ->modalDescription('Sei sicuro di voler eliminare il prodotto? Potrà essere ripristinato in seguito.')
                        ->successNotificationTitle('Prodotto eliminato con successo'),
                    RestoreAction::make()
                        ->label('Ripristina')
                        ->modalHeading('Ripristina prodotto')
                        ->modalDescription('Sei sicuro di voler ripristinare il prodotto?')
                        ->successNotificationTitle('Prodotto ripristinato con successo'),
                    ForceDeleteAction::make()
                        ->label('Elimina definitivamente')
                        ->modalHeading('Elimina definitivamente il prodotto')
                        ->modalDescription('Sei sicuro di voler eliminare definitivamente il prodotto? Questa azione non può essere annullata.')
                        ->successNotificationTitle('Prodotto eliminato definitivamente'),
                ])->label('Azioni'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina selezionati')
                        ->modalHeading('Elimina prodotti selezionati')
                        ->modalDescription('Sei sicuro di voler eliminare i prodotti selezionati? Potranno essere ripristinati in seguito.')
                        ->successNotificationTitle('Prodotti eliminati con successo'),
                    RestoreBulkAction::make()
                        ->label('Ripristina selezionati')
                        ->modalHeading('Ripristina prodotti selezionati')
                        ->modalDescription('Sei sicuro di voler ripristinare i prodotti selezionati?')
                        ->successNotificationTitle('Prodotti ripristinati con successo'),
                    ForceDeleteBulkAction::make()
                        ->label('Elimina definitivamente')
                        ->modalHeading('Elimina definitivamente i prodotti selezionati')
                        ->modalDescription('Sei sicuro di voler eliminare definitivamente i prodotti selezionati? Questa azione non può essere annullata.')
                        ->successNotificationTitle('Prodotti eliminati definitivamente'),
                ])->label('Azioni di massa'),
            ])
            ->emptyStateHeading('Nessun prodotto trovato')
            ->emptyStateDescription('Crea un nuovo prodotto')
            ->emptyStateIcon('heroicon-o-cube');
    }

    public static function getRelations(): array
    {
        return [];
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