<?php


namespace App\Filament\Resources\Suppliers;

use App\Models\Supplier;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|UnitEnum|null $navigationGroup = 'Fornitori';
    protected static ?string $navigationLabel = 'Fornitori';
    protected static ?string $modelLabel = 'Fornitore';
    protected static ?string $pluralModelLabel = 'Fornitori';
    protected static ?int $navigationSort = 1;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Aziendali')
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Ragione Sociale')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\Hidden::make('slug'),
                    Forms\Components\TextInput::make('contact_name')->label('Referente')->maxLength(255),
                    Forms\Components\TextInput::make('vat_number')->label('Partita IVA')->maxLength(20),
                    Forms\Components\TextInput::make('tax_code')->label('Codice Fiscale')->maxLength(20),
                ])->columns(2),
            Section::make('Contatti')
                ->schema([
                    Forms\Components\TextInput::make('email')->label('Email')->email(),
                    Forms\Components\TextInput::make('phone')->label('Telefono'),
                    Forms\Components\TextInput::make('mobile')->label('Cellulare'),
                    Forms\Components\TextInput::make('website')->label('Sito Web')->url(),
                ])->columns(2),
            Section::make('Indirizzo')
                ->schema([
                    Forms\Components\TextInput::make('address')->label('Indirizzo'),
                    Forms\Components\TextInput::make('city')->label('Città'),
                    Forms\Components\TextInput::make('province')->label('Provincia')->maxLength(2),
                    Forms\Components\TextInput::make('postal_code')->label('CAP'),
                ])->columns(2),
            Section::make('Altre Informazioni')
                ->schema([
                    Forms\Components\TextInput::make('payment_terms')->label('Termini Pagamento'),
                    Forms\Components\Toggle::make('is_active')->label('Attivo')->default(true),
                    Forms\Components\Textarea::make('notes')->label('Note')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')->label('Ragione Sociale')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_name')->label('Referente')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefono'),
                Tables\Columns\TextColumn::make('city')->label('Città')->searchable(),
                Tables\Columns\TextColumn::make('orders_count')->label('Ordini')->counts('orders')->badge()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Attivo')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Stato'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
            'view' => Pages\ViewSupplier::route('/{record}'),
        ];
    }
}
