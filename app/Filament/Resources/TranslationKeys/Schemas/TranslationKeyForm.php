<?php

namespace App\Filament\Resources\TranslationKeys\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class TranslationKeyForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi = App::isLocale('fa');

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Translation Key Content')
                    ->tabs([
                        // ─── Tab 1: Key Information ─────────────────────────────
                        Tabs\Tab::make(__('Key Information'))
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('key')
                                    ->label(__('Translation Key'))
                                    ->placeholder(__('e.g., welcome_message, button.submit'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('Unique identifier for this translation'))
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $set('key', Str::slug($state, '_'));
                                    })
                                    ->columnSpan(2),

                                TextInput::make('group')
                                    ->label(__('Group'))
                                    ->placeholder(__('e.g., auth, validation, general'))
                                    ->maxLength(255)
                                    ->helperText(__('Optional: Group related translations together')),

                                Toggle::make('message')
                                    ->label(__('Is Message'))
                                    ->helperText(__('Mark this as a user-facing message'))
                                    ->default(false),
                            ])
                            ->columns(3),

                        // ─── Tab 2: Translations ────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('Translations')
                                    ->tabs(
                                        $languages->map(function ($language) {
                                            return Tabs\Tab::make($language->label)
                                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                                ->badge($language->name === Language::MAIN_LANG ? __('Main') : null)
                                                ->schema([
                                                    Textarea::make("value.{$language->name}")
                                                        ->label(__("Translation"))
                                                        ->rows(4)
                                                        ->maxLength(65535)
                                                        ->placeholder(__("Enter translation for {$language->label}"))
                                                        ->helperText($language->is_rtl ? __('→ RTL Language (Right-to-Left)') : __('← LTR Language (Left-to-Right)'))
                                                        ->required($language->name === Language::MAIN_LANG),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 3: Metadata (only on edit) ─────────────────────
                        Tabs\Tab::make(__('Metadata'))
                            ->icon('heroicon-o-information-circle')
                            ->visible(fn ($record) => $record !== null)
                            ->schema([
                                Placeholder::make('updated_at')
                                    ->label(__('Last Updated'))
                                    ->content(function ($record) use ($isFarsi) {
                                        if (! $record?->updated_at) {
                                            return '-';
                                        }

                                        return $isFarsi
                                            ? verta($record->updated_at)->format('j F Y H:i:s')
                                            : $record->updated_at->format('F d, Y H:i:s');
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
