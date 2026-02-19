<?php

namespace App\Filament\Resources\Partners\Schemas;

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
class PartnerForm
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
                                    TextInput::make("partner_name.{$code}")->label(__('Partner Name'))->required($isMain)->maxLength(255),
                                    Textarea::make("description.{$code}")->label(__('Description'))->rows(3)->columnSpanFull(),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Partnership Info'))
                ->schema([
                    Select::make('partnership_type')->label(__('Partnership Type'))
                        ->options(['technology' => __('Technology'), 'distribution' => __('Distribution'), 'strategic' => __('Strategic'), 'other' => __('Other')]),
                    TextInput::make('website_url')->label(__('Website URL'))->url()->maxLength(500),
                    DatePicker::make('start_date')->label(__('Start Date')),
                    DatePicker::make('end_date')->label(__('End Date'))->after('start_date'),
                ])->columns(2)->collapsible(),

            Section::make(__('Contact'))
                ->schema([
                    TextInput::make('contact_person')->label(__('Contact Person'))->maxLength(255),
                    TextInput::make('email')->label(__('Email'))->email()->maxLength(255),
                    TextInput::make('phone')->label(__('Phone'))->tel()->maxLength(50),
                    SpatieMediaLibraryFileUpload::make('logo')->label(__('Logo'))->collection('logo')->image()->imageEditor()->maxSize(2048)->downloadable()->openable()->previewable()->helperText(__('Upload logo (Max: 2 MB)')),
                ])->columns(2)->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    Toggle::make('featured')->label(__('Featured'))->default(false),
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published')])->default('draft')->required(),
                ])->columns(3),
        ]);
    }
}
