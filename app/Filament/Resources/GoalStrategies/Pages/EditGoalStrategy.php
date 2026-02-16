<?php

namespace App\Filament\Resources\GoalStrategies\Pages;

use App\Filament\Resources\GoalStrategies\GoalStrategyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGoalStrategy extends EditRecord
{
    protected static string $resource = GoalStrategyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
