<?php

namespace App\Filament\Resources\TranslationKeys;

use App\Filament\Resources\TranslationKeys\Pages\CreateTranslationKey;
use App\Filament\Resources\TranslationKeys\Pages\EditTranslationKey;
use App\Filament\Resources\TranslationKeys\Pages\ListTranslationKeys;
use App\Filament\Resources\TranslationKeys\Pages\ViewTranslationKey;
use App\Filament\Resources\TranslationKeys\Schemas\TranslationKeyForm;
use App\Filament\Resources\TranslationKeys\Schemas\TranslationKeyInfolist;
use App\Filament\Resources\TranslationKeys\Tables\TranslationKeysTable;
use App\Models\TranslationKey;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TranslationKeyResource extends Resource
{
    protected static ?string $model = TranslationKey::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'translationkey';
    protected static ?string $navigationLabel = null;
    protected static ?string $modelLabel = null;
    protected static ?string $pluralModelLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('Translation Keys');
    }

    public static function getModelLabel(): string
    {
        return __('Translation Keys');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Translation Keys');
    }
    public static function form(Schema $schema): Schema
    {
        return TranslationKeyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TranslationKeyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TranslationKeysTable::configure($table);
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
            'index' => ListTranslationKeys::route('/'),
            'create' => CreateTranslationKey::route('/create'),
            'view' => ViewTranslationKey::route('/{record}'),
            'edit' => EditTranslationKey::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
