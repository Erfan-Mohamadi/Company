<?php

namespace App\Filament\Resources\Statistics\Tables;

use App\Models\Statistic;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class StatisticsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Label'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('formatted_number')
                    ->label(__('Value'))
                    ->getStateUsing(fn ($record) => trim(
                        ($record->getTranslation('prefix', App::getLocale()) ?? '') .
                        $record->number .
                        ($record->getTranslation('suffix', App::getLocale()) ?? '')
                    ))
                    ->alignCenter(),

                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->placeholder('—')
                    ->limit(30)
                    ->toggleable(),

                IconColumn::make('animation_enabled')
                    ->label(__('Animation'))
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Statistic::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options(Statistic::getStatuses()),
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
