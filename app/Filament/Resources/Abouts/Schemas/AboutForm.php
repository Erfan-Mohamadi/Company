<?php

namespace App\Filament\Resources\Abouts\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AboutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Company Overview'))
                    ->description(__('Basic company information and founding details'))
                    ->schema([
                        TextInput::make('header')
                            ->label(__('Company Header'))
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Main title for the about page')),

                        TextInput::make('founder_name')
                            ->label(__('Founder Name'))
                            ->maxLength(255)
                            ->helperText(__('Name of the company founder')),

                        TextInput::make('founded_year')
                            ->label(__('Founded Year (Shamsi/Jalali)'))
                            ->numeric()
                            ->minValue(1200)
                            ->maxValue(1500)
                            ->step(1)
                            ->placeholder(__('e.g : 1402'))
                            ->suffix(__('Shamsi'))
                            ->helperText(__('Enter year in Shamsi/Jalali calendar (e.g., 1402)'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state && is_numeric($state)) {
                                    // Convert Jalali to Gregorian for display
                                    $gregorianYear = intval($state) + 621;
                                    $set('founded_year_gregorian', $gregorianYear);
                                }
                            }),

                        TextInput::make('founded_year_gregorian')
                            ->label(__('Founded Year (Gregorian)'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('Automatically calculated'))
                            ->suffix(__('Gregorian'))
                            ->helperText(__('This is automatically converted from Shamsi year')),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'published' => __('Published'),
                            ])
                            ->default('draft')
                            ->required(),

                        RichEditor::make('description')
                            ->label(__('Company Description'))
                            ->columnSpan('full')
                            ->resizableImages()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->textColors([])
                            ->customTextColors()
                            ->floatingToolbars([
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
                                ]
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 140px;'])
                            ->helperText(__('Detailed description of your company')),

                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Mission & Vision'))
                    ->description(__('Company mission, vision, and core values'))
                    ->schema([
                        RichEditor::make('mission')
                            ->label(__('Mission'))
                            ->columnSpan('full')
                            ->resizableImages()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->textColors([])
                            ->customTextColors()
                            ->floatingToolbars([
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
                                ]
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 140px;'])
                            ->helperText(__('Company mission statement')),

                        RichEditor::make('vision')
                            ->label(__('Vision'))
                            ->columnSpan('full')
                            ->resizableImages()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->textColors([])
                            ->customTextColors()
                            ->floatingToolbars([
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
                                ]
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 140px;'])
                            ->helperText(__('Company vision statement')),

                        Repeater::make('core_values')
                            ->label(__('Core Values'))
                            ->schema([
                                TextInput::make('value_name')
                                    ->label(__('Value Name'))
                                    ->required()
                                    ->maxLength(255),

                                RichEditor::make('description')
                                    ->label(__('Description'))
                                    ->resizableImages()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                        ['bulletList', 'orderedList'],
                                        ['undo', 'redo'],
                                    ])
                                    ->textColors([])
                                    ->customTextColors()
                                    ->extraInputAttributes(['style' => 'min-height: 100px;']),
                            ])
                            ->columnSpan('full')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['value_name'] ?? null)
                            ->addActionLabel(__('Add Core Value'))
                            ->helperText(__('Define your company core values')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Statistics'))
                    ->description(__('Company statistics and achievements'))
                    ->schema([
                        TextInput::make('employees_count')
                            ->label(__('Employees Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->helperText(__('Total number of employees')),

                        TextInput::make('locations_count')
                            ->label(__('Locations Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->helperText(__('Number of office locations')),

                        TextInput::make('clients_count')
                            ->label(__('Clients Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->helperText(__('Total number of clients served')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Media'))
                    ->description(__('Images, videos, and founder information'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label(__('Company Images'))
                            ->collection('images')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->reorderable()
                            ->appendFiles()
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpan('full')
                            ->helperText(__('Upload company images (Maximum size: 5 MB per image, up to 10 images)')),

                        SpatieMediaLibraryFileUpload::make('video')
                            ->label(__('Company Video'))
                            ->collection('video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(20480)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpan('full')
                            ->helperText(__('Accepted formats: MP4, WebM, OGG - Maximum size: 20 MB')),

                        SpatieMediaLibraryFileUpload::make('founder_image')
                            ->label(__('Founder Image'))
                            ->collection('founder_image')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpan('full')
                            ->helperText(__('Upload founder photo (Maximum size: 5 MB)')),

                        RichEditor::make('founder_message')
                            ->label(__('Founder Message'))
                            ->columnSpan('full')
                            ->resizableImages()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->textColors([])
                            ->customTextColors()
                            ->floatingToolbars([
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
                                ]
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 140px;'])
                            ->helperText(__('Message from the founder')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 1,
                    ])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Additional Information'))
                    ->description(__('Extra metadata and custom fields'))
                    ->schema([
                        KeyValue::make('extra')
                            ->label(__('Extra Data'))
                            ->keyLabel(__('Field Name'))
                            ->valueLabel(__('Field Value'))
                            ->addActionLabel(__('Add Field'))
                            ->reorderable()
                            ->columnSpan('full')
                            ->helperText(__('Add any additional custom fields as key-value pairs (e.g., "website" → "https://example.com")')),
                    ])
                    ->columnSpan(2)
                    ->collapsed(),
            ]);
    }
}
