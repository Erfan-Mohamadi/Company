<?php

namespace App\Filament\Resources\HistoryMilestones\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class HistoryMilestonesForm
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
            'paragraph' => ['h2', 'h3', 'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
            'heading'   => ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify', 'bold', 'italic', 'underline', 'strike'],
            'table'     => ['tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn', 'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow', 'tableMergeCells', 'tableSplitCell', 'tableToggleHeaderRow', 'tableToggleHeaderCell', 'tableDelete'],
            'attachFiles' => ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
        ];

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Milestone Content')
                    ->tabs([
                        // ─── Tab 1: Milestone Details ───────────────────────────
                        Tabs\Tab::make(__('Milestone Details'))
                            ->icon('heroicon-o-calendar-days')
                            ->schema([
                                DatePicker::make('year')
                                    ->label(__('Year'))
                                    ->format('Y')
                                    ->displayFormat('Y')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi ? __('Select the year in Jalali calendar') : __('Select the year'))
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                TextInput::make('event_type')
                                    ->label(__('Event Type'))
                                    ->placeholder(__('e.g., Founding, Expansion, Award'))
                                    ->maxLength(255),

                                SpatieMediaLibraryFileUpload::make('image')
                                    ->label(__('Milestone Image'))
                                    ->collection('milestone_images')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->previewable()
                                    ->helperText(__('Optional image for this milestone (Max 5 MB)')),

                                TextInput::make('order')
                                    ->label(__('Display Order'))
                                    ->numeric()
                                    ->default(999)
                                    ->helperText(__('Lower numbers appear first in timeline')),

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

                        // ─── Tab 2: Translations ────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
                            ->schema([
                                Tabs::make('translations')
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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
