<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'slug', 'description','group'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
                    ->withTimestamps();
    }

public function users()
{
    return $this->belongsToMany(User::class, 'permission_user')
                ->withPivot('type') // Include 'type' if it's part of the pivot table
                ->withTimestamps(); // Automatically include created_at and updated_at timestamps
}
}
