<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        $isFarsi = App::isLocale('fa');

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

                // Display order
                TextColumn::make('order')
                    ->label(__('Order'))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Updated at
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime($isFarsi ? 'j F Y H:i' : 'M j, Y H:i')
                    ->sortable()
                    ->when(
                        $isFarsi,
                        fn (TextColumn $column) => $column->jalaliDateTime('j F Y H:i')
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                SelectFilter::make('department_id')
                    ->label(__('Department'))
                    ->relationship('department', 'name'),

                SelectFilter::make('status')
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
