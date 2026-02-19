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
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('country_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(30),
            TextColumn::make('continent')
                ->label(__('Continent'))
                ->badge()
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
                ->formatStateUsing(fn ($s) => $s ? "{$s}%" : '—')
                ->alignCenter(),
            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'draft' => __('Draft'), 'published' => __('Published'), default => $s })

                ->colors(['draft' => 'gray', 'published' => 'success']),
        ])

            ->defaultSort('order', 'asc')

            ->filters([
                SelectFilter::make('continent')
                    ->options(['Asia' => 'Asia', 'Europe' => 'Europe', 'Africa' => 'Africa', 'North America' => 'North America', 'South America' => 'South America', 'Oceania' => 'Oceania']),
                SelectFilter::make('status')
                    ->options(['draft' => __('Draft'), 'published' => __('Published')]),
            ])

            ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
