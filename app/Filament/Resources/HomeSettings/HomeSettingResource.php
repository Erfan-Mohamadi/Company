<?php

namespace App\Filament\Resources\HomeSettings;

use App\Filament\Resources\HomeSettings\Schemas\HomeSettingForm;
use App\Filament\Resources\HomeSettings\Tables\HomeSettingsTable;
use App\Settings\HomeSettings;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;


class HomeSettingResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string $settings = HomeSettings::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Home Settings');
    }

    public function getTitle(): string
    {
        return __('Home Settings');
    }

    protected static ?string $navigationLabel = 'تنظیمات صفحه اصلی';

    public static function form(Schema $schema): Schema
    {
        return HomeSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListHomeSettings::route('/'),
            'create'  => Pages\CreateHomeSetting::route('/create'),
            'edit'    => Pages\EditHomeSetting::route('/{record}/edit'),
        ];
    }
}
