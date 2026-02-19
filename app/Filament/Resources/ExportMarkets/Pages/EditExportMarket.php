<?php

namespace App\Filament\Resources\ExportMarkets\Pages;

use App\Filament\Resources\ExportMarkets\ExportMarketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExportMarket extends EditRecord
{
    protected static string $resource = ExportMarketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
