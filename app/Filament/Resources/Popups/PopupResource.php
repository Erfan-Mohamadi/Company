<?php

namespace App\Filament\Resources\Popups;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\Popups\Pages\CreatePopup;
use App\Filament\Resources\Popups\Pages\EditPopup;
use App\Filament\Resources\Popups\Pages\ListPopups;
use App\Filament\Resources\Popups\Schemas\PopupForm;
use App\Filament\Resources\Popups\Tables\PopupsTable;
use App\Models\Popup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PopupResource extends Resource
{
    protected static ?string $model = Popup::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Window;
    protected static string|null|\UnitEnum $navigationGroup = 'Engagement';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string  { return __('Engagement'); }
    public static function getNavigationLabel(): string   { return __('Pop-ups'); }
    public static function getModelLabel(): string        { return __('Pop-up'); }
    public static function getPluralModelLabel(): string  { return __('Pop-ups'); }

    public static function form(Schema $schema): Schema  { return PopupForm::configure($schema); }
    public static function table(Table $table): Table    { return PopupsTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListPopups::route('/'),
            'create' => CreatePopup::route('/create'),
            'edit'   => EditPopup::route('/{record}/edit'),
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
