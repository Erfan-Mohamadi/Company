<?php

namespace App\Filament\Resources\Languages\Schemas;

use App\Models\Language;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LanguageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Language Details'))
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('flag')
                            ->label(__('Flag'))
                            ->collection('flag')
                            ->circular()
                            ->defaultImageUrl(url('/images/placeholder-flag.png'))
                            ->size(80),

                        TextEntry::make('name')
                            ->label(__('Language Code'))
                            ->badge()
                            ->color('primary')
                            ->weight('bold'),

                        TextEntry::make('label')
                            ->label(__('Display Name'))
                            ->weight('medium'),

                        IconEntry::make('is_rtl')
                            ->label(__('Text Direction'))
                            ->boolean()
                            ->trueIcon('heroicon-o-arrow-right')
                            ->falseIcon('heroicon-o-arrow-left')
                            ->trueColor('info')
                            ->falseColor('gray')
                            ->tooltip(fn ($record) => $record->is_rtl ? __('Right-to-Left') : __('Left-to-Right')),

                        TextEntry::make('is_main')
                            ->label(__('Language Status'))
                            ->badge()
                            ->getStateUsing(fn ($record) => $record->name === Language::MAIN_LANG)
                            ->formatStateUsing(fn ($state) => $state ? __('Main Language') : __('Secondary Language'))
                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                    ])
                    ->columns(2),

                Section::make(__('Timestamps'))
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('Created'))
                            ->dateTime()
                            ->since(),

                        TextEntry::make('updated_at')
                            ->label(__('Last Updated'))
                            ->dateTime()
                            ->since(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
