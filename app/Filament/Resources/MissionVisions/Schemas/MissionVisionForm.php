<?php

namespace App\Filament\Resources\MissionVisions\Schemas;

use App\Models\Language;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class MissionVisionForm
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
                Tabs::make('Mission Vision Content')
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

                                                    // ── Page Header ──────────────────────────────────────
                                                    TextInput::make("header.{$code}")
                                                        ->label(__('Header'))
                                                        ->required($isMain)
                                                        ->maxLength(255)
                                                        ->columnSpanFull(),

                                                    // ── Short Description ────────────────────────────────
                                                    TextInput::make("short_description.{$code}")
                                                        ->label(__('Short Description'))
                                                        ->maxLength(500)
                                                        ->columnSpanFull()
                                                        ->helperText(__('A brief teaser or summary shown in listings')),

                                                    // ── Vision ───────────────────────────────────────────
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
                                                        ->extraInputAttributes(['style' => 'min-height: 180px;']),

                                                    // ── Mission ──────────────────────────────────────────
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
                                                        ->extraInputAttributes(['style' => 'min-height: 180px;']),

                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Media ────────────────────────────────────────
                        Tabs\Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // Multiple images (collection 'images', no singleFile)
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->label(__('Images'))
                                    ->collection('images')
                                    ->image()
                                    ->imageEditor()
                                    ->multiple()
                                    ->maxFiles(5)
                                    ->reorderable()
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->columnSpanFull()
                                    ->helperText(__('Upload up to 5 images (Max: 5 MB each)')),

                                // Optional video file upload (collection 'video', singleFile)
                                SpatieMediaLibraryFileUpload::make('video')
                                    ->label(__('Video File'))
                                    ->collection('video')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                                    ->maxSize(102400) // 100 MB
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull()
                                    ->helperText(__('Optional: upload a video file (Max: 100 MB). Use the URL field below for YouTube/Vimeo links instead.')),

                                // External video URL
                                TextInput::make('video_url')
                                    ->label(__('External Video URL'))
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull()
                                    ->placeholder('https://www.youtube.com/watch?v=...')
                                    ->helperText(__('YouTube, Vimeo, or any direct video URL')),
                            ]),

                        // ─── Tab 3: Settings ─────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Select::make('status')
                                    ->label(__('Status'))
                                    ->options([
                                        'draft'     => __('Draft'),
                                        'published' => __('Published'),
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
