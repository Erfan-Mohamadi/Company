<?php

namespace App\Filament\Resources\Suppliers\Tables;

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

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->collection('logo')
                    ->circular()
                    ->size(40)
                    ->placeholder(__('No logo')),

                TextColumn::make('supplier_name')
                    ->label(__('Supplier'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('supplier_name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('supply_category')
                    ->label(__('Category'))
                    ->badge()
                    ->color('info')
                    ->limit(25),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', (int) $state))
                    ->alignCenter(),

                TextColumn::make('country')
                    ->label(__('Country'))
                    ->limit(25),

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
