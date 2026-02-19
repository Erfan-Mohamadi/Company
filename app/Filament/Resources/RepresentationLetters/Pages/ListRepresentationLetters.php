<?php

namespace App\Filament\Resources\RepresentationLetters\Pages;

use App\Filament\Resources\RepresentationLetters\RepresentationLetterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRepresentationLetters extends ListRecords
{
    protected static string $resource = RepresentationLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
