<?php

namespace App\Filament\Resources\HistoryMilestones;

use App\Filament\Resources\HistoryMilestones\Pages\CreateHistoryMilestones;
use App\Filament\Resources\HistoryMilestones\Pages\EditHistoryMilestones;
use App\Filament\Resources\HistoryMilestones\Pages\ListHistoryMilestones;
use App\Filament\Resources\HistoryMilestones\Schemas\HistoryMilestonesForm;
use App\Filament\Resources\HistoryMilestones\Tables\HistoryMilestonesTable;
use App\Models\HistoryMilestone;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HistoryMilestonesResource extends Resource
{
    protected static ?string $model = HistoryMilestone::class;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Clock;

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';
    protected static ?string $recordTitleAttribute = 'HistoryMilestones';

    protected static ?int $navigationSort = 6;
    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('History Milestones');
    }

    public static function getModelLabel(): string
    {
        return __('History Milestone');
    }

    public static function getPluralModelLabel(): string
    {
        return __('History Milestones');
    }

    public static function form(Schema $schema): Schema
    {
        return HistoryMilestonesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoryMilestonesTable::configure($table);
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
            'index' => ListHistoryMilestones::route('/'),
            'create' => CreateHistoryMilestones::route('/create'),
            'edit' => EditHistoryMilestones::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'published')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
