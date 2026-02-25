<?php

namespace App\Filament\Resources\ClientLogos\Schemas;

use App\Models\ClientLogo;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ClientLogoForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Client Logo Content')
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
                                                    TextInput::make("name.{$code}")
                                                        ->label(__('Name'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    Textarea::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->rows(2)
                                                        ->columnSpanFull(),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Logo ────────────────────────────────────────
                        Tabs\Tab::make(__('Logo'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('client_logo')
                                    ->label(__('Logo'))
                                    ->collection('client_logo')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload logo — SVG, PNG, or WebP recommended (Max: 2 MB)')),

                                TextInput::make('website_url')
                                    ->label(__('Website URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 3: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Select::make('type')
                                    ->label(__('Type'))
                                    ->options(ClientLogo::getTypes())
                                    ->default(ClientLogo::TYPE_CLIENT)
                                    ->required(),

                                Toggle::make('featured')
                                    ->label(__('Featured'))
                                    ->default(false),

                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(ClientLogo::getStatuses())
                                    ->default(ClientLogo::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
