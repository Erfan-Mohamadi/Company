<?php

namespace App\Filament\Resources\LeadershipTeams;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\LeadershipTeams\Pages\CreateLeadershipTeam;
use App\Filament\Resources\LeadershipTeams\Pages\EditLeadershipTeam;
use App\Filament\Resources\LeadershipTeams\Pages\ListLeadershipTeams;
use App\Filament\Resources\LeadershipTeams\Schemas\LeadershipTeamForm;
use App\Filament\Resources\LeadershipTeams\Tables\LeadershipTeamsTable;
use App\Models\LeadershipTeam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeadershipTeamResource extends Resource
{
    protected static ?string $model = LeadershipTeam::class;
    protected static ?string $recordTitleAttribute = 'LeadershipTeam';
    protected static ?string $cluster = AboutCompany::class;

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::UserGroup;

    protected static string|null|\UnitEnum $navigationGroup = 'Our Team';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Our Team');
    }

    public static function getNavigationLabel(): string
    {
        return __('Leadership Team');
    }

    public static function getModelLabel(): string
    {
        return __('Leadership Member');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Leadership Team');
    }


    public static function form(Schema $schema): Schema
    {
        return LeadershipTeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadershipTeamsTable::configure($table);
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
            'index' => ListLeadershipTeams::route('/'),
            'create' => CreateLeadershipTeam::route('/create'),
            'edit' => EditLeadershipTeam::route('/{record}/edit'),
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
