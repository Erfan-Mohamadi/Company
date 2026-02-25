<?php

namespace App\Filament\Resources\Sliders;

use App\Filament\Clusters\AboutCompany;
use App\Filament\Clusters\HomePage;
use App\Filament\Resources\Sliders\Pages\CreateSlider;
use App\Filament\Resources\Sliders\Pages\EditSlider;
use App\Filament\Resources\Sliders\Pages\ListSliders;
use App\Filament\Resources\Sliders\Schemas\SliderForm;
use App\Filament\Resources\Sliders\Tables\SlidersTable;
use App\Models\Slider;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Photo;
    protected static string|null|\UnitEnum $navigationGroup = 'Hero & Banners';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string  { return __('Hero & Banners'); }
    public static function getNavigationLabel(): string   { return __('Sliders'); }
    public static function getModelLabel(): string        { return __('Slider'); }
    public static function getPluralModelLabel(): string  { return __('Sliders'); }

    public static function form(Schema $schema): Schema  { return SliderForm::configure($schema); }
    public static function table(Table $table): Table    { return SlidersTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListSliders::route('/'),
            'create' => CreateSlider::route('/create'),
            'edit'   => EditSlider::route('/{record}/edit'),
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
