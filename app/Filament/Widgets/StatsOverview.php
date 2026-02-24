<?php

namespace App\Filament\Widgets;

use App\Models\Accreditation;
use App\Models\Customer;
use App\Models\About;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('Total Customers'), Customer::query()->count())
                ->description(__('Active business network'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make(__('Accreditations'), Accreditation::query()->count())
                ->description(Accreditation::query()->where('status', 'published')->count() . ' ' . __('published'))
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make(__('Drafts Pending'), Accreditation::query()->where('status', 'draft')->count())
                ->descriptionIcon('heroicon-m-document')
                ->color('warning'),

            Stat::make(__('Company Profiles'), About::query()->count())
                ->description(__('Total about entries'))
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
        ];
    }
}
