<?php

namespace App\Filament\Resources\BoxTexts\Tables;

use App\Models\BoxText;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class BoxTextsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('header')
                    ->label(__('Header'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('header', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => BoxText::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->alignCenter()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->dateTime('M j, Y')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options(BoxText::getStatuses()),
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
