<?php

namespace App\Filament\Resources\Boxes\Schemas;

use App\Models\Box;
use App\Models\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;

class BoxForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;
        // ─── Shared RichEditor settings (unchanged) ─────────────
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
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
            ],
            'heading' => [
                'h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify',
                'bold', 'italic', 'underline', 'strike'
            ],
            'table' => [
                'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                'tableMergeCells', 'tableSplitCell',
                'tableToggleHeaderRow', 'tableToggleHeaderCell',
                'tableDelete',
            ],
            'attachFiles' => [
                'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'
            ],
        ];
        return $schema
            ->components([
                Tabs::make('Box Content')
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
                                                    TextInput::make("header.{$code}")
                                                        ->label(__('Header'))
                                                        ->required($isMain)
                                                        ->maxLength(255),

                                                    RichEditor::make("description.{$code}")
                                                        ->label(__('Description'))
                                                        ->columnSpan('full')
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 140px;']),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Visual ──────────────────────────────────────
                        Tabs\Tab::make(__('Visual'))
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Select::make('box_type')
                                    ->label(__('Box Type'))
                                    ->options(Box::getBoxTypes())
                                    ->default(Box::TYPE_ICON)
                                    ->live()
                                    ->required(),

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

                                SpatieMediaLibraryFileUpload::make('box_images')
                                    ->label(__('Image'))
                                    ->collection('box_images')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->helperText(__('Upload box image (Max: 2 MB)'))
                                    ->visible(fn ($get) => in_array($get('box_type'), [Box::TYPE_IMAGE, Box::TYPE_ICON_IMAGE])),

                                ColorPicker::make('background_color')
                                    ->label(__('Background Color')),

                                ColorPicker::make('text_color')
                                    ->label(__('Text Color')),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Link ────────────────────────────────────────
                        Tabs\Tab::make(__('Link'))
                            ->icon('heroicon-o-link')
                            ->schema([
                                TextInput::make('link_url')
                                    ->label(__('Link URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),

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
                                    ->options(Box::getStatuses())
                                    ->default(Box::STATUS_DRAFT)
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
