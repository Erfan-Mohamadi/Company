<?php

namespace App\Filament\Resources\TranslationKeys\Pages;

use App\Filament\Resources\TranslationKeys\TranslationKeyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTranslationKey extends ViewRecord
{
    protected static string $resource = TranslationKeyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
