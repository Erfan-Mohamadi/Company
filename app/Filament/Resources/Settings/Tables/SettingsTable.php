<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Models\Setting;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('label'),
                TextColumn::make('type')->badge(),
                TextColumn::make('value')
                    ->html(fn ($state, $record) => match($record->type) {
                        'image' => $state ? "<img src='$state' style='max-height:60px'/>" : '-',
                        'video' => $state ? "<video src='$state' style='max-height:60px' controls></video>" : '-',
                        default => e($state) ?: '-',
                    }),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(collect(Setting::getAllGroups())->mapWithKeys(fn($v, $k) => [$k => $v['title'] ?? $k])),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
