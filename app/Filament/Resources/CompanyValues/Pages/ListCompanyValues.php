<?php

namespace App\Filament\Resources\CompanyValues\Pages;

use App\Filament\Resources\CompanyValues\CompanyValueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyValues extends ListRecords
{
    protected static string $resource = CompanyValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
