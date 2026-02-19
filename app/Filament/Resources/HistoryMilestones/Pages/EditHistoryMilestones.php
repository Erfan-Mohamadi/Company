<?php

namespace App\Filament\Resources\HistoryMilestones\Pages;

use App\Filament\Resources\HistoryMilestones\HistoryMilestonesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHistoryMilestones extends EditRecord
{
    protected static string $resource = HistoryMilestonesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
