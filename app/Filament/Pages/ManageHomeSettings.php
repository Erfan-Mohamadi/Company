<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Settings\HomeSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;

class ManageHomeSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string $settings = HomeSettings::class;

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Home Settings');
    }

    public function getTitle(): string
    {
        return __('Home Settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hero_title')
                    ->label(__('Hero Title'))
                    ->required(),

                TextInput::make('hero_subtitle')
                    ->label(__('Hero Subtitle'))
                    ->required(),

                FileUpload::make('hero_image')
                    ->label(__('Hero Image'))
                    ->image()
                    ->disk('public')
                    ->directory('home')
                    ->nullable(),

                TextInput::make('hero_cta_text')
                    ->label(__('CTA Text'))
                    ->required(),

                TextInput::make('hero_cta_link')
                    ->label(__('CTA Link'))
                    ->url()
                    ->nullable(),

                Repeater::make('features')
                    ->label(__('Features'))
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                        TextInput::make('icon')->nullable(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Toggle::make('show_statistics')
                    ->label(__('Show Statistics')),

                Repeater::make('statistics')
                    ->label(__('Statistics'))
                    ->schema([
                        TextInput::make('number')->numeric()->required(),
                        TextInput::make('label')->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    // Optional: Restrict access to super admins only
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
