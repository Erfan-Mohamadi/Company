<?php

namespace App\Filament\Resources\AreaOfActivities\Pages;

use App\Filament\Resources\AreaOfActivities\AreaOfActivitiesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAreaOfActivities extends EditRecord
{
    protected static string $resource = AreaOfActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
