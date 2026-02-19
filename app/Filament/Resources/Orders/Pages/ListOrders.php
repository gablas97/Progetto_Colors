<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\Concerns\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tutti')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),
            
            'pending' => Tab::make('In Attesa')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('gray'),
            
            'processing' => Tab::make('In Elaborazione')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing'))
                ->badge(fn () => $this->getModel()::where('status', 'processing')->count())
                ->badgeColor('info'),
            
            'shipped' => Tab::make('Spediti')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'shipped'))
                ->badge(fn () => $this->getModel()::where('status', 'shipped')->count())
                ->badgeColor('warning'),
            
            'delivered' => Tab::make('Consegnati')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered'))
                ->badge(fn () => $this->getModel()::where('status', 'delivered')->count())
                ->badgeColor('success'),
            
            'cancelled' => Tab::make('Annullati')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn () => $this->getModel()::where('status', 'cancelled')->count())
                ->badgeColor('danger'),
        ];
    }
}
