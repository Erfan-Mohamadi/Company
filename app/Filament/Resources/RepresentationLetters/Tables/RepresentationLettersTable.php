<?php

namespace App\Filament\Resources\RepresentationLetters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
            TextColumn::make('company_name')
                ->label(__('Company'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('company_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(35),
            TextColumn::make('representative_name')
                ->label(__('Representative'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('representative_name', App::getLocale()) ?? '—')
                ->limit(30),
            TextColumn::make('territory')
                ->label(__('Territory'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('territory', App::getLocale()) ?? '—')
                ->badge()
                ->color('info')
                ->limit(25),
            TextColumn::make('issue_date')
                ->label(__('Issued'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable(),
            TextColumn::make('expiry_date')
                ->label(__('Expires'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable()
                ->color(fn ($r) => $r
                    ->expiry_date?->isPast() ? 'danger' : 'success'),
            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'draft' => __('Draft'), 'published' => __('Published'), default => $s })

                ->colors(['draft' => 'gray', 'published' => 'success']),
        ])

        ->defaultSort('order', 'asc')

        ->filters([SelectFilter::make('status')
            ->options(['draft' => __('Draft'), 'published' => __('Published')])])

        ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

        ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
