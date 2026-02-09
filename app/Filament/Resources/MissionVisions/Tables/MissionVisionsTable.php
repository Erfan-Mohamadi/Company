<?php

namespace App\Filament\Resources\MissionVisions\Tables;

use App\Models\MissionVision;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MissionVisionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('header')
                    ->searchable(),

                TextColumn::make('vision_title')
                    ->searchable(),

                TextColumn::make('mission_title')
                    ->searchable(),

                BadgeColumn::make('status')
                    ->colors([
                        'draft' => 'gray',
                        'published' => 'success',
                    ]),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('preview')
                    ->url(fn (MissionVision $record) => url('/about/mission-vision')) // ← adjust to your real frontend route
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
