<?php

namespace App\Filament\Resources\Awards;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\Awards\Pages\CreateAward;
use App\Filament\Resources\Awards\Pages\EditAward;
use App\Filament\Resources\Awards\Pages\ListAwards;
use App\Filament\Resources\Awards\Schemas\AwardForm;
use App\Filament\Resources\Awards\Tables\AwardsTable;
use App\Models\Award;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AwardResource extends Resource
{
    protected static ?string $model = Award::class;
    protected static ?string $cluster = AboutCompany::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Trophy;
    protected static string|null|\UnitEnum $navigationGroup = 'Achievements';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string { return __('Achievements'); }
    public static function getNavigationLabel(): string  { return __('Awards & Honors'); }
    public static function getModelLabel(): string       { return __('Award'); }
    public static function getPluralModelLabel(): string { return __('Awards & Honors'); }

    public static function form(Schema $schema): Schema { return AwardForm::configure($schema); }
    public static function table(Table $table): Table   { return AwardsTable::configure($table); }
    public static function getRelations(): array        { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListAwards::route('/'),
            'create' => CreateAward::route('/create'),
            'edit'   => EditAward::route('/{record}/edit'),
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
