<?php

namespace App\Filament\Resources\BoxTexts;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\BoxTexts\Pages\CreateBoxText;
use App\Filament\Resources\BoxTexts\Pages\EditBoxText;
use App\Filament\Resources\BoxTexts\Pages\ListBoxTexts;
use App\Filament\Resources\BoxTexts\Schemas\BoxTextForm;
use App\Filament\Resources\BoxTexts\Tables\BoxTextsTable;
use App\Models\BoxText;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoxTextResource extends Resource
{
    protected static ?string $model = BoxText::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Bars3BottomLeft;
    protected static string|null|\UnitEnum $navigationGroup = 'Content Blocks';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string  { return __('Content Blocks'); }
    public static function getNavigationLabel(): string   { return __('Text Sections'); }
    public static function getModelLabel(): string        { return __('Text Section'); }
    public static function getPluralModelLabel(): string  { return __('Text Sections'); }

    public static function form(Schema $schema): Schema  { return BoxTextForm::configure($schema); }
    public static function table(Table $table): Table    { return BoxTextsTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListBoxTexts::route('/'),
            'create' => CreateBoxText::route('/create'),
            'edit'   => EditBoxText::route('/{record}/edit'),
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
