<?php

namespace App\Filament\Resources\ExportMarkets\Pages;

use App\Filament\Resources\ExportMarkets\ExportMarketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExportMarkets extends ListRecords
{
    protected static string $resource = ExportMarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
