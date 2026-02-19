<?php

namespace App\Filament\Resources\CompanyHistories\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class CompanyHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('year')
                ->label(__('Year'))
                ->sortable()
                ->alignCenter()
                ->badge()
                ->color('primary'),
            TextColumn::make('month')
                ->label(__('Month'))
                ->formatStateUsing(fn ($s) => $s ? Carbon::create()
                    ->month($s)
                    ->monthName : '—')
                ->alignCenter(),
            TextColumn::make('title')
                ->label(__('Title'))
                ->getStateUsing(fn ($r) => $r
                    ->getTranslation('title', App::getLocale()) ?? '—')
                ->searchable()
                ->limit(50),
            TextColumn::make('achievement_type')
                ->label(__('Type'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) {
                    'founding' => __('Founding'), 'product_launch' => __('Product Launch'), 'expansion' => __('Expansion'),
                    'award' => __('Award'), 'partnership' => __('Partnership'), default => __('Other'),
                })
                ->color('warning'),
            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()

                ->formatStateUsing(fn (string $s): string => match ($s) { 'draft' => __('Draft'), 'published' => __('Published'), default => $s })

                ->colors(['draft' => 'gray', 'published' => 'success']),
        ])

            ->defaultSort('year', 'desc')

            ->filters([
                SelectFilter::make('achievement_type')
                    ->options([
                    'founding' => __('Founding'), 'product_launch' => __('Product Launch'),
                    'expansion' => __('Expansion'), 'award' => __('Award'), 'partnership' => __('Partnership'), 'other' => __('Other'),
                ]),
                SelectFilter::make('status')
                    ->options(['draft' => __('Draft'), 'published' => __('Published')]),
            ])

            ->recordActions([EditAction::make(), DeleteAction::make(), ViewAction::make()])

            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
