<?php

namespace App\Filament\Resources\Statistics;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\Statistics\Pages\CreateStatistic;
use App\Filament\Resources\Statistics\Pages\EditStatistic;
use App\Filament\Resources\Statistics\Pages\ListStatistics;
use App\Filament\Resources\Statistics\Schemas\StatisticForm;
use App\Filament\Resources\Statistics\Tables\StatisticsTable;
use App\Models\Statistic;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StatisticResource extends Resource
{
    protected static ?string $model = Statistic::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::ChartBar;
    protected static string|null|\UnitEnum $navigationGroup = 'Social Proof';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string  { return __('Social Proof'); }
    public static function getNavigationLabel(): string   { return __('Statistics'); }
    public static function getModelLabel(): string        { return __('Statistic'); }
    public static function getPluralModelLabel(): string  { return __('Statistics'); }

    public static function form(Schema $schema): Schema  { return StatisticForm::configure($schema); }
    public static function table(Table $table): Table    { return StatisticsTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListStatistics::route('/'),
            'create' => CreateStatistic::route('/create'),
            'edit'   => EditStatistic::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = static::getModel()::where('status', 'draft')->count();
        return $drafts ?: (static::getModel()::where('status', 'active')->count() ?: null);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'draft')->exists() ? 'warning' : 'success';
    }
}
