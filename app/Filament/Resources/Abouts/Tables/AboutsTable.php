<?php

namespace App\Filament\Resources\Abouts\Tables;

use App\Models\About;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AboutsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('header')->searchable(),
                TextColumn::make('founded_year'),
                TextColumn::make('employees_count'),
                BadgeColumn::make('status')
                    ->colors(['draft' => 'gray', 'published' => 'success']),
                TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('preview')
                    ->url(fn (About $record) => route('about.show', $record)) // your frontend route
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
