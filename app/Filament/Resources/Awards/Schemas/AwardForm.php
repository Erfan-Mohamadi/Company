<?php

namespace App\Filament\Resources\Awards\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class AwardForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'link'],
            ['bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];

        return $schema
            ->components([
                Section::make(__('Translations'))
                    ->description(__('Provide content in each language'))
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs(
                                $languages->map(function ($language) use ($toolbarButtons) {
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

                                            TextInput::make("awarding_body.{$code}")
                                                ->label(__('Awarding Body'))
                                                ->maxLength(255),

                                            TextInput::make("category.{$code}")
                                                ->label(__('Category'))
                                                ->maxLength(100),

                                            RichEditor::make("description.{$code}")
                                                ->label(__('Description'))
                                                ->columnSpanFull()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->extraInputAttributes(['style' => 'min-height: 160px;']),
                                        ]);
                                })->toArray()
                            )
                            ->activeTab($mainLangIndex)
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make(__('Award Details'))
                    ->schema([
                        DatePicker::make('award_date')
                            ->label(__('Award Date')),
                    ])
                    ->collapsible(),

                Section::make(__('Media'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Award Image'))
                            ->collection('image')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText(__('Upload award image (Maximum size: 5 MB)')),

                        SpatieMediaLibraryFileUpload::make('certificate_file')
                            ->label(__('Certificate PDF'))
                            ->collection('certificate_file')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->helperText(__('Upload certificate PDF (Maximum size: 10 MB)')),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Display Settings'))
                    ->schema([
                        Toggle::make('featured')
                            ->label(__('Featured'))
                            ->default(false),

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
            ]);
    }
}
