<?php

namespace App\Filament\Resources\Popups\Tables;

use App\Models\Popup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class PopupsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('popup_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Popup::getPopupTypes()[$state] ?? $state)
                    ->color('info'),

                TextColumn::make('trigger_type')
                    ->label(__('Trigger'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Popup::getTriggerTypes()[$state] ?? $state)
                    ->color('warning'),

                TextColumn::make('frequency')
                    ->label(__('Frequency'))
                    ->formatStateUsing(fn (string $state): string => Popup::getFrequencies()[$state] ?? $state)
                    ->toggleable(),

                TextColumn::make('start_date')
                    ->label(__('Start'))
                    ->dateTime($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->when($isFarsi, fn (TextColumn $col) => $col->jalaliDate('j F Y'))
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label(__('End'))
                    ->dateTime($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        !$record->end_date            => 'gray',
                        $record->end_date->isPast()   => 'danger',
                        default                       => 'success',
                    })
                    ->when($isFarsi, fn (TextColumn $col) => $col->jalaliDate('j F Y'))
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Popup::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->defaultSort('status', 'asc')
            ->filters([
                SelectFilter::make('popup_type')
                    ->label(__('Type'))
                    ->options(Popup::getPopupTypes()),

                SelectFilter::make('trigger_type')
                    ->label(__('Trigger'))
                    ->options(Popup::getTriggerTypes()),

                SelectFilter::make('status')
                    ->options(Popup::getStatuses()),
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
