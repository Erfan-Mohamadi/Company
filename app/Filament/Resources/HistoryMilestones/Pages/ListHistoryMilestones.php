<?php

namespace App\Filament\Resources\HistoryMilestones\Pages;

use App\Filament\Resources\HistoryMilestones\HistoryMilestonesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHistoryMilestones extends ListRecords
{
    protected static string $resource = HistoryMilestonesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
