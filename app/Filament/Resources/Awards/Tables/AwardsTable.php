<?php

namespace App\Filament\Resources\Awards\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('image')
                    ->label(__('Image'))
                    ->circular()
                    ->size(40),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('awarding_body')
                    ->label(__('Awarding Body'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('awarding_body', App::getLocale()) ?? '—')
                    ->limit(30),

                TextColumn::make('award_date')
                    ->label(__('Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable(),

                TextColumn::make('category')
                    ->label(__('Category'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('category', App::getLocale()) ?? '—')
                    ->badge()
                    ->color('warning')
                    ->limit(20),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
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
            ])
            ->defaultSort('award_date', 'desc')
            ->filters([
                SelectFilter::make('featured')
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

                SelectFilter::make('status')
                    ->options([
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
