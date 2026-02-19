<?php

namespace App\Filament\Resources\Departments\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class DepartmentsForm
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
                                            TextInput::make("name.{$code}")
                                                ->label(__('Department Name'))
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

                Section::make(__('Leadership'))
                    ->schema([
                        TextInput::make('head_name')
                            ->label(__('Department Head Name'))
                            ->maxLength(255),

                        TextInput::make('head_email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),

                        TextInput::make('head_phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('location')
                            ->label(__('Location'))
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Visual'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Department Photo / Banner'))
                            ->collection('image')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->helperText(__('Upload department banner (Maximum size: 5 MB)')),
                    ])
                    ->collapsible(),

                Section::make(__('Settings'))
                    ->schema([
                        TextInput::make('employee_count')
                            ->label(__('Employee Count'))
                            ->numeric()
                            ->default(0),

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
