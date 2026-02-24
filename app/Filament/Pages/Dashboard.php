<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AccreditationStatusChart;
use App\Filament\Widgets\RecentCustomers;
use App\Filament\Widgets\LatestAccreditations;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    public static function getNavigationLabel(): string  { return __('Admin Overview'); }
    public static function getModelLabel(): string       { return __('Admin Overview'); }
    public static function getPluralModelLabel(): string { return __('Admin Overview'); }

    public function getTitle(): string
    {
        return __('Admin Overview');
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            AccreditationStatusChart::class,
            RecentCustomers::class,
            LatestAccreditations::class,
        ];
    }
}
