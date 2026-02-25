<?php

namespace App\Filament\Resources\Boxes\Tables;

use App\Models\Box;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class BoxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('box_images')
                    ->label(__('Image'))
                    ->alignCenter()
                    ->collection('box_images')
                    ->conversion('thumb')
                    ->size(44)
                    ->placeholder(__('—')),

                TextColumn::make('header')
                    ->label(__('Header'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('header', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('box_type')
                    ->label(__('Type'))
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => Box::getBoxTypes()[$state] ?? $state)
                    ->color('info'),

                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->placeholder('—')
                    ->limit(30)
                    ->alignCenter()
                    ->toggleable(),

                ColorColumn::make('background_color')
                    ->label(__('BG Color'))
                    ->toggleable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => Box::getStatuses()[$state] ?? $state)
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
                SelectFilter::make('box_type')
                    ->label(__('Type'))
                    ->options(Box::getBoxTypes()),

                SelectFilter::make(__('status'))
                    ->options(Box::getStatuses()),
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
