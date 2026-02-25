<?php

namespace App\Filament\Resources\Certifications\Tables;

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

class CertificationsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('certificate_image')
                    ->label(__('Certificate Image'))
                    ->collection('certificate_image')
                    ->circular()
                    ->alignCenter()
                    ->size(40),

                TextColumn::make('title')
                    ->label(__('Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('title', App::getLocale()) ?? '—')
                    ->searchable()
                    ->alignCenter()
                    ->limit(40),

                TextColumn::make('certification_body')
                    ->label(__('Certification Body'))
                    ->alignCenter()
                    ->limit(30),

                TextColumn::make('issue_date')
                    ->label(__('Issue Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('expiry_date')
                    ->label(__('Expiry Date'))
                    ->date($isFarsi ? 'j F Y' : 'M j, Y')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($record) => $record->expiry_date?->isPast() ? 'danger' : 'success')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

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
                SelectFilter::make(__('featured'))
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),

                SelectFilter::make(__('status'))
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
