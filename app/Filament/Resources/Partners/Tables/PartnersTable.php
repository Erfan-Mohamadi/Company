<?php

namespace App\Filament\Resources\Partners\Tables;

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

class PartnersTable
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
            TextColumn::make('partner_name')
                ->label(__('Partner'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('partner_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(35),
            TextColumn::make('partnership_type')
                ->label(__('Type'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'technology' => __('Technology'), 'distribution' => __('Distribution'), 'strategic' => __('Strategic'), default => __('Other') })

                ->color('warning'),
            TextColumn::make('start_date')
                ->label(__('Since'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable(),
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
                SelectFilter::make('partnership_type')
                    ->options(['technology' => __('Technology'), 'distribution' => __('Distribution'), 'strategic' => __('Strategic'), 'other' => __('Other')]),
                SelectFilter::make('status')
                    ->options(['draft' => __('Draft'), 'published' => __('Published')]),
            ])

            ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
