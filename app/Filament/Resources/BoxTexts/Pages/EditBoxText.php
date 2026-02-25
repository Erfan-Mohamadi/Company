<?php

namespace App\Filament\Resources\BoxTexts\Pages;

use App\Filament\Resources\BoxTexts\BoxTextResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBoxText extends EditRecord
{
    protected static string $resource = BoxTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
