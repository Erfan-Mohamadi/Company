<?php

namespace App\Filament\Resources\Abouts\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class AboutForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi   = App::isLocale('fa');

        // ─── Shared RichEditor settings (exactly like your original) ─────────────
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

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                // ─── Non-translatable fields ─────────────────────────────────────
                Section::make(__('Company Overview'))
                    ->description(__('Basic company information and founding details'))
                    ->schema([
                        DatePicker::make('founded_date')
                            ->label($isFarsi ? __('Founded Date (Jalali)') : __('Founded Date (Gregorian)'))
                            ->format('Y-m-d')
                            ->displayFormat('Y/m/d')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->helperText($isFarsi
                                ? __('Select the founding date in Jalali calendar')
                                : __('Select the founding date in Gregorian calendar')
                            )
                            ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft'     => __('Draft'),
                                'published' => __('Published'),
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(['default' => 1, 'md' => 3])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Statistics'))
                    ->description(__('Company statistics and achievements'))
                    ->schema([
                        TextInput::make('employees_count')
                            ->label(__('Employees Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1),

                        TextInput::make('locations_count')
                            ->label(__('Locations Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1),

                        TextInput::make('clients_count')
                            ->label(__('Clients Count'))
                            ->numeric()
                            ->minValue(0)
                            ->step(1),
                    ])
                    ->columns(['default' => 1, 'md' => 3])
                    ->columnSpan(2)
                    ->collapsed(),

                Section::make(__('Media'))
                    ->description(__('Images, videos, and founder photo'))
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
                    ])
                    ->columnSpan(2)
                    ->collapsed(),

                // ─── Translations (exactly like TranslationKey style) ─────────────
                Section::make(__('Translations'))
                    ->description(__('Provide translations for each language'))
                    ->schema([
                        Tabs::make('Translations')
                            ->label(__('Translations'))
                            ->tabs(
                                $languages->map(function ($language) use ($toolbarButtons, $floatingToolbars) {
                                    $code    = $language->name;
                                    $isMain  = $code === Language::MAIN_LANG;

                                    return Tabs\Tab::make($language->label)
                                        ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                        ->badge($isMain ? __('Main') : null)
                                        ->schema([
                                            TextInput::make("header.{$code}")
                                                ->label(__('Company Header'))
                                                ->required($isMain)
                                                ->maxLength(255)
                                                ->helperText(__('Main title for the about page')),

                                            TextInput::make("founder_name.{$code}")
                                                ->label(__('Founder Name'))
                                                ->maxLength(255)
                                                ->helperText(__('Name of the company founder')),

                                            RichEditor::make("description.{$code}")
                                                ->label(__('Company Description'))
                                                ->columnSpan('full')
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 140px;'])
                                                ->helperText(__('Detailed description of your company')),

                                            RichEditor::make("mission.{$code}")
                                                ->label(__('Mission'))
                                                ->columnSpan('full')
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 140px;'])
                                                ->helperText(__('Company mission statement')),

                                            RichEditor::make("vision.{$code}")
                                                ->label(__('Vision'))
                                                ->columnSpan('full')
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 140px;'])
                                                ->helperText(__('Company vision statement')),

                                            Repeater::make("core_values.{$code}")
                                                ->label(__('Core Values'))
                                                ->schema([
                                                    TextInput::make('value_name')
                                                        ->label(__('Value Name'))
                                                        ->required()
                                                        ->maxLength(255),

                                                    RichEditor::make('description')
                                                        ->label(__('Description'))
                                                        ->resizableImages()
                                                        ->toolbarButtons($toolbarButtons)
                                                        ->textColors([])
                                                        ->customTextColors()
                                                        ->floatingToolbars($floatingToolbars)
                                                        ->extraInputAttributes(['style' => 'min-height: 100px;']),
                                                ])
                                                ->columnSpan('full')
                                                ->collapsible()
                                                ->itemLabel(fn (array $state): ?string => $state['value_name'] ?? null)
                                                ->addActionLabel(__('Add Core Value'))
                                                ->helperText(__('Define your company core values')),

                                            RichEditor::make("founder_message.{$code}")
                                                ->label(__('Founder Message'))
                                                ->columnSpan('full')
                                                ->resizableImages()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 140px;'])
                                                ->helperText(__('Message from the founder')),

                                            KeyValue::make("extra.{$code}")
                                                ->label(__('Extra Data'))
                                                ->keyLabel(__('Field Name'))
                                                ->valueLabel(__('Field Value'))
                                                ->addActionLabel(__('Add Field'))
                                                ->reorderable()
                                                ->columnSpan('full')
                                                ->helperText(__('Add any additional custom fields as key-value pairs')),
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
            ]);
    }
}
