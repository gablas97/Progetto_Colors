<?php

namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;

class Dashboard extends \Filament\Pages\Dashboard
{
    // Non-static: corrisponde alla dichiarazione del parent Filament\Pages\Page::$view
    protected string $view = 'filament.pages.dashboard-main';

    // BackedEnum|string|null: corrisponde alla dichiarazione del parent
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|UnitEnum|null $navigationGroup = 'Generale';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = 1;

    public string $tab = 'panoramica';

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
    }
}
