<?php

namespace App\Filament\Resources\Languages\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Language Configuration')
                    ->tabs([
                        // ─── Tab 1: Language Information ────────────────────────
                        Tabs\Tab::make(__('Language Information'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Language Code'))
                                    ->placeholder(__('e.g., en, fa, ar, de'))
                                    ->required()
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('ISO 639-1 language code (2 letters) or custom code'))
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('name', Str::lower($state));
                                    }),

                                TextInput::make('label')
                                    ->label(__('Display Name'))
                                    ->placeholder(__('e.g., English, فارسی, العربية'))
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText(__('Name as shown in the interface')),

                                Toggle::make('is_rtl')
                                    ->label(__('Right-to-Left (RTL)'))
                                    ->helperText(__('Enable for languages like Arabic, Persian, Hebrew'))
                                    ->default(false)
                                    ->inline(false),
                            ])
                            ->columns(2),

                        // ─── Tab 2: Flag Image ──────────────────────────────────
                        Tabs\Tab::make(__('Flag'))
                            ->icon('heroicon-o-flag')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('flag')
                                    ->label(__('Flag'))
                                    ->collection('flag')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload flag image - Recommended: Square image (Maximum size: 2 MB)')),
                            ]),

                        // ─── Tab 3: Metadata (visible only on edit) ─────────────
                        Tabs\Tab::make(__('Metadata'))
                            ->icon('heroicon-o-information-circle')
                            ->visible(fn ($record) => $record !== null)
                            ->schema([
                                Placeholder::make('is_main_language')
                                    ->label(__('Language Status'))
                                    ->content(fn ($record) =>
                                    $record && $record->name === Language::MAIN_LANG
                                        ? __('⭐ This is the main language')
                                        : __('Secondary language')
                                    ),

                                Placeholder::make('created_at')
                                    ->label(__('Created'))
                                    ->content(fn ($record) => $record?->created_at?->format('F d, Y H:i:s') ?? '—'),

                                Placeholder::make('updated_at')
                                    ->label(__('Last Updated'))
                                    ->content(fn ($record) => $record?->updated_at?->format('F d, Y H:i:s') ?? '—'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
