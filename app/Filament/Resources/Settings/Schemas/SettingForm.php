<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->label(__('Group'))
                    ->options(collect(Setting::getAllGroups())->mapWithKeys(fn($v, $k) => [$k => $v['title'] ?? $k]))
                    ->required(),

                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),

                TextInput::make('label')
                    ->label(__('Label'))
                    ->required(),

                Select::make('type')
                    ->label(__('Type'))
                    ->options(Setting::getAllTypes())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Reset value when type changes to prevent conflicts
                        $set('value', null);
                    }),

                TextInput::make('value')
                    ->label(__('Value'))
                    ->visible(fn (Get $get) => in_array($get('type'), ['text','number']))
                    ->dehydrated(fn (Get $get) => in_array($get('type'), ['text','number']))
                    ->numeric(fn (Get $get) => $get('type') === 'number')
                    ->required(false)
                    ->key('value-text'),

                RichEditor::make('value')
                    ->label(__('Value'))
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('type') === 'textarea')
                    ->dehydrated(fn (Get $get) => $get('type') === 'textarea')
                    ->resizableImages()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                        ['table', 'attachFiles', 'customBlocks', 'mergeTags'], // attachFiles present
                        ['undo', 'redo'],
                    ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('editor-attachments')
                    ->fileAttachmentsVisibility('public')
                    ->required(false)
                    ->key('value-richtext'),

                Toggle::make('value')
                    ->label(fn (Get $get) => $get('label') ?: __('Enabled'))
                    ->inline(false)
                    ->helperText(__('Enable or disable this feature'))
                    ->visible(fn (Get $get) => $get('type') === Setting::TYPE_CHECKBOX)
                    ->dehydrated(fn (Get $get) => $get('type') === Setting::TYPE_CHECKBOX)
                    ->formatStateUsing(function ($state, $record) {
                        // Only convert for checkbox records
                        if (!$record || $record->type !== Setting::TYPE_CHECKBOX) {
                            return false;
                        }
                        return $state === '1' || $state === 1 || $state === true;
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0')
                    ->key('value-checkbox'),

                SpatieMediaLibraryFileUpload::make('file')
                    ->label(__('File (Image or Video)'))
                    ->collection('setting_files')
                    ->image(fn (Get $get): bool => $get('type') === Setting::TYPE_IMAGE)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'])
                    ->visible(fn (Get $get): bool => in_array($get('type'), [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO]))
                    ->required(fn (Get $get): bool => in_array($get('type'), [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO]))
                    ->disk('public')
                    ->directory('settings')
                    ->maxSize(20480)
                    ->preserveFilenames(),
            ]);

    }
}
