<?php

namespace App\Filament\Resources\Abouts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class AboutsTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

        return $table
            ->columns([
                TextColumn::make('header')
                    ->label(__('Company Header'))
                    ->searchable(),

                TextColumn::make('founded_date')
                    ->label(__('Founded Date'))
                    ->date($isFarsi ? 'j F Y' : 'F j, Y')
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDate('j F Y')
                    ),

                TextColumn::make('clients_count')
                    ->label(__('Clients Count')),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        default     => $state,
                    })
                    ->colors([
                        'draft'     => 'gray',
                        'published' => 'success',
                    ]),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDateTime()
                    ),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
