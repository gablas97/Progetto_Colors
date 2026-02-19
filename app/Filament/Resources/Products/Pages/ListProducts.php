<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuovo Prodotto'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tutti')
                ->badge(fn () => $this->getModel()::count()),
            
            'active' => Tab::make('Attivi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => $this->getModel()::where('is_active', true)->count()),
            
            'inactive' => Tab::make('Disattivati')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(fn () => $this->getModel()::where('is_active', false)->count())
                ->badgeColor('gray'),
            
            'featured' => Tab::make('In Homepage')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true))
                ->badge(fn () => $this->getModel()::where('is_featured', true)->count())
                ->badgeColor('success'),
            
            'low_stock' => Tab::make('Stock Basso')
                ->modifyQueryUsing(fn (Builder $query) => $query->lowStock())
                ->badge(fn () => $this->getModel()::lowStock()->count())
                ->badgeColor('warning'),
            
            'out_of_stock' => Tab::make('Esauriti')
                ->modifyQueryUsing(fn (Builder $query) => $query->outOfStock())
                ->badge(fn () => $this->getModel()::outOfStock()->count())
                ->badgeColor('danger'),
        ];
    }
}
