<?php

namespace App\Filament\Resources\GoalStrategies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use phpDocumentor\Reflection\PseudoTypes\HtmlEscapedString;

class GoalStrategiesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->alignCenter()
                    ->limit(60)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('short_summary')
                    ->label(__('Short Summary'))
                    ->alignCenter()
                    ->limit(30),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'goal'     => __('goal'),
                        'strategy' => __('strategy'),
                        'objective' => __('objective'),
                        'milestone' => __('milestone'),
                        default     => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'goal'      => 'info',
                        'strategy'  => 'warning',
                        'objective' => 'success',
                        'milestone' => 'purple',
                        default     => 'gray',
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
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

//                TextColumn::make('created_at')
//                    ->label(__('Created At'))
//                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
//                    ->sortable()
//                    ->when($isFarsi, fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                    ->sortable()
                    ->alignCenter()
                    ->when($isFarsi, fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make(__('type'))
                    ->options([
                        'goal'      => __('Goal'),
                        'strategy'  => __('Strategy'),
                        'objective' => __('Objective'),
                        'milestone' => __('Milestone'),
                    ]),

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
