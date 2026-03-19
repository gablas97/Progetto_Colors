<?php

namespace App\Filament\Resources\StockAdjustments\Pages;

use App\Filament\Resources\StockAdjustments\StockAdjustmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStockAdjustments extends ListRecords
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuovo Movimento'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tutti'),
            
            'manual' => Tab::make('Manuali')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereIn('type', ['manual_load', 'manual_unload'])
                )
                ->badge(fn () => 
                    $this->getModel()::whereIn('type', ['manual_load', 'manual_unload'])->count()
                ),
            
            'automatic' => Tab::make('Automatici')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereIn('type', ['order_fulfilled', 'supplier_order_received', 'return'])
                )
                ->badge(fn () => 
                    $this->getModel()::whereIn('type', ['order_fulfilled', 'supplier_order_received', 'return'])->count()
                ),
        ];
    }
}
