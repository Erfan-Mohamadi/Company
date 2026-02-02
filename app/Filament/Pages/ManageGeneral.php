<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;

class ManageGeneral extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected string $view = 'filament.pages.manage-general';  // ← matches manage-general.blade.php
    protected static string|null|\UnitEnum $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('General Settings');
    }

    protected static ?string $title = 'General Settings';           // ← same here

    public function getTitle(): string
    {
        return __('General Settings');
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->label(__('Site Name'))
                    ->required()
                    ->maxLength(255),

                Toggle::make('maintenance_mode')
                    ->label(__('Maintenance Mode'))
                    ->helperText(__('Enable to show maintenance page for visitors')),

                FileUpload::make('logo_path')
                    ->label(__('Site Logo'))
                    ->image()
                    ->disk('public')
                    ->directory('logos')
                    ->imageEditor()
                    ->nullable(),

                Repeater::make('social_links')
                    ->label(__('Social Media Links'))
                    ->schema([
                        TextInput::make('platform')
                            ->label(__('Platform'))
                            ->required()
                            ->maxLength(50),

                        TextInput::make('url')
                            ->label(__('URL'))
                            ->required()
                            ->url(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->default([]),


            ]);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
