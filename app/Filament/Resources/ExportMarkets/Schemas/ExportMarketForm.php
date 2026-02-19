<?php

namespace App\Filament\Resources\ExportMarkets\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class ExportMarketForm
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
                                    TextInput::make("country_name.{$code}")->label(__('Country Name'))->required($isMain)->maxLength(100),
                                    TextInput::make("region.{$code}")->label(__('Region'))->maxLength(100),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Country Details'))
                ->schema([
                    TextInput::make('country_code')->label(__('Country Code'))->maxLength(10),
                    Select::make('continent')->label(__('Continent'))
                        ->options([
                            'Asia' => __('Asia'), 'Europe' => __('Europe'), 'Africa' => __('Africa'),
                            'North America' => __('North America'), 'South America' => __('South America'),
                            'Oceania' => __('Oceania'), 'Antarctica' => __('Antarctica'),
                        ]),
                ])->columns(2)->collapsible(),

            Section::make(__('Market Data'))
                ->schema([
                    TextInput::make('export_volume')->label(__('Export Volume (units)'))->numeric(),
                    TextInput::make('export_value')->label(__('Export Value (USD)'))->numeric(),
                    TextInput::make('distributors_count')->label(__('Distributors'))->numeric()->default(0),
                    TextInput::make('start_year')->label(__('Start Year'))->numeric()->minValue(1900)->maxValue(date('Y')),
                    TextInput::make('growth_rate')->label(__('Growth Rate (%)'))->numeric(),

                    Repeater::make('main_products')->label(__('Main Products'))
                        ->schema([TextInput::make('product')->label(__('Product'))->maxLength(100)])
                        ->addActionLabel(__('Add Product'))
                        ->columnSpanFull(),
                ])->columns(2)->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published')])->default('draft')->required(),
                ])->columns(2),
        ]);
    }
}
