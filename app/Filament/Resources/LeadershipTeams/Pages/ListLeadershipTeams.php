<?php

namespace App\Filament\Resources\LeadershipTeams\Pages;

use App\Filament\Resources\LeadershipTeams\LeadershipTeamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeadershipTeams extends ListRecords
{
    protected static string $resource = LeadershipTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
