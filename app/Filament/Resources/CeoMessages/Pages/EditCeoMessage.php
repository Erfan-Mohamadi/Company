<?php

namespace App\Filament\Resources\CeoMessages\Pages;

use App\Filament\Resources\CeoMessages\CeoMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCeoMessage extends EditRecord
{
    protected static string $resource = CeoMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
