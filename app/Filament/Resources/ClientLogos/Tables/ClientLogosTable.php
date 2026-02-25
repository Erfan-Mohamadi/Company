<?php

namespace App\Filament\Resources\ClientLogos\Tables;

use App\Models\ClientLogo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class ClientLogosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('client_logo')
                    ->label(__('Logo'))
                    ->collection('client_logo')
                    ->conversion('thumb')
                    ->size(44)
                    ->placeholder(__('No logo')),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', App::getLocale()) ?? '—')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ClientLogo::getTypes()[$state] ?? $state)
                    ->color('warning'),

                TextColumn::make('website_url')
                    ->label(__('Website'))
                    ->limit(30)
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->trueColor('warning')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ClientLogo::getStatuses()[$state] ?? $state)
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
                SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options(ClientLogo::getTypes()),

                SelectFilter::make('featured')
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

                SelectFilter::make('status')
                    ->options(ClientLogo::getStatuses()),
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
