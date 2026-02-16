<?php

namespace App\Filament\Resources\GoalStrategies\Pages;

use App\Filament\Resources\GoalStrategies\GoalStrategyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGoalStrategies extends ListRecords
{
    protected static string $resource = GoalStrategyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
