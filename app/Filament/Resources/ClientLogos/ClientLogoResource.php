<?php

namespace App\Filament\Resources\ClientLogos;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\ClientLogos\Pages\CreateClientLogo;
use App\Filament\Resources\ClientLogos\Pages\EditClientLogo;
use App\Filament\Resources\ClientLogos\Pages\ListClientLogos;
use App\Filament\Resources\ClientLogos\Schemas\ClientLogoForm;
use App\Filament\Resources\ClientLogos\Tables\ClientLogosTable;
use App\Models\ClientLogo;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientLogoResource extends Resource
{
    protected static ?string $model = ClientLogo::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::BuildingOffice2;
    protected static string|null|\UnitEnum $navigationGroup = 'Social Proof';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string  { return __('Social Proof'); }
    public static function getNavigationLabel(): string   { return __('Client Logos'); }
    public static function getModelLabel(): string        { return __('Client Logo'); }
    public static function getPluralModelLabel(): string  { return __('Client Logos'); }

    public static function form(Schema $schema): Schema  { return ClientLogoForm::configure($schema); }
    public static function table(Table $table): Table    { return ClientLogosTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListClientLogos::route('/'),
            'create' => CreateClientLogo::route('/create'),
            'edit'   => EditClientLogo::route('/{record}/edit'),
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
