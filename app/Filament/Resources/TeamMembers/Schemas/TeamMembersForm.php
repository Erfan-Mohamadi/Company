<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Models\Language;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class TeamMembersForm
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
                                                ->label(__('Name'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            TextInput::make("position.{$code}")
                                                ->label(__('Position'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            Textarea::make("bio.{$code}")
                                                ->label(__('Bio'))
                                                ->maxLength(500)
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

                        TextInput::make('facebook_url')
                            ->label(__('Facebook URL'))
                            ->url()
                            ->maxLength(500),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make(__('Skills'))
                    ->schema([
                        Repeater::make('skills')
                            ->label(__('Skills'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Skill'))
                                    ->required()
                                    ->maxLength(100),

                                Select::make('level')
                                    ->label(__('Proficiency'))
                                    ->options([
                                        'beginner'     => __('Beginner'),
                                        'intermediate' => __('Intermediate'),
                                        'advanced'     => __('Advanced'),
                                        'expert'       => __('Expert'),
                                    ])
                                    ->default('intermediate'),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->addActionLabel(__('Add Skill'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Section::make(__('Photo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('image')
                            ->label(__('Photo'))
                            ->collection('image')
                            ->image()
                            ->imageEditor()
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
