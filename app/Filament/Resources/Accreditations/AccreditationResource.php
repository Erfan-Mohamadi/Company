<?php

namespace App\Filament\Resources\Accreditations;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Resources\Accreditations\Pages\CreateAccreditation;
use App\Filament\Resources\Accreditations\Pages\EditAccreditation;
use App\Filament\Resources\Accreditations\Pages\ListAccreditations;
use App\Filament\Resources\Accreditations\Schemas\AccreditationForm;
use App\Filament\Resources\Accreditations\Tables\AccreditationsTable;
use App\Models\Accreditation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccreditationResource extends Resource
{
    protected static ?string $model = Accreditation::class;
    protected static ?string $cluster = AboutCompany::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::ShieldCheck;
    protected static string|null|\UnitEnum $navigationGroup = 'Achievements';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string { return __('Achievements'); }
    public static function getNavigationLabel(): string  { return __('Accreditations'); }
    public static function getModelLabel(): string       { return __('Accreditation'); }
    public static function getPluralModelLabel(): string { return __('Accreditations'); }

    public static function form(Schema $schema): Schema { return AccreditationForm::configure($schema); }
    public static function table(Table $table): Table   { return AccreditationsTable::configure($table); }
    public static function getRelations(): array        { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListAccreditations::route('/'),
            'create' => CreateAccreditation::route('/create'),
            'edit'   => EditAccreditation::route('/{record}/edit'),
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
