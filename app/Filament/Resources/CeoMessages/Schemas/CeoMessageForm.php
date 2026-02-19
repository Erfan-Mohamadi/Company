<?php

namespace App\Filament\Resources\CeoMessages\Schemas;


use App\Models\Language;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
class CeoMessageForm
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
                                            TextInput::make("title.{$code}")
                                                ->label(__('Title'))
                                                ->required($isMain)
                                                ->maxLength(255),

                                            TextInput::make("ceo_name.{$code}")
                                                ->label(__('CEO Name'))
                                                ->maxLength(255),

                                            TextInput::make("ceo_position.{$code}")
                                                ->label(__('CEO Position'))
                                                ->maxLength(255),

                                            RichEditor::make("message_text.{$code}")
                                                ->label(__('Message'))
                                                ->columnSpanFull()
                                                ->toolbarButtons($toolbarButtons)
                                                ->textColors([])
                                                ->customTextColors()
                                                ->floatingToolbars($floatingToolbars)
                                                ->extraInputAttributes(['style' => 'min-height: 250px;']),
                                        ]);
                                })->toArray()
                            )
                            ->activeTab($mainLangIndex)
                            ->columnSpanFull()
                            ->contained(false),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make(__('Executive Media'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('ceo_image')
                            ->label(__('CEO Photo'))
                            ->collection('ceo_image')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText(__('Upload CEO photo (Maximum size: 5 MB)')),

                        SpatieMediaLibraryFileUpload::make('ceo_signature')
                            ->label(__('CEO Signature (transparent PNG)'))
                            ->collection('ceo_signature')
                            ->image()
                            ->maxSize(2048)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->helperText(__('Upload signature as transparent PNG (Maximum size: 2 MB)')),

                        TextInput::make('video_url')
                            ->label(__('Video Message URL'))
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
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
