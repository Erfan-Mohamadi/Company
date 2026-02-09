<?php

namespace App\Filament\Resources\MissionVisions\Pages;

use App\Filament\Resources\MissionVisions\MissionVisionResource;
use App\Models\MissionVision;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMissionVisions extends ListRecords
{
    protected static string $resource = MissionVisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(MissionVision::query()->count() === 0),
        ];
    }

    protected function getRedirectUrl(): string
    {
        if (MissionVision::query()->count() === 1) {
            return MissionVisionResource::getUrl('edit', ['record' => MissionVision::query()->first()]);
        }

        return parent::getRedirectUrl();
    }
}
