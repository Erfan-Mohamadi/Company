<?php

namespace App\Filament\Resources\Dealers\Tables;

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

class DealersTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');
        return $table
            ->columns([
            ImageColumn::make('logo')
                ->label(__('Logo'))
                ->circular()
                ->size(40),
            TextColumn::make('dealer_name')
                ->label(__('Dealer'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('dealer_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(35),
            TextColumn::make('dealer_code')
                ->label(__('Code'))
                ->copyable()
                ->limit(15),
            TextColumn::make('territory')
                ->label(__('Territory'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('territory', App::getLocale()) ?? '—')
                ->limit(25),
            TextColumn::make('contract_end_date')
                ->label(__('Contract Ends'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable()
                ->color(fn ($r) => $r
                    ->contract_end_date?->isPast() ? 'danger' : 'success'),
            TextColumn::make('rating')
                ->label(__('Rating'))
                ->formatStateUsing(fn ($s) => str_repeat('⭐', (int) $s))
                ->alignCenter(),
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
