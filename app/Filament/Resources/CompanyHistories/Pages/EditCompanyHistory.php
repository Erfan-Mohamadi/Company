<?php

namespace App\Filament\Resources\CompanyHistories\Pages;

use App\Filament\Resources\CompanyHistories\CompanyHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyHistory extends EditRecord
{
    protected static string $resource = CompanyHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
