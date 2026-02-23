<?php

namespace App\Filament\Resources\Departments\Tables;

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

class DepartmentsTable
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
                    ->size(40)
                    ->placeholder(__('No image')),

                TextColumn::make('name')
                    ->label(__('Department Name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('head_name')
                    ->label(__('Head'))
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('employee_count')
                    ->label(__('Employees'))
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('location')
                    ->label(__('Location'))
                    ->limit(30),

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
