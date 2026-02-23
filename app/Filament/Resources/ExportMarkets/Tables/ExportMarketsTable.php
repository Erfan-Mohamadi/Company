<?php

namespace App\Filament\Resources\ExportMarkets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class ExportMarketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country_name')
                    ->label(__('Country'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('country_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('continent')
                    ->label(__('Continent'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Asia'          => __('Asia'),
                        'Europe'        => __('Europe'),
                        'Africa'        => __('Africa'),
                        'North America' => __('North America'),
                        'South America' => __('South America'),
                        'Oceania'       => __('Oceania'),
                        default     => $state,
                    })
                    ->color('info'),

                TextColumn::make('export_value')
                    ->label(__('Export Value'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('distributors_count')
                    ->label(__('Distributors'))
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('growth_rate')
                    ->label(__('Growth %'))
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : '—')
                    ->alignCenter(),

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

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('continent')
                    ->label(__('Continent'))
                    ->options([
                        'Asia'          => __('Asia'),
                        'Europe'        => __('Europe'),
                        'Africa'        => __('Africa'),
                        'North America' => __('North America'),
                        'South America' => __('South America'),
                        'Oceania'       => __('Oceania'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('Status'))
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
