<?php

namespace App\Filament\Resources\Statistics\Schemas;

use App\Models\Language;
use App\Models\Statistic;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class StatisticForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Statistic Content')
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
                                                        ->label(__('Label'))
                                                        ->required($isMain)
                                                        ->maxLength(100)
                                                        ->helperText(__('e.g. "Happy Clients", "Years Experience"')),

                                                    TextInput::make("prefix.{$code}")
                                                        ->label(__('Prefix'))
                                                        ->maxLength(20)
                                                        ->helperText(__('Shown before the number. e.g. "$"')),

                                                    TextInput::make("suffix.{$code}")
                                                        ->label(__('Suffix'))
                                                        ->maxLength(20)
                                                        ->helperText(__('Shown after the number. e.g. "+", "K", "%"')),
                                                ])
                                                ->columns(3);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Number & Icon ───────────────────────────────
                        Tabs\Tab::make(__('Number & Icon'))
                            ->icon('heroicon-o-hashtag')
                            ->schema([
                                TextInput::make('number')
                                    ->label(__('Number'))
                                    ->required()
                                    ->maxLength(50)
                                    ->helperText(__('e.g. 500, 1200, 15. Stored as string to avoid scientific notation.')),

                                TextInput::make('icon')
                                    ->label(__('Icon'))
                                    ->placeholder('heroicon-o-users')
                                    ->helperText(__('Heroicon name, e.g. heroicon-o-users')),

                                Toggle::make('animation_enabled')
                                    ->label(__('Count-up Animation'))
                                    ->helperText(__('Animate the number counting up when scrolled into view.'))
                                    ->default(true),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(Statistic::getStatuses())
                                    ->default(Statistic::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
