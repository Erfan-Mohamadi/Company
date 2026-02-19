<?php

namespace App\Filament\Resources\CeoMessages\Pages;

use App\Filament\Resources\CeoMessages\CeoMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCeoMessages extends ListRecords
{
    protected static string $resource = CeoMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
