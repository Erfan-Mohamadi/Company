<?php

namespace App\Filament\Resources\MissionVisions\Schemas;

use App\Models\Language;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class MissionVisionForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi   = App::isLocale('fa');

        // RichEditor toolbar configuration — matching your previous style
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

        // Try to activate main language tab by default
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([


                // ─── Language-specific content ──────────────────────────────────────
                Section::make(__('Content Translations'))
                    ->description(__('Mission, vision and descriptions in each language'))
                    ->schema([
                        Tabs::make('translations')
                            ->label(__('Translations'))
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
                                                ->maxLength(255)
                                                ->helperText(__('Optional main title for this section')),

                                            TextInput::make("vision_title.{$code}")
                                                ->label(__('Vision Title'))
                                                ->maxLength(255),

                                            RichEditor::make("vision_text.{$code}")
                                                ->label(__('Vision Text'))
                                                ->columnSpanFull()
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 160px;']),

                                            TextInput::make("mission_title.{$code}")
                                                ->label(__('Mission Title'))
                                                ->maxLength(255),

                                            RichEditor::make("mission_text.{$code}")
                                                ->label(__('Mission Text'))
                                                ->columnSpanFull()
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 160px;']),

                                            RichEditor::make("short_description.{$code}")
                                                ->label(__('Short Description'))
                                                ->columnSpanFull()
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 120px;']),
                                        ]);
                                })->toArray()
                            )
                            ->activeTab($mainLangIndex)
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull(),
                // ─── Shared fields (not translated) ─────────────────────────────────
                Section::make(__('Media'))
                    ->description(__('Images and optional video — same for all languages'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label(__('Images'))
                            ->collection('images')
                            ->multiple()
                            ->maxFiles(5)
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('mission-vision/images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->helperText(__('Up to 5 images, max 5 MB each')),

                        SpatieMediaLibraryFileUpload::make('video')
                            ->label(__('Video'))
                            ->collection('video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->disk('public')
                            ->directory('mission-vision/videos')
                            ->visibility('public')
                            ->maxSize(20480)
                            ->helperText(__('Optional video — max 20 MB')),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->columnSpanFull(),

                // ─── Publication status (shared) ────────────────────────────────────
                Section::make(__('Status'))
                    ->schema([
                        Select::make('status')
                            ->label(__('Publication Status'))
                            ->options([
                                'draft'     => __('Draft'),
                                'published' => __('Published'),
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),


            ]);

    }
}
