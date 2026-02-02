<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('activities')
                    ->label(__('Activities'))
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->url(fn ($record) => UserResource::getUrl('activities', ['record' => $record])),

                Impersonate::make()
                    ->visible(fn (Model $record): bool =>
                        auth()->user()?->hasRole('super_admin') &&
                        ! $record->hasRole('super_admin')
                    ),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Model $record): bool => ! $record->hasRole('super_admin'))
                    ->authorize(fn (Model $record): bool => ! $record->hasRole('super_admin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('Delete Selected'))
                        ->before(function ($records) {
                            // Prevent bulk-deleting super admins
                            return $records->reject(fn (Model $record) => $record->hasRole('super_admin'));
                        }),
                ]),
            ]);
    }
}
