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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // if you ever add more columns, add here
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    // user_roles pivot (default table name: role_user)
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    // direct permissions (permission_user pivot, with type = allow/deny)
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('type') // 'allow' or 'deny'
            ->withTimestamps();
    }

    // permissions coming from roles
    public function rolePermissions()
    {
        // make sure roles are loaded (avoid null on first call)
        $roleIds = $this->roles->pluck('id');

        if ($roleIds->isEmpty()) {
            return collect();
        }

        return Permission::whereHas('roles', function ($q) use ($roleIds) {
            $q->whereIn('roles.id', $roleIds);
        })->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Permission logic
    |--------------------------------------------------------------------------
    */

    // All effective permissions: role-based + direct allow/deny overrides
    public function allPermissions()
    {
        // start from role-based permissions (use slug or name – pick one and be consistent)
        $rolePerms = $this->rolePermissions()
            ->pluck('slug') // or 'name' if you prefer
            ->unique()
            ->values()
            ->toArray();

        // direct permissions: ['permission_slug' => 'allow' | 'deny']
        $direct = $this->directPermissions()
            ->get()
            ->mapWithKeys(function ($p) {
                return [$p->slug => $p->pivot->type]; // or $p->name
            })
            ->toArray();

        // apply direct overrides
        foreach ($direct as $slug => $type) {
            if ($type === 'allow') {
                if (!in_array($slug, $rolePerms)) {
                    $rolePerms[] = $slug;
                }
            } elseif ($type === 'deny') {
                $rolePerms = array_values(array_diff($rolePerms, [$slug]));
            }
        }

        return collect($rolePerms)->unique()->values();
    }

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

    public function hasPermission(string $permissionSlug): bool
    {
        // 1) check direct override first
        $direct = $this->directPermissions()
            ->where('slug', $permissionSlug) // or 'name'
            ->first();

        if ($direct) {
            return $direct->pivot->type === 'allow';
        }

        // 2) else, check via roles
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permissionSlug) {
                $q->where('slug', $permissionSlug); // or 'name'
            })
            ->exists();
    }

    public function permissions()
{
    return $this->belongsToMany(Permission::class)
                ->withPivot('type'); // Include 'type' if it's part of the pivot table
}
}
