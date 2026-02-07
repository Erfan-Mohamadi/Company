<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Form;
use Illuminate\Support\Str;

class SettingForm
{
    /**
     * This schema is used in the resource's create/edit pages (if needed).
     * For the group settings page, we use a dynamic form in GroupSettings.
     */
    public static function make(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group')
                    ->label('گروه')
                    ->options(collect(Setting::getAllGroups())->mapWithKeys(fn($v, $k) => [$k => $v['title'] ?? $k]))
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('نام کلید')
                    ->required()
                    ->alphaDash()
                    ->unique(Setting::class, 'name', ignoreRecord: true),

                Forms\Components\TextInput::make('label')
                    ->label('برچسب')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('نوع')
                    ->options(Setting::getAllTypes())
                    ->required(),

                // Value field – conditional based on type
                Forms\Components\TextInput::make('value')
                    ->label('مقدار')
                    ->visible(fn ($get) => in_array($get('type'), ['text', 'number']))
                    ->numeric(fn ($get) => $get('type') === 'number'),

                Forms\Components\RichEditor::make('value')
                    ->label('مقدار')
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('type') === 'textarea'),

                Forms\Components\Toggle::make('value')
                    ->label(fn ($get) => $get('label') ?: 'فعال / غیرفعال')
                    ->inline(false)
                    ->visible(fn ($get) => $get('type') === 'toggle'),

                SpatieMediaLibraryFileUpload::make('file')
                    ->label('فایل (تصویر یا ویدئو)')
                    ->collection('setting_files')
                    ->image(fn ($get) => $get('type') === 'image')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'])
                    ->visible(fn ($get) => in_array($get('type'), ['image', 'video']))
                    ->disk('public')
                    ->directory('settings')
                    ->imageEditor()
                    ->preserveFilenames(),
            ]);
    }
}
