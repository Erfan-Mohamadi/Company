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
                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                    ->sortable()
                    ->when($isFarsi, fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')),

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
                SelectFilter::make(__('achievement_type'))
                    ->options([
                        'founding'       => __('Founding'),
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
