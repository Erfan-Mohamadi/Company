<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Models\Language;
use App\Models\Testimonial;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Testimonial Content')
                    ->tabs([

                        // ─── Tab 1: Translations ────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('Translations')
                                    ->tabs(
                                        $languages->map(function ($language) {
                                            $code   = $language->name;
                                            $isMain = $code === Language::MAIN_LANG;

                                            return Tabs\Tab::make($language->label)
                                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                                ->badge($isMain ? __('Main') : null)
                                                ->schema([
                                                    TextInput::make("customer_name.{$code}")
                                                        ->label(__('Customer Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    TextInput::make("customer_position.{$code}")
                                                        ->label(__('Position / Title'))
                                                        ->maxLength(255),

                                                    TextInput::make("customer_company.{$code}")
                                                        ->label(__('Company'))
                                                        ->maxLength(255),

                                                    Textarea::make("testimonial_text.{$code}")
                                                        ->label(__('Testimonial'))
                                                        ->required($isMain)
                                                        ->rows(4)
                                                        ->columnSpanFull(),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Photo ───────────────────────────────────────
                        Tabs\Tab::make(__('Photo'))
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('testimonial_avatars')
                                    ->label(__('Customer Photo'))
                                    ->collection('testimonial_avatars')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('1:1')
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload customer photo (Max: 2 MB)')),
                            ]),

                        // ─── Tab 3: Rating & Video ──────────────────────────────
                        Tabs\Tab::make(__('Rating & Video'))
                            ->icon('heroicon-o-star')
                            ->schema([
                                Select::make('rating')
                                    ->label(__('Rating'))
                                    ->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐'])
                                    ->default(5)
                                    ->required(),

                                TextInput::make('video_url')
                                    ->label(__('Video URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->helperText(__('Optional video testimonial URL')),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('featured')
                                    ->label(__('Featured'))
                                    ->default(false),

                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(Testimonial::getStatuses())
                                    ->default(Testimonial::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
