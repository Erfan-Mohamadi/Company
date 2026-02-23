<?php

namespace App\Filament\Resources\CompanyValues;

use App\Filament\Resources\CompanyValues\Pages\CreateCompanyValue;
use App\Filament\Resources\CompanyValues\Pages\EditCompanyValue;
use App\Filament\Resources\CompanyValues\Pages\ListCompanyValues;
use App\Filament\Resources\CompanyValues\Schemas\CompanyValueForm;
use App\Filament\Resources\CompanyValues\Tables\CompanyValuesTable;
use App\Models\CompanyValue;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyValueResource extends Resource
{
    protected static ?string $model = CompanyValue::class;
    protected static ?string $recordTitleAttribute = 'CompanyValue';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Star;

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('Company Values');
    }

    public static function getModelLabel(): string
    {
        return __('Company Value');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Company Values');
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyValueForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyValuesTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanyValues::route('/'),
            'create' => CreateCompanyValue::route('/create'),
            'edit'   => EditCompanyValue::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = static::getModel()::where('status', 'draft')->count();
        if ($drafts) {
            return $drafts;
        }
        return static::getModel()::where('status', 'published')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'draft')->exists()
            ? 'warning'
            : 'success';
    }
}
