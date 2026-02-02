<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->live(),

                TextInput::make('value')
                    ->label(__('Value'))
                    ->visible(fn (Get $get) => in_array($get('type'), ['text','number']))
                    ->numeric(fn (Get $get) => $get('type') === 'number'),

                Textarea::make('value')
                    ->label(__('Value'))
                    ->visible(fn (Get $get) => $get('type') === 'textarea')
                    ->rows(5),

                SpatieMediaLibraryFileUpload::make('file')
                    ->label(__('File (Image or Video)'))
                    ->collection('setting_files')
                    ->image(fn (Get $get): bool => $get('type') === Setting::TYPE_IMAGE)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'])
                    ->visible(fn (Get $get): bool => in_array($get('type'), [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO]))
                    ->required(fn (Get $get): bool => in_array($get('type'), [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO]))
                    ->disk('public')
                    ->directory('settings')
                    ->imageEditor()
                    ->maxSize(20480)
                    ->preserveFilenames(),
            ]);

    }
}
