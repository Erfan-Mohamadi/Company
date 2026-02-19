<?php

namespace App\Filament\Resources\Dealers\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class DealerForm
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
                                    TextInput::make("dealer_name.{$code}")->label(__('Dealer Name'))->required($isMain)->maxLength(255),
                                    TextInput::make("territory.{$code}")->label(__('Territory'))->maxLength(255),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Dealer Info'))
                ->schema([
                    TextInput::make('dealer_code')->label(__('Dealer Code'))->unique(ignoreRecord: true)->maxLength(50),
                    TextInput::make('website_url')->label(__('Website URL'))->url()->maxLength(500),
                    SpatieMediaLibraryFileUpload::make('logo')->label(__('Logo'))->collection('logo')->image()->imageEditor()->maxSize(2048)->downloadable()->openable()->previewable()->helperText(__('Upload logo (Max: 2 MB)')),
                ])->columns(2)->collapsible(),

            Section::make(__('Contact'))
                ->schema([
                    TextInput::make('contact_person')->label(__('Contact Person'))->maxLength(255),
                    TextInput::make('email')->label(__('Email'))->email()->maxLength(255),
                    TextInput::make('phone')->label(__('Phone'))->tel()->maxLength(50),
                ])->columns(3)->collapsible(),

            Section::make(__('Location'))
                ->schema([
                    TextInput::make('address')->label(__('Address'))->maxLength(500)->columnSpanFull(),
                    TextInput::make('city')->label(__('City'))->maxLength(100),
                    TextInput::make('province')->label(__('Province / State'))->maxLength(100),
                    TextInput::make('country')->label(__('Country'))->maxLength(100),
                    TextInput::make('postal_code')->label(__('Postal Code'))->maxLength(20),
                ])->columns(2)->collapsible(),

            Section::make(__('Contract & Rating'))
                ->schema([
                    DatePicker::make('contract_start_date')->label(__('Contract Start')),
                    DatePicker::make('contract_end_date')->label(__('Contract End'))->after('contract_start_date'),
                    Select::make('rating')->label(__('Rating (1-5)'))
                        ->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐']),
                ])->columns(3)->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published')])->default('draft')->required(),
                ])->columns(2),
        ]);
    }
}
