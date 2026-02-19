<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
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
            ImageColumn::make('logo')
                ->label(__('Logo'))
                ->circular()
                ->size(40),
            TextColumn::make('supplier_name')
                ->label(__('Supplier'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('supplier_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(35),
            TextColumn::make('supply_category')
                ->label(__('Category'))
                ->badge()
                ->color('info')
                ->limit(25),
            TextColumn::make('rating')
                ->label(__('Rating'))
                ->formatStateUsing(fn ($s) => str_repeat('⭐', (int) $s))
                ->alignCenter(),
            TextColumn::make('country')
                ->label(__('Country'))
                ->limit(25),
            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'draft' => __('Draft'), 'published' => __('Published'), default => $s })

                ->colors(['draft' => 'gray', 'published' => 'success']),
        ])

            ->defaultSort('order', 'asc')

            ->filters([
                SelectFilter::make('status')
                    ->options(['draft' => __('Draft'), 'published' => __('Published')]),
            ])

            ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
