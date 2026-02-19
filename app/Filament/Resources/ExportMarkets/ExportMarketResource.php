<?php

namespace App\Filament\Resources\ExportMarkets;

use App\Filament\Resources\ExportMarkets\Pages\CreateExportMarket;
use App\Filament\Resources\ExportMarkets\Pages\EditExportMarket;
use App\Filament\Resources\ExportMarkets\Pages\ListExportMarkets;
use App\Filament\Resources\ExportMarkets\Schemas\ExportMarketForm;
use App\Filament\Resources\ExportMarkets\Tables\ExportMarketsTable;
use App\Models\ExportMarket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExportMarketResource extends Resource
{
    protected static ?string $model = ExportMarket::class;
    protected static ?string $recordTitleAttribute = 'ExportMarket';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::GlobeAlt;
    protected static string|null|\UnitEnum $navigationGroup = 'Business Network';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string   { return __('Business Network'); }
    public static function getNavigationLabel(): string    { return __('Export Markets'); }
    public static function getModelLabel(): string         { return __('Export Market'); }
    public static function getPluralModelLabel(): string   { return __('Export Markets'); }

    public static function form(Schema $schema): Schema
    {
        return ExportMarketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExportMarketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExportMarkets::route('/'),
            'create' => CreateExportMarket::route('/create'),
            'edit' => EditExportMarket::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string { return static::getModel()::where('status', 'published')->count() ?: null; }
    public static function getNavigationBadgeColor(): ?string { return 'success'; }
}
