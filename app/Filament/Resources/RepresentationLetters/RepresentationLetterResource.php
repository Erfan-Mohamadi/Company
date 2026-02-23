<?php

namespace App\Filament\Resources\RepresentationLetters;

use App\Filament\Resources\RepresentationLetters\Pages\CreateRepresentationLetter;
use App\Filament\Resources\RepresentationLetters\Pages\EditRepresentationLetter;
use App\Filament\Resources\RepresentationLetters\Pages\ListRepresentationLetters;
use App\Filament\Resources\RepresentationLetters\Schemas\RepresentationLetterForm;
use App\Filament\Resources\RepresentationLetters\Tables\RepresentationLettersTable;
use App\Models\RepresentationLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RepresentationLetterResource extends Resource
{
    protected static ?string $model = RepresentationLetter::class;
    protected static ?string $recordTitleAttribute = 'RepresentationLetter';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::DocumentDuplicate;
    protected static string|null|\UnitEnum $navigationGroup = 'Achievements';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string   { return __('Achievements'); }
    public static function getNavigationLabel(): string    { return __('Representation Letters'); }
    public static function getModelLabel(): string         { return __('Representation Letter'); }
    public static function getPluralModelLabel(): string   { return __('Representation Letters'); }

    public static function form(Schema $schema): Schema
    {
        return RepresentationLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RepresentationLettersTable::configure($table);
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
            'index' => ListRepresentationLetters::route('/'),
            'create' => CreateRepresentationLetter::route('/create'),
            'edit' => EditRepresentationLetter::route('/{record}/edit'),
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
