<?php

namespace App\Filament\Resources\OrganizationalCharts;

use App\Filament\Resources\OrganizationalCharts\Pages\CreateOrganizationalCharts;
use App\Filament\Resources\OrganizationalCharts\Pages\EditOrganizationalCharts;
use App\Filament\Resources\OrganizationalCharts\Pages\ListOrganizationalCharts;
use App\Filament\Resources\OrganizationalCharts\Schemas\OrganizationalChartsForm;
use App\Filament\Resources\OrganizationalCharts\Tables\OrganizationalChartsTable;
use App\Models\OrganizationalChart;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationalChartsResource extends Resource
{
    protected static ?string $model = OrganizationalChart::class;
    protected static ?string $recordTitleAttribute = 'OrganizationalChart';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::ChartBarSquare;

    protected static string|null|\UnitEnum $navigationGroup = 'Our Team';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('Our Team');
    }

    public static function getNavigationLabel(): string
    {
        return __('Organization Chart');
    }

    public static function getModelLabel(): string
    {
        return __('Organization Chart');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Organization Charts');
    }

    public static function form(Schema $schema): Schema
    {
        return OrganizationalChartsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationalChartsTable::configure($table);
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
            'index' => ListOrganizationalCharts::route('/'),
            'create' => CreateOrganizationalCharts::route('/create'),
            'edit' => EditOrganizationalCharts::route('/{record}/edit'),
        ];
    }
}
