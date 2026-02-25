<?php

namespace App\Filament\Resources\Boxes;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\Boxes\Pages\CreateBox;
use App\Filament\Resources\Boxes\Pages\EditBox;
use App\Filament\Resources\Boxes\Pages\ListBoxes;
use App\Filament\Resources\Boxes\Schemas\BoxForm;
use App\Filament\Resources\Boxes\Tables\BoxesTable;
use App\Models\Box;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoxResource extends Resource
{
    protected static ?string $model = Box::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Squares2x2;
    protected static string|null|\UnitEnum $navigationGroup = 'Content Blocks';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string  { return __('Content Blocks'); }
    public static function getNavigationLabel(): string   { return __('Icon Boxes'); }
    public static function getModelLabel(): string        { return __('Box'); }
    public static function getPluralModelLabel(): string  { return __('Boxes'); }

    public static function form(Schema $schema): Schema  { return BoxForm::configure($schema); }
    public static function table(Table $table): Table    { return BoxesTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListBoxes::route('/'),
            'create' => CreateBox::route('/create'),
            'edit'   => EditBox::route('/{record}/edit'),
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
