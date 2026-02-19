<?php

namespace App\Filament\Resources\Discounts\Pages;

use App\Filament\Resources\Discounts\DiscountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDiscounts extends ListRecords
{
    protected static string $resource = DiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuovo Sconto'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tutti')
                ->badge(fn () => $this->getModel()::count()),
            
            'active' => Tab::make('Attivi')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                          ->where(function ($q) {
                              $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                          })
                )
                ->badge(fn () => $this->getModel()::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                    })->count())
                ->badgeColor('success'),
            
            'expired' => Tab::make('Scaduti')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('expires_at', '<', now()))
                ->badge(fn () => $this->getModel()::where('expires_at', '<', now())->count())
                ->badgeColor('danger'),
            
            'percentage' => Tab::make('Percentuale')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'percentage'))
                ->badge(fn () => $this->getModel()::where('type', 'percentage')->count()),
            
            'fixed' => Tab::make('Fissi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'fixed'))
                ->badge(fn () => $this->getModel()::where('type', 'fixed')->count()),
        ];
    }
}
