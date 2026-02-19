<?php

namespace App\Filament\Resources\Certifications\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Section::make(__('Translations'))
                    ->description(__('Provide content in each language'))
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs(
                                $languages->map(function ($language) {
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

                                            Textarea::make("description.{$code}")
                                                ->label(__('Description'))
                                                ->rows(3)
                                                ->columnSpanFull(),
                                        ]);
                                })->toArray()
                            )
                            ->activeTab($mainLangIndex)
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make(__('Certificate Details'))
                    ->schema([
                        TextInput::make('certification_body')
                            ->label(__('Certification Body'))
                            ->maxLength(255),

                        TextInput::make('certificate_number')
                            ->label(__('Certificate Number'))
                            ->maxLength(100),

                        DatePicker::make('issue_date')
                            ->label(__('Issue Date')),

                        DatePicker::make('expiry_date')
                            ->label(__('Expiry Date'))
                            ->after('issue_date'),

                        TextInput::make('verification_url')
                            ->label(__('Verification URL'))
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Files'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('certificate_image')
                            ->label(__('Certificate Image'))
                            ->collection('certificate_image')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText(__('Upload certificate image (Maximum size: 5 MB)')),

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
