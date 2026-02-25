<?php

namespace App\Filament\Resources\CallToActions\Pages;

use App\Filament\Resources\CallToActions\CallToActionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCallToAction extends EditRecord
{
    protected static string $resource = CallToActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
