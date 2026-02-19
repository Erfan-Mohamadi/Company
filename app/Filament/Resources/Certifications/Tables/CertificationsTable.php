<?php

namespace App\Filament\Resources\Certifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class CertificationsTable
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
                    ->limit(40),

                TextColumn::make('certification_body')
                    ->label(__('Body'))
                    ->limit(30),

                TextColumn::make('issue_date')
                    ->label(__('Issue Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label(__('Expiry Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->expiry_date?->isPast() ? 'danger' : 'success'),

                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->alignCenter(),

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
                SelectFilter::make('featured')
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

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
