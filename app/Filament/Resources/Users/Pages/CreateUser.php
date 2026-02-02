<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Log initial assignment after the record and relationships are created
        // syncRoles() and syncPermissions() are already handled by
        // saveRelationshipsUsing() in UserForm.php
        $this->record->captureOriginalRolesAndPermissions();
        $this->record->logRoleOrPermissionChange('Initial roles & permissions assigned');
    }
}
