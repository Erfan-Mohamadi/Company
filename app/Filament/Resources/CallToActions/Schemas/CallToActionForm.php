<?php

namespace App\Filament\Resources\CallToActions\Schemas;

use App\Models\CallToAction;
use App\Models\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CallToActionForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('CTA Content')
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

                                                    Textarea::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->rows(3)
                                                        ->columnSpanFull(),

                                                    TextInput::make("button_text.{$code}")
                                                        ->label(__('Button Label'))
                                                        ->maxLength(100),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Button ──────────────────────────────────────
                        Tabs\Tab::make(__('Button'))
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                TextInput::make('button_link')
                                    ->label(__('Button URL'))
                                    ->url()
                                    ->maxLength(500),

                                Select::make('button_style')
                                    ->label(__('Button Style'))
                                    ->options(CallToAction::getButtonStyles())
                                    ->default(CallToAction::BUTTON_STYLE_PRIMARY),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Background ──────────────────────────────────
                        Tabs\Tab::make(__('Background'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('cta_backgrounds')
                                    ->label(__('Background Image'))
                                    ->collection('cta_backgrounds')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload background image (Max: 5 MB). Overrides background color when set.')),

                                ColorPicker::make('background_color')
                                    ->label(__('Background Color'))
                                    ->helperText(__('Used when no background image is set.')),

                                TextInput::make('overlay_opacity')
                                    ->label(__('Overlay Opacity (%)'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(50)
                                    ->helperText(__('Dark overlay opacity over background image. 0 = none, 100 = full.')),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(CallToAction::getStatuses())
                                    ->default(CallToAction::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
