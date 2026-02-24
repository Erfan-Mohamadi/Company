<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\Resources\Suppliers\Tables\SuppliersTable;
use App\Models\Supplier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $cluster = AboutCompany::class;
    protected static ?string $recordTitleAttribute = 'Supplier';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Truck;
    protected static string|null|\UnitEnum $navigationGroup = 'Business Network';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string   { return __('Business Network'); }
    public static function getNavigationLabel(): string    { return __('Suppliers'); }
    public static function getModelLabel(): string         { return __('Supplier'); }
    public static function getPluralModelLabel(): string   { return __('Suppliers'); }

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
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
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
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
