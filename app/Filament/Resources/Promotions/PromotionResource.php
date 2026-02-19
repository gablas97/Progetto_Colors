<?php


namespace App\Filament\Resources\Promotions;

use App\Models\Promotion;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    protected static string|UnitEnum|null $navigationGroup = 'Clienti e Fedeltà';
    protected static ?string $navigationLabel = 'Promozioni';
    protected static ?string $modelLabel = 'Promozione';
    protected static ?string $pluralModelLabel = 'Promozioni';
    protected static ?int $navigationSort = 4;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dettagli Promozione')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nome')->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\Hidden::make('slug'),
                    Forms\Components\Select::make('type')->label('Tipo')
                        ->options([
                            'percentage' => 'Sconto %',
                            'fixed' => 'Sconto Fisso €',
                            'buy_x_get_y' => 'Compra X Paga Y',
                            'bundle' => 'Bundle',
                            'shipping' => 'Spedizione Gratuita',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('value')->label('Valore')->numeric()->required(),
                    Forms\Components\TextInput::make('buy_quantity')->label('Quantità da Comprare')->numeric()
                        ->visible(fn (Get $get) => $get('type') === 'buy_x_get_y'),
                    Forms\Components\TextInput::make('get_quantity')->label('Quantità in Omaggio')->numeric()
                        ->visible(fn (Get $get) => $get('type') === 'buy_x_get_y'),
                    Forms\Components\Textarea::make('description')->label('Descrizione')->rows(3)->columnSpanFull(),
                ])->columns(2),
            Section::make('Validità e Limiti')
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')->label('Inizio')->required(),
                    Forms\Components\DateTimePicker::make('ends_at')->label('Fine')->required()->after('starts_at'),
                    Forms\Components\TextInput::make('min_order_amount')->label('Ordine Minimo')->numeric()->prefix('€'),
                    Forms\Components\TextInput::make('usage_limit')->label('Limite Utilizzi')->numeric(),
                    Forms\Components\Toggle::make('is_active')->label('Attiva')->default(true),
                ])->columns(2),
            Section::make('Prodotti e Categorie Coinvolti')
                ->schema([
                    Forms\Components\Select::make('products')->label('Prodotti')
                        ->relationship('products', 'name')->multiple()->searchable()->preload(),
                    Forms\Components\Select::make('categories')->label('Categorie')
                        ->relationship('categories', 'name')->multiple()->searchable()->preload(),
                ])->columns(2),
            Section::make('Media')
                ->schema([
                    Forms\Components\FileUpload::make('banner_image')->label('Banner Promozione')
                        ->image()->directory('promotions')->imageEditor(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'percentage' => 'Sconto %',
                        'fixed' => 'Sconto €',
                        'buy_x_get_y' => 'Compra X Paga Y',
                        'bundle' => 'Bundle',
                        'shipping' => 'Spedizione Gratis',
                        default => $state,
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('value')->label('Valore')->sortable(),
                Tables\Columns\TextColumn::make('starts_at')->label('Inizio')->dateTime('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->label('Fine')->dateTime('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('usage_count')->label('Utilizzi')->badge()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Attiva')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Stato'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
            'view' => Pages\ViewPromotion::route('/{record}'),
        ];
    }
}
