<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPermissionController extends Controller
{
    // assign or update direct permission for user
public function assign(User $user, Permission $permission, Request $r)
{
$type = $r->input('type', 'allow'); // allow | deny
$user->directPermissions()->syncWithoutDetaching([$permission->id => ['type' => $type]]);
return $user->load(['directPermissions','roles.permissions']);
}


public function revoke(User $user, Permission $permission)
{
$user->directPermissions()->detach($permission->id);
return $user->load(['directPermissions','roles.permissions']);
}
}
