<?php

namespace App\Filament\Resources\MissionVisions\Pages;

use App\Filament\Resources\MissionVisions\MissionVisionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMissionVision extends EditRecord
{
    protected static string $resource = MissionVisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
