<?php

namespace App\Filament\Resources\CallToActions\Tables;

use App\Models\CallToAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class CallToActionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('cta_backgrounds')
                    ->label(__('Background'))
                    ->collection('cta_backgrounds')
                    ->conversion('thumb')
                    ->size(60)
                    ->placeholder(__('—')),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($state): ?string => $state),

                TextColumn::make('button_text')
                    ->label(__('Button'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('button_text', App::getLocale()) ?? '—')
                    ->limit(25),

                TextColumn::make('button_style')
                    ->label(__('Style'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CallToAction::getButtonStyles()[$state] ?? $state)
                    ->color('info'),

                ColorColumn::make('background_color')
                    ->label(__('BG Color'))
                    ->toggleable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => CallToAction::getStatuses()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('button_style')
                    ->label(__('Button Style'))
                    ->options(CallToAction::getButtonStyles()),

                SelectFilter::make('status')
                    ->options(CallToAction::getStatuses()),
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
