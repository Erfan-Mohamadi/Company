<?php

namespace App\Filament\Resources\CompanyValues\Tables;

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

class CompanyValuesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Image'))
                    ->alignCenter(),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->alignCenter()
                    ->limit(50),

                TextColumn::make('icon')
                    ->label(__('Icon'))
                    ->alignCenter()
                    ->limit(30)
                    ->color('gray'),

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

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                    ->sortable()
                    ->when($isFarsi, fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')),
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
