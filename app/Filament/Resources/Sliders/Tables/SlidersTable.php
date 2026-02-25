<?php

namespace App\Filament\Resources\Sliders\Tables;

use App\Models\Slider;
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

class SlidersTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('slider_media')
                    ->label(__('Image'))
                    ->collection('slider_media')
                    ->conversion('thumb')
                    ->size(60)
                    ->placeholder(__('No image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('subtitle')
                    ->label(__('Subtitle'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('subtitle', App::getLocale()) ?? '—')
                    ->limit(35)
                    ->toggleable(),

                TextColumn::make('button_style')
                    ->label(__('Button'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Slider::getButtonStyles()[$state] ?? $state)
                    ->color('info'),

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
                    ->formatStateUsing(fn (string $state): string => Slider::getStatuses()[$state] ?? $state)
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
                SelectFilter::make('button_style')
                    ->label(__('Button Style'))
                    ->options(Slider::getButtonStyles()),

                SelectFilter::make('status')
                    ->options(Slider::getStatuses()),
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
