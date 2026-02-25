<?php

namespace App\Filament\Resources\CallToActions;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\CallToActions\Pages\CreateCallToAction;
use App\Filament\Resources\CallToActions\Pages\EditCallToAction;
use App\Filament\Resources\CallToActions\Pages\ListCallToActions;
use App\Filament\Resources\CallToActions\Schemas\CallToActionForm;
use App\Filament\Resources\CallToActions\Tables\CallToActionsTable;
use App\Models\CallToAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CallToActionResource extends Resource
{
    protected static ?string $model = CallToAction::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::CursorArrowRays;
    protected static string|null|\UnitEnum $navigationGroup = 'Content Blocks';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string  { return __('Content Blocks'); }
    public static function getNavigationLabel(): string   { return __('Call to Actions'); }
    public static function getModelLabel(): string        { return __('Call to Action'); }
    public static function getPluralModelLabel(): string  { return __('Call to Actions'); }

    public static function form(Schema $schema): Schema  { return CallToActionForm::configure($schema); }
    public static function table(Table $table): Table    { return CallToActionsTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListCallToActions::route('/'),
            'create' => CreateCallToAction::route('/create'),
            'edit'   => EditCallToAction::route('/{record}/edit'),
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
