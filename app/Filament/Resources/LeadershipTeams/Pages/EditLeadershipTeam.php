<?php

namespace App\Filament\Resources\LeadershipTeams\Pages;

use App\Filament\Resources\LeadershipTeams\LeadershipTeamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeadershipTeam extends EditRecord
{
    protected static string $resource = LeadershipTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
