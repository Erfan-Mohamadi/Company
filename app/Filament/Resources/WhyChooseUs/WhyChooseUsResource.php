<?php

namespace App\Filament\Resources\WhyChooseUs;

use App\Filament\Resources\WhyChooseUs\Pages\CreateWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Pages\EditWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Pages\ListWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Schemas\WhyChooseUsForm;
use App\Filament\Resources\WhyChooseUs\Tables\WhyChooseUsTable;
use App\Models\WhyChooseUs;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WhyChooseUsResource extends Resource
{
    protected static ?string $model = WhyChooseUs::class;

    protected static ?string $recordTitleAttribute = 'WhyChooseUs';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Star;

    protected static string|null|\UnitEnum $navigationGroup = 'Company Profile';

    protected static ?int $navigationSort = 6; // after Goals & Strategies

    public static function getNavigationGroup(): ?string
    {
        return __('Company Profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('Why Choose Us');
    }

    public static function getModelLabel(): string
    {
        return __('Why Choose Us');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Why Choose Us');
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return WhyChooseUsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhyChooseUsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWhyChooseUs::route('/'),
            'create' => CreateWhyChooseUs::route('/create'),
            'edit'   => EditWhyChooseUs::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $drafts = static::getModel()::where('status', 'draft')->count();
        if ($drafts) {
            return $drafts;
        }
        return static::getModel()::where('status', 'published')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'draft')->exists()
            ? 'warning'
            : 'success';
    }
}
