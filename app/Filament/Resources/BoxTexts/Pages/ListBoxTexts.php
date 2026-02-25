<?php

namespace App\Filament\Resources\BoxTexts\Pages;

use App\Filament\Resources\BoxTexts\BoxTextResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBoxTexts extends ListRecords
{
    protected static string $resource = BoxTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
