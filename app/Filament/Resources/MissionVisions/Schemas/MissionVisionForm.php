<?php

namespace App\Filament\Resources\MissionVisions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MissionVisionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Content'))
                    ->description(__('Main mission and vision statements'))
                    ->schema([
                        TextInput::make('header')
                            ->label(__('Header'))
                            ->maxLength(255)
                            ->helperText(__('Optional main title for this section')),

                        TextInput::make('vision_title')
                            ->label(__('Vision Title'))
                            ->maxLength(255),

                        RichEditor::make('vision_text')
                            ->label(__('Vision Text'))
                            ->columnSpanFull()
                            ->resizableImages()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'blockquote', 'codeBlock',
                                'alignStart', 'alignCenter', 'alignEnd',
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 160px;']),

                        TextInput::make('mission_title')
                            ->label(__('Mission Title'))
                            ->maxLength(255),

                        RichEditor::make('mission_text')
                            ->label(__('Mission Text'))
                            ->columnSpanFull()
                            ->resizableImages()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'blockquote', 'codeBlock',
                                'alignStart', 'alignCenter', 'alignEnd',
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 160px;']),

                        Textarea::make('short_description')
                            ->label(__('Short Description'))
                            ->rows(3),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->collapsed(),

                Section::make(__('Media'))
                    ->description(__('Images and optional video'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->label(__('Images'))
                            ->collection('images')
                            ->multiple()
                            ->maxFiles(5)
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('mission-vision/images')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->helperText(__('Up to 5 images, max 5 MB each')),

                        SpatieMediaLibraryFileUpload::make('video')
                            ->label(__('Video'))
                            ->collection('video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm'])
                            ->disk('public')
                            ->directory('mission-vision/videos')
                            ->visibility('public')
                            ->maxSize(20480)
                            ->helperText(__('Optional video - max 20 MB')),
                    ])
                    ->collapsed(),
            ]);
    }
}
