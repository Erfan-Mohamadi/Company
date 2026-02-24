<?php

namespace App\Filament\Resources\Milestones;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\Milestones\Pages\CreateMilestone;
use App\Filament\Resources\Milestones\Pages\EditMilestone;
use App\Filament\Resources\Milestones\Pages\ListMilestones;
use App\Filament\Resources\Milestones\Schemas\MilestoneForm;
use App\Filament\Resources\Milestones\Tables\MilestonesTable;
use App\Models\Milestone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MilestoneResource extends Resource
{
    protected static ?string $cluster = AboutCompany::class;

    protected static ?string $model = Milestone::class;
    protected static ?string $recordTitleAttribute = 'Milestone';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Flag;
    protected static string|null|\UnitEnum $navigationGroup = 'Achievements';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string   { return __('Achievements'); }
    public static function getNavigationLabel(): string    { return __('Milestones'); }
    public static function getModelLabel(): string         { return __('Milestone'); }
    public static function getPluralModelLabel(): string   { return __('Milestones'); }
    public static function form(Schema $schema): Schema
    {
        return MilestoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MilestonesTable::configure($table);
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
            'index' => ListMilestones::route('/'),
            'create' => CreateMilestone::route('/create'),
            'edit' => EditMilestone::route('/{record}/edit'),
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
