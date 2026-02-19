<?php

namespace App\Filament\Resources\OrganizationalCharts\Schemas;

use App\Models\Language;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class OrganizationalChartsForm
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
                                            Textarea::make("description.{$code}")
                                                ->label(__('Description'))
                                                ->rows(4)
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

                Section::make(__('Chart Diagram'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('diagram')
                            ->label(__('Org Chart Image'))
                            ->collection('diagram')
                            ->helperText(__('Upload a large image of the org chart. Accepts PNG, JPG, SVG.'))
                            ->image()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('Settings'))
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft'     => __('Draft'),
                                'published' => __('Published'),
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
            ]);
    }
}
