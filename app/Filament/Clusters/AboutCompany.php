<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class AboutCompany extends Cluster
{
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationLabel(): string
    {
        return __('About Company Section');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('About Company Section');
    }
}
