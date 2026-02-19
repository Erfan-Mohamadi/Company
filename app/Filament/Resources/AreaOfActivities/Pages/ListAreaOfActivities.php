<?php

namespace App\Filament\Resources\AreaOfActivities\Pages;

use App\Filament\Resources\AreaOfActivities\AreaOfActivitiesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAreaOfActivities extends ListRecords
{
    protected static string $resource = AreaOfActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
