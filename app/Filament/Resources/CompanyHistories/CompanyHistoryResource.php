<?php

namespace App\Filament\Resources\CompanyHistories;

use App\Filament\Resources\CompanyHistories\Pages\CreateCompanyHistory;
use App\Filament\Resources\CompanyHistories\Pages\EditCompanyHistory;
use App\Filament\Resources\CompanyHistories\Pages\ListCompanyHistories;
use App\Filament\Resources\CompanyHistories\Schemas\CompanyHistoryForm;
use App\Filament\Resources\CompanyHistories\Tables\CompanyHistoriesTable;
use App\Models\CompanyHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyHistoryResource extends Resource
{
    protected static ?string $model = CompanyHistory::class;
    protected static ?string $recordTitleAttribute = 'CompanyHistory';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Clock;
    protected static string|null|\UnitEnum $navigationGroup = 'Growth Story';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string   { return __('Growth Story'); }
    public static function getNavigationLabel(): string    { return __('Company History'); }
    public static function getModelLabel(): string         { return __('History Entry'); }
    public static function getPluralModelLabel(): string   { return __('Company History'); }


    public static function form(Schema $schema): Schema
    {
        return CompanyHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyHistoriesTable::configure($table);
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
            'index' => ListCompanyHistories::route('/'),
            'create' => CreateCompanyHistory::route('/create'),
            'edit' => EditCompanyHistory::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string { return static::getModel()::where('status', 'published')->count() ?: null; }
    public static function getNavigationBadgeColor(): ?string { return 'success'; }
}
