<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use STS\FilamentImpersonate\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()
                ->record($this->getRecord())
                ->visible(fn (): bool =>
                    auth()->user()?->hasRole('super_admin') &&
                    ! $this->getRecord()->hasRole('super_admin')
                ),

            DeleteAction::make()
                ->label(__('Delete User'))
                ->visible(fn (): bool => ! $this->getRecord()->hasRole('super_admin'))
                ->requiresConfirmation()
                ->modalHeading(__('Delete User'))
                ->modalDescription(__('Are you sure you want to delete this user? This action cannot be undone.'))
                ->modalSubmitActionLabel(__('Yes, Delete'))
                ->modalCancelActionLabel(__('Cancel')),
        ];
    }
}
