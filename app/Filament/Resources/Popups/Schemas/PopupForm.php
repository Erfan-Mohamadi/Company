<?php

namespace App\Filament\Resources\Popups\Schemas;

use App\Models\Language;
use App\Models\Popup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class PopupForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $isFarsi       = App::isLocale('fa');
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'strike', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'bulletList', 'orderedList'],
            ['attachFiles'],
            ['undo', 'redo'],
        ];

        $floatingToolbars = [
            'paragraph'   => ['h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
            'heading'     => ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'bold', 'italic'],
            'attachFiles' => ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
        ];

        return $schema
            ->components([
                Tabs::make('Popup Content')
                    ->tabs([

                        // ─── Tab 1: Translations ────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('Translations')
                                    ->tabs(
                                        $languages->map(function ($language) use ($toolbarButtons, $floatingToolbars) {
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

                                                    RichEditor::make("content.{$code}")
                                                        ->label(__('Content'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 160px;']),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Trigger ─────────────────────────────────────
                        Tabs\Tab::make(__('Trigger'))
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                Select::make('popup_type')
                                    ->label(__('Popup Type'))
                                    ->options(Popup::getPopupTypes())
                                    ->default(Popup::TYPE_ANNOUNCEMENT)
                                    ->required(),

                                Select::make('trigger_type')
                                    ->label(__('Trigger'))
                                    ->options(Popup::getTriggerTypes())
                                    ->default(Popup::TRIGGER_ON_LOAD)
                                    ->live()
                                    ->required(),

                                TextInput::make('display_delay')
                                    ->label(__('Display Delay (seconds)'))
                                    ->numeric()
                                    ->default(0)
                                    ->visible(fn ($get) => in_array($get('trigger_type'), [Popup::TRIGGER_ON_LOAD, Popup::TRIGGER_TIMED]))
                                    ->helperText(__('Seconds to wait before showing the popup.')),

                                Select::make('frequency')
                                    ->label(__('Show Frequency'))
                                    ->options(Popup::getFrequencies())
                                    ->default(Popup::FREQUENCY_ONCE_SESSION)
                                    ->required(),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Scheduling & Targeting ──────────────────────
                        Tabs\Tab::make(__('Scheduling & Targeting'))
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label(__('Start Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                                    ->closeOnDateSelection()
                                    ->helperText(__('Leave empty to show immediately.'))
                                    ->when($isFarsi, fn (DateTimePicker $p) => $p->jalali()),

                                DateTimePicker::make('end_date')
                                    ->label(__('End Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                                    ->closeOnDateSelection()
                                    ->after('start_date')
                                    ->helperText(__('Leave empty to show indefinitely.'))
                                    ->when($isFarsi, fn (DateTimePicker $p) => $p->jalali()),

                                TagsInput::make('pages')
                                    ->label(__('Target Pages'))
                                    ->placeholder(__('e.g. /products, /about'))
                                    ->columnSpanFull()
                                    ->helperText(__('Leave empty to show on all pages. Add URL patterns to restrict to specific pages.')),
                            ])
                            ->columns(2),

                        // ─── Tab 4: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options(Popup::getStatuses())
                                    ->default(Popup::STATUS_DRAFT)
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
