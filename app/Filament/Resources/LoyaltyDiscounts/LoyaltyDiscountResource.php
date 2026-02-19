<?php

namespace App\Filament\Resources\LoyaltyDiscounts;

use App\Models\LoyaltyDiscount;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class LoyaltyDiscountResource extends Resource
{
    protected static ?string $model = LoyaltyDiscount::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';
    protected static string|UnitEnum|null $navigationGroup = 'Clienti e Fedeltà';
    protected static ?string $navigationLabel = 'Sconti Fedeltà';
    protected static ?string $modelLabel = 'Sconto Fedeltà';
    protected static ?string $pluralModelLabel = 'Sconti Fedeltà';
    protected static ?int $navigationSort = 3;

    public static function schema(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Sconto Fedeltà')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Nome')->required()->maxLength(255),
                    Forms\Components\Textarea::make('description')->label('Descrizione')->rows(3),
                    Forms\Components\Select::make('tier')->label('Livello Richiesto')
                        ->options(['base' => 'Base', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'])
                        ->required(),
                    Forms\Components\Select::make('discount_type')->label('Tipo Sconto')
                        ->options(['percentage' => 'Percentuale (%)', 'fixed' => 'Fisso (€)'])
                        ->required(),
                    Forms\Components\TextInput::make('discount_value')->label('Valore Sconto')
                        ->numeric()->required()->minValue(0),
                    Forms\Components\TextInput::make('points_required')->label('Punti Richiesti')
                        ->numeric()->default(0)->minValue(0),
                    Forms\Components\TextInput::make('min_order_amount')->label('Ordine Minimo')
                        ->numeric()->prefix('€'),
                    Forms\Components\Toggle::make('is_active')->label('Attivo')->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tier')->label('Livello')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match($state) {
                        'base' => 'gray',
                        'silver' => 'info',
                        'gold' => 'warning',
                        'platinum' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('discount_value')->label('Valore')
                    ->formatStateUsing(fn ($record) => $record->discount_type === 'percentage'
                        ? "{$record->discount_value}%"
                        : number_format($record->discount_value, 2) . ' €'),
                Tables\Columns\TextColumn::make('points_required')->label('Punti Richiesti')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Attivo')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tier')->label('Livello')
                    ->options(['base' => 'Base', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('tier');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoyaltyDiscounts::route('/'),
            'create' => Pages\CreateLoyaltyDiscount::route('/create'),
            'edit' => Pages\EditLoyaltyDiscount::route('/{record}/edit'),
        ];
    }
}
