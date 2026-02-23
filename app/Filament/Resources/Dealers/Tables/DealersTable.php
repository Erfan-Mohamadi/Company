<?php

namespace App\Filament\Resources\Dealers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class DealersTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->collection('logo')
                    ->circular()
                    ->alignCenter()
                    ->size(40)
                    ->placeholder(__('No logo')),

                TextColumn::make('dealer_name')
                    ->label(__('Dealer'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('dealer_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->alignCenter()
                    ->limit(35)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('dealer_code')
                    ->label(__('Code'))
                    ->copyable()
                    ->alignCenter()
                    ->limit(15),

                TextColumn::make('territory')
                    ->label(__('Territory'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('territory', App::getLocale()) ?? '—')
                    ->limit(25),

                TextColumn::make('contract_end_date')
                    ->label(__('Contract Ends'))
                    ->alignCenter()
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        !$record->contract_end_date => 'gray',
                        $record->contract_end_date->isPast() => 'danger',
                        $record->contract_end_date->isFuture() => 'success',
                        default => 'warning',
                    })
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state))
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->alignCenter()
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
                SelectFilter::make('status')
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
