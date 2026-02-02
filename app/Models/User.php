<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true; // You can add custom logic here if needed
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $user): void {
            if ($user->hasRole('super_admin')) {
                throw new ModelNotFoundException('Cannot delete super admin user.');
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'email',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['password', 'remember_token'])
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }

    // ── Capture & log helpers ──────────────────────────────────────

    public array $originalRoles = [];
    public array $originalPermissions = [];

    /**
     * Call this BEFORE syncRoles() / syncPermissions() to snapshot current state.
     */
    public function captureOriginalRolesAndPermissions(): void
    {
        // load fresh from DB so we always get the real current state
        $this->load('roles', 'permissions');

        $this->originalRoles = $this->roles->pluck('name')->sort()->values()->toArray();
        $this->originalPermissions = $this->permissions->pluck('name')->sort()->values()->toArray();
    }

    /**
     * Call this AFTER syncRoles() / syncPermissions().
     * Uses ->fresh() to reload relationships from DB after sync.
     */
    public function logRoleOrPermissionChange(string $action = 'Roles & Permissions updated'): void
    {
        // Force reload from DB – without this $this->roles still returns cached (old) data
        $this->load('roles', 'permissions');

        $newRoles = $this->roles->pluck('name')->sort()->values()->toArray();
        $newPermissions = $this->permissions->pluck('name')->sort()->values()->toArray();

        // Only log if something actually changed
        if ($this->originalRoles === $newRoles && $this->originalPermissions === $newPermissions) {
            return;
        }

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_roles'       => $this->originalRoles,
                'new_roles'       => $newRoles,
                'old_permissions' => $this->originalPermissions,
                'new_permissions' => $newPermissions,
            ])
            ->log($action);

        // Reset after logging
        $this->originalRoles = [];
        $this->originalPermissions = [];
    }
}
