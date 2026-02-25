<?php

namespace App\Filament\Resources\Awards\Tables;

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

class AwardsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Image'))
                    ->alignCenter()
                    ->collection('image')
                    ->placeholder(__('No image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->alignCenter()
                    ->searchable()
                    ->limit(40),

                TextColumn::make('awarding_body')
                    ->label(__('Awarding Body'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('awarding_body', App::getLocale()) ?? '—')
                    ->limit(30),

                TextColumn::make('award_date')
                    ->label(__('Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->alignCenter()
                    ->sortable()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('category', App::getLocale()) ?? '—')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->limit(20),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->alignCenter()
                    ->boolean()
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
            ->defaultSort('award_date', 'desc')
            ->filters([
                SelectFilter::make(__('featured'))
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

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
