<?php

namespace App\Filament\Resources\Accreditations\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class AccreditationForm
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
                                    TextInput::make("organization_name.{$code}")->label(__('Organization Name'))->required($isMain)->maxLength(255),
                                    TextInput::make("accreditation_type.{$code}")->label(__('Accreditation Type'))->maxLength(100),
                                    Textarea::make("description.{$code}")->label(__('Description'))->rows(3)->columnSpanFull(),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)
                        ->columnSpanFull()
                        ->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Membership Details'))
                ->schema([
                    TextInput::make('membership_number')->label(__('Membership Number'))->maxLength(100),
                    DatePicker::make('member_since')->label(__('Member Since')),
                    DatePicker::make('start_date')->label(__('Start Date')),
                    DatePicker::make('end_date')->label(__('End Date'))->after('start_date'),
                    TextInput::make('verification_url')->label(__('Verification URL'))->url()->maxLength(500)->columnSpanFull(),
                ])->columns(2)->collapsible(),

            Section::make(__('Files'))
                ->schema([
                    SpatieMediaLibraryFileUpload::make('logo')->label(__('Logo'))->collection('logo')->image()->imageEditor()->maxSize(2048)->downloadable()->openable()->previewable()->helperText(__('Upload logo (Max: 2 MB)')),
                    SpatieMediaLibraryFileUpload::make('certificate')->label(__('Certificate PDF'))->collection('certificate')->acceptedFileTypes(['application/pdf'])->maxSize(10240)->downloadable()->openable()->helperText(__('Upload certificate PDF (Max: 10 MB)')),
                ])->columns(2)->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))
                        ->options(['draft' => __('Draft'), 'published' => __('Published')])
                        ->default('draft')->required(),
                ])->columns(2),
        ]);
    }
}
