<?php

namespace App\Filament\Resources\CompanyValues\Pages;

use App\Filament\Resources\CompanyValues\CompanyValueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyValue extends EditRecord
{
    protected static string $resource = CompanyValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
