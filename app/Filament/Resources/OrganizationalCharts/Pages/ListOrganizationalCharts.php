<?php

namespace App\Filament\Resources\OrganizationalCharts\Pages;

use App\Filament\Resources\OrganizationalCharts\OrganizationalChartsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationalCharts extends ListRecords
{
    protected static string $resource = OrganizationalChartsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
