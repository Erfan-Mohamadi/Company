<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->password()
                ->label(__('Password'))
                ->minLength(8)
                ->required(fn (string $operation) => $operation === 'create')
                ->hiddenOn('view')
                ->rule('confirmed')
                ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                ->dehydrated(fn ($state) => filled($state)),

            TextInput::make('password_confirmation')
                ->password()
                ->label(__('Confirm Password'))
                ->required(fn (string $operation) => $operation === 'create')
                ->hiddenOn('view'),

            Section::make(__('Roles & Permissions'))
                ->description(__('Only super admin can modify'))
                ->visible(fn () => auth()->user()?->hasRole('super_admin'))
                ->schema([
                    CheckboxList::make('roles')
                        ->label(__('Roles'))
                        ->relationship('roles', 'name')
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->disableOptionWhen(fn (string $value, ?Model $record): bool =>
                            $value === 'super_admin' && $record?->id === auth()->id()
                        )
                        ->required(false)
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) {
                            // Prepare for logging (capture old state before any changes)
                            if ($record) {
                                $record->captureOriginalRolesAndPermissions();
                            }
                        })
                        ->saveRelationshipsUsing(function (CheckboxList $component, Model $record, ?array $state) {
                            /** @var \App\Models\User $record */
                            $record->syncRoles($state ?? []);
                            $record->logRoleOrPermissionChange('Roles updated via main form');
                        }),

                    // If you ever want direct permissions visible and saved
                    // (currently hidden, but ready if you change ->hidden() to ->visible())
                    CheckboxList::make('permissions')
                        ->label(__('Direct Permissions (Optional)'))
                        ->relationship('permissions', 'name')
                        ->columns(2)
                        ->searchable()
                        ->bulkToggleable()
                        ->hidden()
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) {
                            if ($record) {
                                $record->captureOriginalRolesAndPermissions();
                            }
                        })
                        ->saveRelationshipsUsing(function (CheckboxList $component, Model $record, ?array $state) {
                            /** @var \App\Models\User $record */
                            $record->syncPermissions($state ?? []);
                            $record->logRoleOrPermissionChange('Direct permissions updated via main form');
                        }),
                ]),
        ]);
    }
}
