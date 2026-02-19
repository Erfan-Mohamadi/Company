<?php

namespace App\Filament\Resources\RepresentationLetters\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class RepresentationLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema->components([
            Section::make(__('Translations'))
                ->schema([
                    Tabs::make('Translations')
                        ->tabs($languages->map(function ($language) {
                            $code   = $language->name;
                            $isMain = $code === Language::MAIN_LANG;
                            return Tabs\Tab::make($language->label)
                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                ->badge($isMain ? __('Main') : null)
                                ->schema([
                                    TextInput::make("header.{$code}")->label(__('Header'))->required($isMain)->maxLength(255),
                                    TextInput::make("company_name.{$code}")->label(__('Company Name'))->maxLength(255),
                                    TextInput::make("representative_name.{$code}")->label(__('Representative Name'))->maxLength(255),
                                    TextInput::make("territory.{$code}")->label(__('Territory'))->maxLength(100),
                                    Textarea::make("description.{$code}")->label(__('Description'))->rows(3)->columnSpanFull(),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Dates'))
                ->schema([
                    DatePicker::make('issue_date')->label(__('Issue Date')),
                    DatePicker::make('expiry_date')->label(__('Expiry Date'))->after('issue_date'),
                ])->columns(2)->collapsible(),

            Section::make(__('Document'))
                ->schema([
                    SpatieMediaLibraryFileUpload::make('document_file')->label(__('Document PDF'))->collection('document_file')->acceptedFileTypes(['application/pdf'])->maxSize(10240)->downloadable()->openable()->columnSpanFull()->helperText(__('Upload document PDF (Max: 10 MB)')),
                ])->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published')])->default('draft')->required(),
                ])->columns(2),
        ]);

    }
}
