<?php

namespace App\Filament\Resources\GoalStrategies;

use App\Filament\Resources\GoalStrategies\Pages\CreateGoalStrategy;
use App\Filament\Resources\GoalStrategies\Pages\EditGoalStrategy;
use App\Filament\Resources\GoalStrategies\Pages\ListGoalStrategies;
use App\Filament\Resources\GoalStrategies\Schemas\GoalStrategyForm;
use App\Filament\Resources\GoalStrategies\Tables\GoalStrategiesTable;
use App\Models\GoalStrategy;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GoalStrategyResource extends Resource
{
    protected static ?string $model = GoalStrategy::class;
    protected static ?string $recordTitleAttribute = 'GoalStrategy';


    protected static string|null|\BackedEnum $navigationIcon = Heroicon::RocketLaunch;

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';

    protected static ?int $navigationSort = 5; // after Core Values

    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('Goals & Strategies');
    }

    public static function getModelLabel(): string
    {
        return __('Goal / Strategy');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Goals & Strategies');
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return GoalStrategyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoalStrategiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGoalStrategies::route('/'),
            'create' => CreateGoalStrategy::route('/create'),
            'edit'   => EditGoalStrategy::route('/{record}/edit'),
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
