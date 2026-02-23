<?php

namespace App\Filament\Resources\MissionVisions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class MissionVisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // First image from the 'images' collection
                SpatieMediaLibraryImageColumn::make('images')
                    ->label(__('Image'))
                    ->collection('images')
                    ->conversion('thumb')
                    ->circular()
                    ->size(40),

                // Page header (translatable)
                TextColumn::make('header')
                    ->label(__('Header'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('header', App::getLocale()) ?? '—')
                    ->searchable(query: function ($query, string $value) {
                        $query->whereRaw("JSON_SEARCH(LOWER(header), 'one', ?) IS NOT NULL", ['%' . strtolower($value) . '%']);
                    })
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state),

                // Vision title (translatable)
                TextColumn::make('vision_title')
                    ->label(__('Vision'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('vision_title', App::getLocale()) ?? '—')
                    ->limit(40)
                    ->toggleable(),

                // Mission title (translatable)
                TextColumn::make('mission_title')
                    ->label(__('Mission'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('mission_title', App::getLocale()) ?? '—')
                    ->limit(40)
                    ->toggleable(),

                // External video URL indicator
                TextColumn::make('video_url')
                    ->label(__('Video'))
                    ->formatStateUsing(fn ($state) => $state ? '✓' : '—')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Status badge
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        default     => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        default     => 'gray',
                    }),

                // Timestamps
                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
