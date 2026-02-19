<?php

namespace App\Filament\Resources\LeadershipTeams\Schemas;

use App\Models\Department;
use App\Models\Language;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class LeadershipTeamForm
{
    public static function configure(Schema $schema): Schema
    {
        $languages     = Language::getAllLanguages();
        $mainLangIndex = $languages->search(fn ($lang) => $lang->name === Language::MAIN_LANG) + 1 ?: 1;

        $toolbarButtons = [
            ['bold', 'italic', 'underline', 'strike', 'link'],
            ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'bulletList', 'orderedList'],
            ['undo', 'redo'],
        ];

        $floatingToolbars = [
            'paragraph' => ['bold', 'italic', 'underline', 'strike', 'alignStart', 'alignCenter', 'alignEnd'],
            'heading'   => ['h2', 'h3', 'bold', 'italic'],
        ];

        return $schema
            ->components([
                Section::make(__('Translations'))
                    ->description(__('Provide content in each language'))
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs(
                                $languages->map(function ($language) use ($toolbarButtons, $floatingToolbars) {
                                    $code   = $language->name;
                                    $isMain = $code === Language::MAIN_LANG;

                                    return Tabs\Tab::make($language->label)
                                        ->icon($language->is_rtl ? 'heroicon-o-arrow-right' : 'heroicon-o-arrow-left')
                                        ->badge($isMain ? __('Main') : null)
                                        ->schema([
                                            TextInput::make("name.{$code}")
                                                ->label(__('Name'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            TextInput::make("position.{$code}")
                                                ->label(__('Position / Title'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            Textarea::make("short_bio.{$code}")
                                                ->label(__('Short Bio'))
                                                ->maxLength(300)
                                                ->rows(3)
                                                ->columnSpanFull(),

                                            RichEditor::make("long_bio.{$code}")
                                                ->label(__('Long Bio'))
                                                ->columnSpanFull()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 180px;']),
                                        ]);
                                })->toArray()
                            )
                            ->activeTab($mainLangIndex)
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make(__('Contact & Social'))
                    ->schema([
                        TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('linkedin_url')
                            ->label(__('LinkedIn URL'))
                            ->url()
                            ->maxLength(500),

                        TextInput::make('twitter_url')
                            ->label(__('Twitter / X URL'))
                            ->url()
                            ->maxLength(500),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Achievements'))
                    ->schema([
                        Repeater::make('achievements')
                            ->label(__('Achievements'))
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('Title'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('year')
                                    ->label(__('Year'))
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(date('Y')),

                                Textarea::make('description')
                                    ->label(__('Description'))
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addActionLabel(__('Add Achievement'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make(__('Profile Photo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Photo'))
                            ->collection('image')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->helperText(__('Upload profile photo (Maximum size: 5 MB)')),
                    ])
                    ->collapsible(),

                Section::make(__('Display Settings'))
                    ->schema([
                        Select::make('department_id')
                            ->label(__('Department'))
                            ->relationship('department', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->getTranslation('name', app()->getLocale()))
                            ->searchable()
                            ->preload(),

                        TextInput::make('order')
                            ->label(__('Display Order'))
                            ->numeric()
                            ->default(0),

                        Toggle::make('featured')
                            ->label(__('Featured'))
                            ->default(false),

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
