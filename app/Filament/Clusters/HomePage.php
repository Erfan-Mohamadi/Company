<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class HomePage extends Cluster
{
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::HomeModern;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('Homepage & UI Components');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('Homepage & UI Components');
    }
}
