<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolePermissionController extends Controller
{
   public function sync(Role $role, Request $r)
{
// expects permission ids array
$role->permissions()->sync($r->input('permissions', []));
return $role->load('permissions');
}


public function attach(Role $role, Permission $permission)
{
$role->permissions()->syncWithoutDetaching([$permission->id]);
return $role->load('permissions');
}


public function detach(Role $role, Permission $permission)
{
$role->permissions()->detach($permission->id);
return $role->load('permissions');
}
}
