<?php

namespace App\Filament\Resources\MissionVisions;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\MissionVisions\Pages\CreateMissionVision;
use App\Filament\Resources\MissionVisions\Pages\EditMissionVision;
use App\Filament\Resources\MissionVisions\Pages\ListMissionVisions;
use App\Filament\Resources\MissionVisions\Schemas\MissionVisionForm;
use App\Filament\Resources\MissionVisions\Tables\MissionVisionsTable;
use App\Models\MissionVision;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MissionVisionResource extends Resource
{
    protected static ?string $model = MissionVision::class;
    protected static ?string $cluster = AboutCompany::class;

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-eye';

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';
    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;
    public static function getNavigationLabel(): string
    {
        return __('Mission Vision');
    }

    public static function getModelLabel(): string
    {
        return __('Mission Vision');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Mission Vision');
    }
    protected static ?string $recordTitleAttribute = 'header';

    public static function form(Schema $schema): Schema
    {
        return MissionVisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MissionVisionsTable::configure($table);
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
            'index' => ListMissionVisions::route('/'),
            'create' => CreateMissionVision::route('/create'),
            'edit' => EditMissionVision::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return MissionVision::query()->count() === 0;
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
