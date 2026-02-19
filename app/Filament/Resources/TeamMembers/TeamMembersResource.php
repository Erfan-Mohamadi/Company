<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\CreateTeamMembers;
use App\Filament\Resources\TeamMembers\Pages\EditTeamMembers;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\Resources\TeamMembers\Schemas\TeamMembersForm;
use App\Filament\Resources\TeamMembers\Tables\TeamMembersTable;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeamMembersResource extends Resource
{
    protected static ?string $model = TeamMember::class;
    protected static ?string $recordTitleAttribute = 'TeamMember';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Users;

    protected static string|null|\UnitEnum $navigationGroup = 'Our Team';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Our Team');
    }

    public static function getNavigationLabel(): string
    {
        return __('Team Members');
    }

    public static function getModelLabel(): string
    {
        return __('Team Member');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Team Members');
    }
    public static function form(Schema $schema): Schema
    {
        return TeamMembersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamMembersTable::configure($table);
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
            'index' => ListTeamMembers::route('/'),
            'create' => CreateTeamMembers::route('/create'),
            'edit' => EditTeamMembers::route('/{record}/edit'),
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
