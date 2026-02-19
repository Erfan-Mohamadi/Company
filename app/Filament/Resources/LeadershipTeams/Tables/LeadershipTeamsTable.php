<?php

namespace App\Filament\Resources\LeadershipTeams\Tables;

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

class LeadershipTeamsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label(__('Photo'))
                    ->circular()
                    ->size(44),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('position')
                    ->label(__('Position'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('position', App::getLocale()) ?? '—')
                    ->limit(35),

                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->limit(25),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->alignCenter(),

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
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('department_id')
                    ->label(__('Department'))
                    ->relationship('department', 'name'),

                SelectFilter::make('featured')
                    ->options([
                        '1' => __('Featured'),
                        '0' => __('Not Featured'),
                    ]),

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
