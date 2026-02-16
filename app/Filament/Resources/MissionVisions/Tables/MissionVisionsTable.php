<?php

namespace App\Filament\Resources\MissionVisions\Tables;

use App\Models\Language;
use App\Models\MissionVision;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class MissionVisionsTable
{
    public static function configure(Table $table): Table
    {
        $locale = App::getLocale();
        $mainLang = Language::MAIN_LANG;
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('header')
                    ->label(__('Header'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('header', $locale)
                        ?? $record->getTranslation('header', $mainLang)
                        ?? '—')
                    ->searchable(query: fn ($query, $search) =>
                    $query->whereJsonContains("header->{$locale}", $search)
                    ),

                TextColumn::make('vision_title')
                    ->label(__('Vision Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('vision_title', $locale)
                        ?? $record->getTranslation('vision_title', $mainLang)
                        ?? '—')
                    ->searchable(),

                TextColumn::make('mission_title')
                    ->label(__('Mission Title'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('mission_title', $locale)
                        ?? $record->getTranslation('mission_title', $mainLang)
                        ?? '—')
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'published' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->date($isFarsi ? 'j F Y' : 'F j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    )
                    ->sortable(),
            ])
            ->filters([
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
                Action::make('preview')
                    ->label(__('Preview'))
                    ->url(fn ($record) => url('/about/mission-vision')) // ← update to your real frontend route
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
