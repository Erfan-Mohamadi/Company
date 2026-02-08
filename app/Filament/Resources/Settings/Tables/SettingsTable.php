<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Models\Setting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('group')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible()
            )
            ->collapsedGroupsByDefault()

            ->columns([
                TextColumn::make('group')
                    ->sortable()
                    ->hidden(),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('label')
                    ->label(__('Label')),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __(Setting::getAllTypes()[$state] ?? $state)),

                TextColumn::make('value')
                    ->label(__('Value'))
                    ->html()
                    ->state(function (Setting $record): mixed {
                        // For image/video, get the media URL
                        if (in_array($record->type, [Setting::TYPE_IMAGE, Setting::TYPE_VIDEO])) {
                            $media = $record->getFirstMedia('setting_files');
                            return $media ? $media->getFullUrl() : $record->value;
                        }

                        return $record->value;
                    })
                    ->formatStateUsing(function ($state, Setting $record): string {
                        return match ($record->type) {
                            Setting::TYPE_IMAGE => filled($state)
                                ? '<img src="' . e($state) . '" style="max-height: 80px; max-width: 120px; border-radius: 6px; object-fit: contain;" alt="' . __('Image preview') . '" loading="lazy" />'
                                : '<span class="text-gray-500 italic">' . __('No image') . '</span>',

                            Setting::TYPE_VIDEO => filled($state)
                                ? '<video src="' . e($state) . '" style="max-height: 80px; max-width: 120px; border-radius: 6px;" controls muted preload="metadata"></video>'
                                : '<span class="text-gray-500 italic">' . __('No video') . '</span>',

                            Setting::TYPE_TOGGLE => (filled($state) && ($state === '1' || $state === 1 || $state === true || $state === 'true'))
                                ? '<svg class="fi-ta-icon-item-icon h-5 w-5 text-success-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>'
                                : '<svg class="fi-ta-icon-item-icon h-5 w-5 text-danger-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>',

                            Setting::TYPE_TEXTAREA => filled($state)
                                ? Str::limit(nl2br(e($state)), 30)
                                : '<span class="text-gray-500 italic">' . __('Not set') . '</span>',

                            default => filled($state)
                                ? e($state)
                                : '<span class="text-gray-500 italic">' . __('Not set') . '</span>',
                        };
                    }),
            ])

            ->filters([
                SelectFilter::make('group')
                    ->label(__('Group'))
                    ->options(collect(Setting::getAllGroups())->mapWithKeys(fn($v, $k) => [$k => __($v['title'] ?? $k)]))
                    ->multiple()
                    ->searchable(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
