<?php

namespace App\Filament\Resources\Sliders\Schemas;

use App\Models\Language;
use App\Models\Slider;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $isFarsi       = App::isLocale('fa');
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Slider Content')
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
                                                    TextInput::make("title.{$code}")
                                                        ->label(__('Title'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    TextInput::make("subtitle.{$code}")
                                                        ->label(__('Subtitle'))
                                                        ->maxLength(255),

                                                    Textarea::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->rows(3)
                                                        ->maxLength(500)
                                                        ->columnSpanFull(),

                                                    TextInput::make("link_text.{$code}")
                                                        ->label(__('Button Label'))
                                                        ->maxLength(100),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Media ───────────────────────────────────────
                        Tabs\Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('slider_media')
                                    ->label(__('Slide Image'))
                                    ->collection('slider_media')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload slide image (Maximum size: 5 MB). If a video URL is set below, the video takes priority.')),

                                TextInput::make('video_url')
                                    ->label(__('Video URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull()
                                    ->helperText(__('Optional. Overrides the image when set.')),
                            ]),

                        // ─── Tab 3: Link & Button ───────────────────────────────
                        Tabs\Tab::make(__('Link & Button'))
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                TextInput::make('link_url')
                                    ->label(__('Button URL'))
                                    ->url()
                                    ->maxLength(500),

                                Select::make('button_style')
                                    ->label(__('Button Style'))
                                    ->options(Slider::getButtonStyles())
                                    ->default(Slider::BUTTON_STYLE_PRIMARY),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Animation & Timing ──────────────────────────
                        Tabs\Tab::make(__('Animation & Timing'))
                            ->icon('heroicon-o-play')
                            ->schema([
                                Select::make('animation_type')
                                    ->label(__('Animation'))
                                    ->options(Slider::getAnimationTypes())
                                    ->default(Slider::ANIMATION_FADE),

                                TextInput::make('display_duration')
                                    ->label(__('Display Duration (ms)'))
                                    ->numeric()
                                    ->default(5000)
                                    ->helperText(__('How long each slide is shown in milliseconds. e.g. 5000 = 5 seconds')),

                                DateTimePicker::make('start_date')
                                    ->label(__('Start Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                                    ->closeOnDateSelection()
                                    ->helperText(__('Leave empty to always show.'))
                                    ->when($isFarsi, fn (DateTimePicker $p) => $p->jalali()),

                                DateTimePicker::make('end_date')
                                    ->label(__('End Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                                    ->closeOnDateSelection()
                                    ->after('start_date')
                                    ->helperText(__('Leave empty to show indefinitely.'))
                                    ->when($isFarsi, fn (DateTimePicker $p) => $p->jalali()),
                            ])
                            ->columns(2),

                        // ─── Tab 5: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(Slider::getStatuses())
                                    ->default(Slider::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
