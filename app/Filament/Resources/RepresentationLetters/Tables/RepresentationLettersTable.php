<?php

namespace App\Filament\Resources\RepresentationLetters\Tables;

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
class RepresentationLettersTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
//                SpatieMediaLibraryImageColumn::make('document_preview') // optional – add collection if you later add image preview
//                ->label(__('Preview'))
//                    ->collection('document_preview')
//                    ->circular()
//                    ->size(40)
//                    ->placeholder(__('No preview')),

                TextColumn::make('company_name')
                    ->label(__('Company'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('company_name', App::getLocale()) ?? '—')
                    ->alignCenter()
                    ->searchable()
                    ->limit(35),

                TextColumn::make('representative_name')
                    ->label(__('Representative'))
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->getTranslation('representative_name', App::getLocale()) ?? '—')
                    ->limit(30),

                TextColumn::make('territory')
                    ->label(__('Territory'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('territory', App::getLocale()) ?? '—')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->limit(25),

                TextColumn::make('issue_date')
                    ->label(__('Issued'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('expiry_date')
                    ->label(__('Expires'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($record) => match (true) {
                        !$record->expiry_date => 'gray',
                        $record->expiry_date->isPast() => 'danger',
                        $record->expiry_date->isFuture() => 'success',
                        default => 'warning',
                    })
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

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
            ->defaultSort('order', 'asc')
            ->filters([
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
