<?php

namespace App\Filament\Resources\Accreditations\Tables;

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

class AccreditationsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->collection('logo')
                    ->alignCenter()
                    ->circular()
                    ->size(40)
                    ->placeholder(__('No logo')),

                TextColumn::make('organization_name')
                    ->label(__('Organization'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('organization_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->alignCenter()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('accreditation_type')
                    ->label(__('Type'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('accreditation_type', App::getLocale()) ?? '—')
                    ->badge()
                    ->color('info')
                    ->limit(25),

                TextColumn::make('member_since')
                    ->label(__('Since'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('end_date')
                    ->label(__('Ends'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
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
                SelectFilter::make(__('status'))
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
