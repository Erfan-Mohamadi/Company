<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use LogsActivity;

    /**
     * Fillable attributes (add 'team_id' if tenancy is enabled).
     */
    protected $fillable = [
        'name',
        'guard_name',
        // 'team_id',  // ← uncomment if using teams/tenancy in permission.php
    ];

    /**
     * Optional: Prevent deletion of critical permissions (e.g., 'access_admin').
     */
    protected static function booted(): void
    {
        static::deleting(function (self $permission): void {
            $protected = ['access_admin', 'super_admin_permission'];  // Add your critical ones
            if (in_array($permission->name, $protected)) {
                throw new ModelNotFoundException("Cannot delete protected permission: {$permission->name}");
                // Alternative (silent): return false;
            }
        });
    }

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'guard_name',
                // 'team_id',  // if tenancy
            ])
            ->logOnlyDirty()                 // Only changed fields
            ->dontSubmitEmptyLogs()          // Skip empty updates
            ->logExcept(['updated_at'])      // Less noise
            ->useLogName('permission')
            ->setDescriptionForEvent(fn (string $eventName) => "Permission has been {$eventName}");
    }
}
