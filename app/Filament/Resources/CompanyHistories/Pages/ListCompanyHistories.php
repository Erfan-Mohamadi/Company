<?php

namespace App\Filament\Resources\CompanyHistories\Pages;

use App\Filament\Resources\CompanyHistories\CompanyHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyHistories extends ListRecords
{
    protected static string $resource = CompanyHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
