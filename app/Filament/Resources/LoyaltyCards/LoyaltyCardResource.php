<?php


namespace App\Filament\Resources\LoyaltyCards;

use App\Models\LoyaltyCard;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class LoyaltyCardResource extends Resource
{
    protected static ?string $model = LoyaltyCard::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static string|UnitEnum|null $navigationGroup = 'Clienti e Fedeltà';
    protected static ?string $navigationLabel = 'Carte Fedeltà';
    protected static ?string $modelLabel = 'Carta Fedeltà';
    protected static ?string $pluralModelLabel = 'Carte Fedeltà';
    protected static ?int $navigationSort = 2;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Carta Fedeltà')
                ->schema([
                    Forms\Components\TextInput::make('card_number')
                        ->label('Numero Carta')
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Generato automaticamente'),
                    Forms\Components\Select::make('user_id')
                        ->label('Cliente')
                        ->relationship('user', 'email', fn ($query) => $query->where('role', 'customer'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('points')
                        ->label('Punti')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('total_spent')
                        ->label('Totale Speso')
                        ->numeric()
                        ->prefix('€')
                        ->default(0),
                    Forms\Components\Select::make('tier')
                        ->label('Livello')
                        ->options([
                            'base' => 'Base',
                            'silver' => 'Silver',
                            'gold' => 'Gold',
                            'platinum' => 'Platinum',
                        ])
                        ->default('base')
                        ->required(),
                    Forms\Components\DatePicker::make('issued_at')
                        ->label('Data Emissione')
                        ->default(now()),
                    Forms\Components\DatePicker::make('expires_at')
                        ->label('Scadenza'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Attiva')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('card_number')
                    ->label('N. Carta')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('Cliente')
                    ->formatStateUsing(fn ($record) => $record->user?->full_name ?? '-')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Punti')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Totale Speso')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tier')
                    ->label('Livello')
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'base' => 'gray',
                        'silver' => 'info',
                        'gold' => 'warning',
                        'platinum' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tier')
                    ->label('Livello')
                    ->options([
                        'base' => 'Base',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
                        'platinum' => 'Platinum',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Stato')
                    ->trueLabel('Attive')
                    ->falseLabel('Disattive'),
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
            'index' => Pages\ListLoyaltyCards::route('/'),
            'create' => Pages\CreateLoyaltyCard::route('/create'),
            'edit' => Pages\EditLoyaltyCard::route('/{record}/edit'),
            'view' => Pages\ViewLoyaltyCard::route('/{record}'),
        ];
    }
}
