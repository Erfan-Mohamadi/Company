<?php

namespace App\Filament\Resources\CoreValues\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class CoreValuesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('value_name')
                    ->label(__('Value Name'))
                    ->getStateUsing(fn($record) => $record->getTranslation('value_name', App::getLocale()) ?? '—')
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(80)
                    ->tooltip(fn($state) => $state),

                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->formatStateUsing(fn($state) => $state ? "<span class=\"text-xl\">{$state}</span>" : '—')
                    ->html(),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
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
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime($isFarsi ? 'j F Y' : 'M j, Y')
                    ->when($isFarsi, fn(TextColumn $c) => $c->jalaliDate('j F Y'))
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc');
    }
}
