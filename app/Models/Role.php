<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
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
     * Prevent deletion of super_admin role.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $role): void {
            if ($role->name === 'super_admin') {
                throw new ModelNotFoundException('Cannot delete the super_admin role.');
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
            ->useLogName('role')
            ->setDescriptionForEvent(fn (string $eventName) => "Role has been {$eventName}")
            // Optional: nicer updated message
             ->setDescriptionForEvent(fn (string $eventName) => $eventName === 'updated' ? 'Role updated (details or permissions changed)' : "Role has been {$eventName}");
    }
}
