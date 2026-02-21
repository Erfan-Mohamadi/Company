<?php

namespace App\Filament\Resources\AreaOfActivities\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Support\Str;

class AreaOfActivitiesForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();

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
                Tabs::make('Area of Activity Content')
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
                                                        ->maxLength(255)
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(function (string $context, $state, callable $set) use ($code) {
                                                            if ($context === 'create' && $code === Language::MAIN_LANG) {
                                                                $set('slug', Str::slug($state));
                                                            }
                                                        }),

                                                    Textarea::make("short_description.{$code}")
                                                        ->label(__('Short Description'))
                                                        ->maxLength(200)
                                                        ->rows(3)
                                                        ->columnSpanFull(),

                                                    RichEditor::make("description.{$code}")
                                                        ->label(__('Full Description'))
                                                        ->columnSpanFull()
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 200px;']),

                                                    Textarea::make("meta_description.{$code}")
                                                        ->label(__('Meta Description (SEO)'))
                                                        ->maxLength(160)
                                                        ->rows(2)
                                                        ->columnSpanFull(),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Industries ───────────────────────────────────
                        Tabs\Tab::make(__('Industries'))
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Repeater::make('industries')
                                    ->label(__('Industries'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('Industry Name'))
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')
                                            ->label(__('Description'))
                                            ->rows(2)
                                            ->maxLength(500),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->addActionLabel(__('Add Industry'))
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 3: Visual ───────────────────────────────────────
                        Tabs\Tab::make(__('Visual'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                IconPicker::make('icon')
                                    ->label(__('Section Icon'))
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
                                    ->helperText(__('Upload area image (Maximum size: 5 MB)')),
                            ]),

                        // ─── Tab 4: Settings ─────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('slug')
                                    ->label(__('Slug'))
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

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
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
