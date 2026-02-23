<?php

namespace App\Filament\Resources\AreaOfActivities;

use App\Filament\Resources\AreaOfActivities\Pages\CreateAreaOfActivities;
use App\Filament\Resources\AreaOfActivities\Pages\EditAreaOfActivities;
use App\Filament\Resources\AreaOfActivities\Pages\ListAreaOfActivities;
use App\Filament\Resources\AreaOfActivities\Schemas\AreaOfActivitiesForm;
use App\Filament\Resources\AreaOfActivities\Tables\AreaOfActivitiesTable;
use App\Models\AreaOfActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AreaOfActivitiesResource extends Resource
{
    protected static ?string $model = AreaOfActivity::class;
    protected static ?string $recordTitleAttribute = 'AreaOfActivity';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Briefcase;

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('Areas of Activity');
    }

    public static function getModelLabel(): string
    {
        return __('Area of Activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Areas of Activity');
    }
    public static function form(Schema $schema): Schema
    {
        return AreaOfActivitiesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AreaOfActivitiesTable::configure($table);
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
            'index' => ListAreaOfActivities::route('/'),
            'create' => CreateAreaOfActivities::route('/create'),
            'edit' => EditAreaOfActivities::route('/{record}/edit'),
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
