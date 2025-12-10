<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function assign(User $user, Request $r)
{
// expects 'roles' => [role_id,...]
$user->roles()->sync($r->input('roles', []));
return $user->load('roles');
}


public function attach(User $user, Role $role)
{
$user->roles()->syncWithoutDetaching([$role->id]);
return $user->load('roles');
}


public function detach(User $user, Role $role)
{
$user->roles()->detach($role->id);
return $user->load('roles');
}
}
