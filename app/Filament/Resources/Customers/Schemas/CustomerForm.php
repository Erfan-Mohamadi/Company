<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Language;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class CustomerForm
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
                                    TextInput::make("name.{$code}")->label(__('Customer Name'))->required($isMain)->maxLength(255),
                                    Textarea::make("project_description.{$code}")->label(__('Project Description'))->rows(3)->columnSpanFull(),
                                    Textarea::make("testimonial_text.{$code}")->label(__('Testimonial'))->rows(3)->columnSpanFull(),
                                    TextInput::make("author_name.{$code}")->label(__('Testimonial Author')),
                                    TextInput::make("author_position.{$code}")->label(__('Author Position')),
                                ]);
                        })->toArray())
                        ->activeTab($mainLangIndex)->columnSpanFull()->contained(false),
                ])->collapsible()->collapsed(false),

            Section::make(__('Customer Info'))
                ->schema([
                    TextInput::make('industry')->label(__('Industry'))->maxLength(100),
                    TextInput::make('country')->label(__('Country'))->maxLength(100),
                    TextInput::make('website_url')->label(__('Website URL'))->url()->maxLength(500),
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
