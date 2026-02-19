<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tutte')
                ->badge(fn () => $this->getModel()::count()),
            
            'pending' => Tab::make('In Attesa')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_approved', false))
                ->badge(fn () => $this->getModel()::where('is_approved', false)->count())
                ->badgeColor('warning'),
            
            'approved' => Tab::make('Approvate')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_approved', true))
                ->badge(fn () => $this->getModel()::where('is_approved', true)->count())
                ->badgeColor('success'),
            
            'verified' => Tab::make('Acquisti Verificati')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_verified_purchase', true))
                ->badge(fn () => $this->getModel()::where('is_verified_purchase', true)->count())
                ->badgeColor('info'),
            
            '5_stars' => Tab::make('5 ⭐')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 5))
                ->badge(fn () => $this->getModel()::where('rating', 5)->count()),
            
            '4_stars' => Tab::make('4 ⭐')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 4))
                ->badge(fn () => $this->getModel()::where('rating', 4)->count()),
            
            '3_stars' => Tab::make('3 ⭐')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rating', 3))
                ->badge(fn () => $this->getModel()::where('rating', 3)->count()),
            
            '1_2_stars' => Tab::make('1-2 ⭐')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('rating', [1, 2]))
                ->badge(fn () => $this->getModel()::whereIn('rating', [1, 2])->count())
                ->badgeColor('danger'),
        ];
    }
}
