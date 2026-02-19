<?php

namespace App\Filament\Resources\OrganizationalCharts\Pages;

use App\Filament\Resources\OrganizationalCharts\OrganizationalChartsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationalCharts extends EditRecord
{
    protected static string $resource = OrganizationalChartsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
