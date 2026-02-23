<?php

namespace App\Filament\Resources\CompanyHistories\Tables;

use Carbon\Carbon;
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

class CompanyHistoriesTable
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
                    ->collection('image')
                    ->circular()
                    ->size(40)
                    ->placeholder(__('No image')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(function ($state) use ($isFarsi, $jalaliMonths) {
                        if (!$state) return '—';

                        $date = $state instanceof \Carbon\Carbon ? $state : \Carbon\Carbon::parse($state);

                        if ($isFarsi && class_exists(\Morilog\Jalali\Jalalian::class)) {
                            $jalali = \Morilog\Jalali\Jalalian::fromCarbon($date);
                            return sprintf(
                                '%s %d %d',
                                $jalaliMonths[$jalali->getMonth()] ?? '—',
                                $jalali->getDay(),
                                $jalali->getYear()
                            );
                        }

                        return $date->format('M j, Y');
                    }),

                TextColumn::make('achievement_type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'founding'       => __('Founding'),
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
                SelectFilter::make('achievement_type')
                    ->options([
                        'founding'       => __('Founding'),
                        'product_launch' => __('Product Launch'),
                        'expansion'      => __('Expansion'),
                        'award'          => __('Award'),
                        'partnership'    => __('Partnership'),
                        'other'          => __('Other'),
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
