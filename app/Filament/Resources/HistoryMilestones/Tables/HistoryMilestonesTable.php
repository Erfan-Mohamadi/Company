<?php

namespace App\Filament\Resources\HistoryMilestones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class HistoryMilestonesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');
        $locale  = App::getLocale();

        return $table
            ->columns([
                TextColumn::make('year')
                    ->label(__('Year'))
                    ->sortable()
                    ->alignCenter()
                    ->when($isFarsi, fn (TextColumn $col) => $col->formatStateUsing(fn ($state) => verta($state . '-01-01')->format('Y'))),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', $locale) ?? '—')
                    ->searchable()
                    ->limit(60),

                TextColumn::make('event_type')
                    ->label(__('Event Type'))
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        default     => $state,
                    })
                    ->colors([
                        'draft'     => 'gray',
                        'published' => 'success',
                    ]),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                    ->sortable()
                    ->when($isFarsi, fn (TextColumn $col) => $col->jalaliDateTime('j F Y H:i')),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                    ]),
                SelectFilter::make('event_type')
                    ->label(__('Event Type'))
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
