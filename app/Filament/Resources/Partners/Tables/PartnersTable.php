<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class PartnersTable
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
                    ->size(40)
                    ->placeholder(__('No logo')),

                TextColumn::make('partner_name')
                    ->label(__('Partner'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('partner_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('partnership_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'technology'   => __('Technology'),
                        'distribution' => __('Distribution'),
                        'strategic'    => __('Strategic'),
                        default        => __('Other'),
                    })
                    ->color('warning'),

                TextColumn::make('start_date')
                    ->label(__('Since'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('end_date')
                    ->label(__('Ends'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        !$record->end_date => 'gray',
                        $record->end_date->isPast() => 'danger',
                        $record->end_date->isFuture() => 'success',
                        default => 'warning',
                    })
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->trueColor('warning')
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
                SelectFilter::make('partnership_type')
                    ->options([
                        'technology'   => __('Technology'),
                        'distribution' => __('Distribution'),
                        'strategic'    => __('Strategic'),
                        'other'        => __('Other'),
                    ]),

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
