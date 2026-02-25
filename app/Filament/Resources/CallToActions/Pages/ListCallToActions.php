<?php

namespace App\Filament\Resources\CallToActions\Pages;

use App\Filament\Resources\CallToActions\CallToActionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCallToActions extends ListRecords
{
    protected static string $resource = CallToActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
