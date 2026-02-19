<?php

namespace App\Filament\Resources\Accreditations\Tables;

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

class AccreditationsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');
        return $table->columns([
            ImageColumn::make('logo')
                ->label(__('Logo'))
                ->circular()
                ->size(40),
            TextColumn::make('organization_name')
                ->label(__('Organization'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('organization_name', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(40),
            TextColumn::make('accreditation_type')
                ->label(__('Type'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('accreditation_type', App::getLocale()) ?? '—')
                ->badge()
                ->color('info')
                ->limit(25),
            TextColumn::make('member_since')
                ->label(__('Since'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable(),
            TextColumn::make('end_date')
                ->label(__('Ends'))
                ->date($isFarsi ? 'j F Y' : 'M j, Y')
                ->sortable()
                ->color(fn ($r) => $r
                    ->end_date?->isPast() ? 'danger' : 'success'),
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
