<?php

namespace App\Filament\Resources\HomeSettings\Schemas;

use App\Models\HomeSetting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class HomeSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('group')
                    ->label('گروه')
                    ->options(collect(HomeSetting::getAllGroups())->mapWithKeys(fn($v, $k) => [$k => $v['title'] ?? $k]))
                    ->required(),

                TextInput::make('name')
                    ->label('نام کلید')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),

                TextInput::make('label')
                    ->label('نام نمایشی')
                    ->required(),

                Select::make('type')
                    ->label('نوع')
                    ->options(HomeSetting::getAllTypes())
                    ->required()
                    ->live(),

                TextInput::make('value')
                    ->label('مقدار')
                    ->visible(fn (Get $get) => in_array($get('type'), ['text','number']))
                    ->numeric(fn (Get $get) => $get('type') === 'number'),

                Textarea::make('value')
                    ->label('مقدار')
                    ->visible(fn (Get $get) => $get('type') === 'textarea')
                    ->rows(5),

                SpatieMediaLibraryFileUpload::make('file')
                    ->label('فایل (تصویر یا ویدئو)')
                    ->collection('home_setting_files')
                    ->image(fn (Get $get): bool => $get('type') === HomeSetting::TYPE_IMAGE)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'])
                    ->visible(fn (Get $get): bool => in_array($get('type'), [HomeSetting::TYPE_IMAGE, HomeSetting::TYPE_VIDEO]))
                    ->required(fn (Get $get): bool => in_array($get('type'), [HomeSetting::TYPE_IMAGE, HomeSetting::TYPE_VIDEO]))
                    ->disk('public')
                    ->directory('home-settings')
                    ->imageEditor()
                    ->maxSize(20480)
                    ->preserveFilenames(),
                ]);

    }
}
