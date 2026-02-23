<?php

namespace App\Filament\Resources\ExportMarkets\Schemas;

use App\Models\Language;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;

class ExportMarketForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages = Language::getAllLanguages();
        $isFarsi   = App::isLocale('fa');

        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        return $schema
            ->components([
                Tabs::make('Export Market Content')
                    ->tabs([
                        // ─── Tab 1: Translations ────────────────────────────────
                        Tabs\Tab::make(__('Translations'))
                            ->icon('heroicon-o-language')
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
                                                    TextInput::make("country_name.{$code}")
                                                        ->label(__('Country Name'))
                                                        ->required($isMain)
                                                        ->maxLength(100),

                                                    TextInput::make("region.{$code}")
                                                        ->label(__('Region'))
                                                        ->maxLength(100),
                                                ]);
                                        })->toArray()
                                    )
                                    ->activeTab($mainLangIndex)
                                    ->contained(false)
                                    ->columnSpanFull(),
                            ]),

                        // ─── Tab 2: Market Data ─────────────────────────────────
                        Tabs\Tab::make(__('Market Data'))
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                TextInput::make('export_volume')
                                    ->label(__('Export Volume (units)'))
                                    ->numeric(),

                                TextInput::make('export_value')
                                    ->label(__('Export Value (USD)'))
                                    ->numeric(),

                                TextInput::make('distributors_count')
                                    ->label(__('Distributors'))
                                    ->numeric()
                                    ->default(0),

                                DatePicker::make('start_year')
                                    ->label(__('Start Year'))
                                    ->format('Y')
                                    ->displayFormat('Y')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->helperText($isFarsi
                                        ? __('Select the year in Jalali calendar')
                                        : __('Select the year')
                                    )
                                    ->when($isFarsi, fn (DatePicker $picker) => $picker->jalali()),

                                TextInput::make('growth_rate')
                                    ->label(__('Growth Rate (%)'))
                                    ->numeric(),

                                Repeater::make('main_products')
                                    ->label(__('Main Products'))
                                    ->schema([
                                        TextInput::make('product')
                                            ->label(__('Product'))
                                            ->maxLength(100),
                                    ])
                                    ->addActionLabel(__('Add Product'))
                                    ->columnSpanFull(),

                                Select::make('continent')
                                    ->label(__('Continent'))
                                    ->options([
                                        'Asia'          => __('Asia'),
                                        'Europe'        => __('Europe'),
                                        'Africa'        => __('Africa'),
                                        'North America' => __('North America'),
                                        'South America' => __('South America'),
                                        'Oceania'       => __('Oceania'),
                                        'Antarctica'    => __('Antarctica'),
                                    ]),
                            ])
                            ->columns(2),

                        // ─── Tab 3: Settings ────────────────────────────────────
                        Tabs\Tab::make(__('Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
