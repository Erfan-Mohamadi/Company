<?php

namespace App\Filament\Resources\LeadershipTeams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class LeadershipTeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Photo
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Photo'))
                    ->collection('image')
                    ->conversion('thumb')
                    ->circular()
                    ->size(44),

                // Name (translatable)
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('name', App::getLocale()) ?? '—')
                    ->searchable(query: function ($query, string $value) {
                        $query->whereRaw("JSON_SEARCH(LOWER(name), 'one', ?) IS NOT NULL", ['%' . strtolower($value) . '%']);
                    })
                    ->sortable(query: fn ($query, string $direction) =>
                    $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"" . App::getLocale() . "\"')) {$direction}")
                    )
                    ->limit(35),

                // Position (translatable)
                TextColumn::make('position')
                    ->label(__('Position'))
                    ->getStateUsing(fn ($record) => $record->getTranslation('position', App::getLocale()) ?? '—')
                    ->limit(35)
                    ->toggleable(),

                // Department
                TextColumn::make('department.name')
                    ->label(__('Department'))
                    ->limit(25)
                    ->toggleable(),

                // Email
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Featured
                IconColumn::make('featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->alignCenter(),

                // Display order
                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter(),

                // Status badge
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                        default     => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        default     => 'gray',
                    }),

                // Timestamps
                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('department_id')
                    ->label(__('Department'))
                    ->relationship('department', 'name'),

                TernaryFilter::make('featured')
                    ->label(__('Featured'))
                    ->trueLabel(__('Featured only'))
                    ->falseLabel(__('Not featured')),

                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft'     => __('Draft'),
                        'published' => __('Published'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
