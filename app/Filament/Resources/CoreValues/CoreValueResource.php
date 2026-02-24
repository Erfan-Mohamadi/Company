<?php

namespace App\Filament\Resources\CoreValues;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\CoreValues\Pages\CreateCoreValue;
use App\Filament\Resources\CoreValues\Pages\EditCoreValue;
use App\Filament\Resources\CoreValues\Pages\ListCoreValues;
use App\Filament\Resources\CoreValues\Schemas\CoreValueForm;
use App\Filament\Resources\CoreValues\Tables\CoreValuesTable;
use App\Models\CoreValue;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CoreValueResource extends Resource
{
    protected static ?string $model = CoreValue::class;
    protected static ?string $recordTitleAttribute = 'value_name';
    protected static ?string $cluster = AboutCompany::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Heart;
    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string { return __('Company Profile'); }
    public static function getNavigationLabel(): string  { return __('Core Values'); }
    public static function getModelLabel(): string       { return __('Core Value'); }
    public static function getPluralModelLabel(): string { return __('Core Values'); }

    public static function form(Schema $schema): Schema  { return CoreValueForm::configure($schema); }
    public static function table(Table $table): Table    { return CoreValuesTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListCoreValues::route('/'),
            'create' => CreateCoreValue::route('/create'),
            'edit'   => EditCoreValue::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = static::getModel()::where('status', 'draft')->count();
        return $drafts ?: (static::getModel()::where('status', 'published')->count() ?: null);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'draft')->exists() ? 'warning' : 'success';
    }
}
