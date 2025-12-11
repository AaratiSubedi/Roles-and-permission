<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        // add more fields here if needed
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // user_roles pivot (role_user)
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    // direct permissions via permission_user pivot, with 'type' = allow | deny
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('type') // 'allow' or 'deny'
            ->withTimestamps();
    }

    // permissions that come from roles
    public function rolePermissions()
    {
        // if roles not loaded yet, load them
        $roles = $this->roles()->with('permissions')->get();

        return $roles->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->values();
    }

    /**
     * Effective permissions: role-based ± direct overrides
     * Returns a collection of Permission models
     */
    public function allPermissions()
    {
        // Start with role-based permissions (models)
        $permissions = $this->rolePermissions();

        // Direct overrides: ['slug' => 'allow' | 'deny']
        $direct = $this->directPermissions()
            ->get()
            ->mapWithKeys(function ($p) {
                return [$p->slug => $p->pivot->type]; // use 'name' if you prefer
            });

        // Apply overrides
        foreach ($direct as $slug => $type) {
            if ($type === 'allow') {
                // ensure it's present
                if (! $permissions->contains('slug', $slug)) {
                    $permModel = Permission::where('slug', $slug)->first();
                    if ($permModel) {
                        $permissions->push($permModel);
                    }
                }
            } elseif ($type === 'deny') {
                // remove if present
                $permissions = $permissions->reject(function ($p) use ($slug) {
                    return $p->slug === $slug;
                })->values();
            }
        }

        return $permissions->unique('id')->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Role & Permission Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRole($role): bool
    {
        // If array: check if user has ANY of these role slugs/names
        if (is_array($role)) {
            $slugs = $this->roles->pluck('slug')->toArray();
            $names = $this->roles->pluck('name')->toArray();

            return count(array_intersect($slugs, $role)) > 0
                || count(array_intersect($names, $role)) > 0;
        }

        // If string: check by slug or name
        if (is_string($role)) {
            return $this->roles->contains('slug', $role)
                || $this->roles->contains('name', $role);
        }

        // If Role model or id
        return $this->roles->contains('id', $role->id ?? $role);
    }

    /**
     * Check if user has a given permission (by slug or Permission model)
     */
    public function hasPermission($permission): bool
    {
        $effectivePermissions = $this->allPermissions();

        if (is_string($permission)) {
            return $effectivePermissions->contains('slug', $permission)
                || $effectivePermissions->contains('name', $permission);
        }

        // If Permission model or id
        return $effectivePermissions->contains('id', $permission->id ?? $permission);
    }
}
