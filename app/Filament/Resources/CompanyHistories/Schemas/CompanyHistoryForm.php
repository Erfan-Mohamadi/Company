<?php

namespace App\Filament\Resources\CompanyHistories\Schemas;

use App\Models\Language;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class CompanyHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'link'],
            ['h2', 'h3', 'bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];

        return $schema->components([
            Section::make(__('Translations'))
                ->schema([
                    Tabs::make('Translations')
                        ->tabs($languages->map(function ($language) use ($toolbarButtons) {
                            $code   = $language->name;
                            $isMain = $code === Language::MAIN_LANG;
                            return Tabs\Tab::make($language->label)
                                ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                ->badge($isMain ? __('Main') : null)
                                ->schema([
                                    TextInput::make("title.{$code}")->label(__('Title'))->required($isMain)->maxLength(255),
                                    RichEditor::make("description.{$code}")->label(__('Description'))
                                        ->columnSpanFull()->toolbarButtons($toolbarButtons)->textColors([])->customTextColors()
                                        ->extraInputAttributes(['style' => 'min-height: 160px;']),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Timeline'))
                ->schema([
                    TextInput::make('year')->label(__('Year'))->numeric()->minValue(1800)->maxValue(date('Y') + 10)->required(),
                    Select::make('month')->label(__('Month (optional)'))
                        ->options([
                            1 => __('January'), 2 => __('February'), 3 => __('March'), 4 => __('April'),
                            5 => __('May'), 6 => __('June'), 7 => __('July'), 8 => __('August'),
                            9 => __('September'), 10 => __('October'), 11 => __('November'), 12 => __('December'),
                        ])->placeholder(__('— Any month —')),
                    Select::make('achievement_type')->label(__('Achievement Type'))
                        ->options([
                            'founding'       => __('Founding'),
                            'product_launch' => __('Product Launch'),
                            'expansion'      => __('Expansion'),
                            'award'          => __('Award'),
                            'partnership'    => __('Partnership'),
                            'other'          => __('Other'),
                        ]),
                ])->columns(3)->collapsible(),

            Section::make(__('Visual'))
                ->schema([
                    TextInput::make('icon')->label(__('Icon'))->helperText(__('e.g. heroicon-o-flag'))->maxLength(100),
                    SpatieMediaLibraryFileUpload::make('image')->label(__('Image'))->collection('image')->image()->imageEditor()->maxSize(5120)->downloadable()->openable()->previewable()->columnSpanFull()->helperText(__('Upload image (Max: 5 MB)')),
                ])->columns(2)->collapsible(),

            Section::make(__('Settings'))
                ->schema([
                    TextInput::make('order')->label(__('Display Order'))->numeric()->default(0),
                    Select::make('status')->label(__('Status'))->options(['draft' => __('Draft'), 'published' => __('Published')])->default('draft')->required(),
                ])->columns(2),
        ]);
    }
}
