<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            ImageColumn::make('logo')
                ->label(__('Logo'))
                ->circular()
                ->size(40),
            TextColumn::make('name')
                ->label(__('Name'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(35),
            TextColumn::make('industry')
                ->label(__('Industry'))
                ->badge()
                ->color('info')
                ->limit(25),
            TextColumn::make('country')
                ->label(__('Country'))
                ->limit(25),
            IconColumn::make('featured')
                ->label(__('Featured'))
                ->boolean()
                ->alignCenter(),
            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'draft' => __('Draft'), 'published' => __('Published'), default => $s })

                ->colors(['draft' => 'gray', 'published' => 'success']),
        ])

            ->defaultSort('order', 'asc')

            ->filters([
                SelectFilter::make('featured')
                    ->options(['1' => __('Featured'), '0' => __('Not Featured')]),
                SelectFilter::make('status')
                    ->options(['draft' => __('Draft'), 'published' => __('Published')]),
            ])

            ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
