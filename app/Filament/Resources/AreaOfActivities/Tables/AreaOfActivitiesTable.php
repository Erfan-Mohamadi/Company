<?php

namespace App\Filament\Resources\AreaOfActivities\Tables;

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

class AreaOfActivitiesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Image'))
                    ->collection('image')
                    ->circular()
                    ->size(40),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->limit(30)
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('industries')
                    ->label(__('Industries'))
                    ->getStateUsing(fn ($record) => count($record->industries ?? []))
                    ->badge()
                    ->color('info')
                    ->suffix(' ' . __('industries')),

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
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')
                    ),
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
