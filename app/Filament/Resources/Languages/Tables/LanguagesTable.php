<?php

namespace App\Filament\Resources\Languages\Tables;

use App\Models\Language;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LanguagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('flag')
                    ->label(__('Flag'))
                    ->collection('flag')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder-flag.png'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('label')
                    ->label(__('Display Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->is_rtl ? __('RTL Language') : __('LTR Language')),

                IconColumn::make('is_rtl')
                    ->label(__('Direction'))
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-right')
                    ->falseIcon('heroicon-o-arrow-left')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) => $record->is_rtl ? __('Right-to-Left') : __('Left-to-Right'))
                    ->alignCenter(),

                TextColumn::make('is_main')
                    ->label(__('Status'))
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->name === Language::MAIN_LANG)
                    ->formatStateUsing(fn ($state) => $state ? __('Main Language') : __('Secondary'))
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Last Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_rtl')
                    ->label(__('Direction'))
                    ->placeholder(__('All languages'))
                    ->trueLabel(__('RTL only'))
                    ->falseLabel(__('LTR only')),

                TernaryFilter::make('is_main')
                    ->label(__('Language Type'))
                    ->placeholder(__('All languages'))
                    ->trueLabel(__('Main language only'))
                    ->falseLabel(__('Secondary languages only'))
                    ->query(function ($query, array $data) {
                        if (!isset($data['value'])) return $query;

                        if ($data['value']) {
                            return $query->where('name', Language::MAIN_LANG);
                        } else {
                            return $query->where('name', '!=', Language::MAIN_LANG);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->after(fn () => Language::clearAllCaches()),
            ])
            ->defaultSort('name', 'asc')
            ->striped();
    }
}
