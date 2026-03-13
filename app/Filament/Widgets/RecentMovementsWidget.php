<?php

namespace App\Filament\Widgets;

use App\Models\WarehouseMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentMovementsWidget extends TableWidget
{
    protected static ?int $sort = 8;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Movimenti Magazzino Recenti')
            ->query(fn (): Builder => WarehouseMovement::query()
                ->with(['product', 'user'])
                ->latest()
                ->limit(10)
            )
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'carico'  => 'Carico',
                        'scarico' => 'Scarico',
                        'reso'    => 'Reso',
                        default   => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'carico'  => 'success',
                        'scarico' => 'danger',
                        'reso'    => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->limit(28)
                    ->description(fn ($record) => $record->getReasonLabel()),

                TextColumn::make('quantity')
                    ->label('Qtà')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '') . $state),

                TextColumn::make('user.first_name')
                    ->label('Operatore')
                    ->formatStateUsing(fn ($state, $record) => $record->user
                        ? trim($record->user->first_name . ' ' . $record->user->last_name)
                        : 'Sistema'
                    )
                    ->default('Sistema')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->since()
                    ->sortable(),
            ])
            ->emptyStateHeading('Nessun movimento registrato')
            ->emptyStateIcon('heroicon-o-arrows-right-left')
            ->paginated(false);
    }
}
