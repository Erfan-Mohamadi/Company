<?php

namespace App\Filament\Resources\CeoMessages;

use App\Filament\Resources\CeoMessages\Pages\CreateCeoMessage;
use App\Filament\Resources\CeoMessages\Pages\EditCeoMessage;
use App\Filament\Resources\CeoMessages\Pages\ListCeoMessages;
use App\Filament\Resources\CeoMessages\Schemas\CeoMessageForm;
use App\Filament\Resources\CeoMessages\Tables\CeoMessagesTable;
use App\Models\CeoMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CeoMessageResource extends Resource
{
    protected static ?string $model = CeoMessage::class;
    protected static ?string $recordTitleAttribute = 'CeoMessage';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Megaphone;

    protected static string|null|\UnitEnum $navigationGroup = 'Our Team';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('Our Team');
    }

    public static function getNavigationLabel(): string
    {
        return __("CEO's Message");
    }

    public static function getModelLabel(): string
    {
        return __("CEO Message");
    }

    public static function getPluralModelLabel(): string
    {
        return __("CEO Messages");
    }
    public static function form(Schema $schema): Schema
    {
        return CeoMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CeoMessagesTable::configure($table);
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
            'index' => ListCeoMessages::route('/'),
            'create' => CreateCeoMessage::route('/create'),
            'edit' => EditCeoMessage::route('/{record}/edit'),
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
