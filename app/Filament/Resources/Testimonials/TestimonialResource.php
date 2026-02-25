<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Clusters\HomePage;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use App\Models\Testimonial;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static ?string $cluster = HomePage::class;
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::ChatBubbleLeftRight;
    protected static string|null|\UnitEnum $navigationGroup = 'Social Proof';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string  { return __('Social Proof'); }
    public static function getNavigationLabel(): string   { return __('Testimonials'); }
    public static function getModelLabel(): string        { return __('Testimonial'); }
    public static function getPluralModelLabel(): string  { return __('Testimonials'); }

    public static function form(Schema $schema): Schema  { return TestimonialForm::configure($schema); }
    public static function table(Table $table): Table    { return TestimonialsTable::configure($table); }
    public static function getRelations(): array         { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit'   => EditTestimonial::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        if ($pending) return $pending;
        $drafts = static::getModel()::where('status', 'draft')->count();
        return $drafts ?: (static::getModel()::where('status', 'active')->count() ?: null);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        if (static::getModel()::where('status', 'pending')->exists()) return 'danger';
        return static::getModel()::where('status', 'draft')->exists() ? 'warning' : 'success';
    }
}
