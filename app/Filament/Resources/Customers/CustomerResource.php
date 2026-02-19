<?php


namespace App\Filament\Resources\Customers;

use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'Clienti e Fedeltà';
    protected static ?string $navigationLabel = 'Clienti';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clienti';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'customer');
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dati Personali')
                ->schema([
                    Forms\Components\TextInput::make('first_name')->label('Nome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('last_name')->label('Cognome')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')->label('Telefono')->maxLength(20),
                ])->columns(2),
            Section::make('Impostazioni')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->label('Attivo')->default(true),
                    Forms\Components\Toggle::make('newsletter_subscribed')->label('Iscritto Newsletter')->default(false),
                    Forms\Components\TextInput::make('welcome_voucher')->label('Voucher Benvenuto')->numeric()->prefix('€'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nome Completo')
                    ->searchable(['first_name', 'last_name'])->sortable(['first_name']),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->label('Telefono')->toggleable(),
                Tables\Columns\TextColumn::make('orders_count')->label('Ordini')
                    ->counts('orders')->sortable()->badge(),
                Tables\Columns\TextColumn::make('loyaltyCard.tier')->label('Livello')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '-')
                    ->color(fn (?string $state) => match($state) {
                        'base' => 'gray',
                        'silver' => 'info',
                        'gold' => 'warning',
                        'platinum' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')->label('Attivo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Registrato')
                    ->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Stato')
                    ->trueLabel('Attivi')->falseLabel('Disattivi'),
                Tables\Filters\TernaryFilter::make('newsletter_subscribed')->label('Newsletter'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) User::where('role', 'customer')->count();
    }
}
