<?php

namespace App\Filament\Resources\RepresentationLetters\Pages;

use App\Filament\Resources\RepresentationLetters\RepresentationLetterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRepresentationLetter extends EditRecord
{
    protected static string $resource = RepresentationLetterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
