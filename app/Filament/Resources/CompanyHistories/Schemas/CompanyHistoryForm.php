<?php

namespace App\Filament\Resources\CompanyHistories\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\Facades\App;

class CompanyHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi   = App::isLocale('fa');

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            ['table', 'attachFiles'],
            ['undo', 'redo'],
        ];

        $floatingToolbars = [
            'paragraph' => [
                'h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript',
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
            ],
            'heading' => [
                'h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
                'bold', 'italic', 'underline', 'strike',
            ],
            'table' => [
                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                'tableMergeCells', 'tableSplitCell',
                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                'tableDelete',
            ],
            'attachFiles' => ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
        ];

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Company History Content')
                    ->tabs([
                        // ─── Tab 1: Translations ─────────────────────────────────
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

                                                    RichEditor::make("description.{$code}")
                                                        ->label(__('Description'))
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

                        // ─── Tab 2: Timeline ────────────────────────────────────
                        Tabs\Tab::make(__('Timeline'))
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                DatePicker::make('date')
                                    ->label(__('Date'))
                                    ->native(false)
                                    ->displayFormat($isFarsi ? 'j F Y' : 'M j, Y')
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the date in Jalali calendar')
                                        : __('Select the date')
                                    )
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali())
                                    ->required(),

                                Select::make('achievement_type')
                                    ->label(__('Achievement Type'))
                                    ->options([
                                        'founding'       => __('Founding'),
                                        'product_launch' => __('Product Launch'),
                                        'expansion'      => __('Expansion'),
                                        'award'          => __('Award'),
                                        'partnership'    => __('Partnership'),
                                        'other'          => __('Other'),
                                    ]),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Visual ──────────────────────────────────────
                        Tabs\Tab::make(__('Visual'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                IconPicker::make('icon')
                                    ->label(__('Icon'))
                                    ->listSearchResults()
                                    ->sets(['heroicons', 'filament'])
                                    ->closeOnSelect()
                                    ->gridSearchResults()
                                    ->iconsSearchResults()
                                    ->gridSearchResults()
                                    ->extraAttributes([
                                        'style' => 'max-height: 50vh; overflow: auto'
                                    ]),

                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label(__('Image'))
                                    ->collection('image')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload image (Max: 5 MB)')),
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
                                    ->options([
                                        'draft'     => __('Draft'),
                                        'published' => __('Published'),
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
