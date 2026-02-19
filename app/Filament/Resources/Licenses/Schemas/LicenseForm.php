<?php

namespace App\Filament\Resources\Licenses\Schemas;


use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class LicenseForm
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
                                            TextInput::make("license_name.{$code}")
                                                ->label(__('License Name'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            TextInput::make("issuing_authority.{$code}")
                                                ->label(__('Issuing Authority'))
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

                Section::make(__('License Details'))
                    ->schema([
                        TextInput::make('license_number')
                            ->label(__('License Number'))
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Select::make('license_type')
                            ->label(__('License Type'))
                            ->options([
                                'trade'        => __('Trade'),
                                'professional' => __('Professional'),
                                'operating'    => __('Operating'),
                                'import'       => __('Import / Export'),
                                'other'        => __('Other'),
                            ]),

                        DatePicker::make('issue_date')
                            ->label(__('Issue Date')),

                        DatePicker::make('expiry_date')
                            ->label(__('Expiry Date'))
                            ->after('issue_date'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('File'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('license_file')
                            ->label(__('License PDF'))
                            ->collection('license_file')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull()
                            ->helperText(__('Upload license PDF (Maximum size: 10 MB)')),
                    ])
                    ->collapsible(),

                Section::make(__('Display Settings'))
                    ->schema([
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
                    ->columns(2),
            ]);
    }
}
