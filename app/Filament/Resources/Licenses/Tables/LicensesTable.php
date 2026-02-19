<?php

namespace App\Filament\Resources\Licenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class LicensesTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('license_name')
                    ->label(__('License Name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('license_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('license_number')
                    ->label(__('Number'))
                    ->copyable()
                    ->limit(20),

                TextColumn::make('issuing_authority')
                    ->label(__('Issuing Authority'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('issuing_authority', App::getLocale()) ?? '—')
                    ->limit(30),

                TextColumn::make('expiry_date')
                    ->label(__('Expiry Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expiry_date?->isPast() ? 'danger' : 'success'),

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
                SelectFilter::make('license_type')
                    ->options([
                        'trade'        => __('Trade'),
                        'professional' => __('Professional'),
                        'operating'    => __('Operating'),
                        'import'       => __('Import / Export'),
                        'other'        => __('Other'),
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
