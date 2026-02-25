<?php

namespace App\Filament\Resources\Milestones\Tables;

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

class MilestonesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        $jalaliMonths = [
            1  => 'فروردین',
            2  => 'اردیبهشت',
            3  => 'خرداد',
            4  => 'تیر',
            5  => 'مرداد',
            6  => 'شهریور',
            7  => 'مهر',
            8  => 'آبان',
            9  => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Image'))
                    ->alignCenter()
                    ->collection('image')
                    ->placeholder(__('No image')),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->alignCenter()
                    ->dateTime($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->when($isFarsi, fn (TextColumn $column) => $column->jalaliDateTime('j F Y')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('achievement_type')
                    ->label(__('Type'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'product_launch' => __('Product Launch'),
                        'expansion'      => __('Expansion'),
                        'award'          => __('Award'),
                        'partnership'    => __('Partnership'),
                        default          => __('Other'),
                    })
                    ->color('warning'),

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
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make(__('achievement_type'))
                    ->options([
                        'product_launch' => __('Product Launch'),
                        'expansion'      => __('Expansion'),
                        'award'          => __('Award'),
                        'partnership'    => __('Partnership'),
                        'other'          => __('Other'),
                    ]),

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
