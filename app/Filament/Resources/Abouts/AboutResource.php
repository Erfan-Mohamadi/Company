<?php

namespace App\Filament\Resources\Abouts;

use App\Filament\Resources\Abouts\Pages\CreateAbout;
use App\Filament\Resources\Abouts\Pages\EditAbout;
use App\Filament\Resources\Abouts\Pages\ListAbouts;
use App\Filament\Resources\Abouts\Schemas\AboutForm;
use App\Filament\Resources\Abouts\Tables\AboutsTable;
use App\Models\About;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;


    protected static ?string $recordTitleAttribute = 'about';
    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-building-office-2';
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
        return __('Abouts');
    }
    public static function getModelLabel(): string
    {
        return __('Abouts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Abouts');
    }
    public static function form(Schema $schema): Schema
    {
        return AboutForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AboutsTable::configure($table);
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
            'index' => ListAbouts::route('/'),
            'create' => CreateAbout::route('/create'),
            'edit' => EditAbout::route('/{record}/edit'),
        ];
    }
}
