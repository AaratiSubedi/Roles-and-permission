<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAccessController extends Controller
{
    // Display all users with their roles and direct permissions
    public function index()
    {
        $users = User::with('roles', 'directPermissions')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    // Edit user roles and permissions
public function edit(User $user)
{
    $roles       = Role::all();
    $permissions = Permission::all();

    // Get the role IDs assigned to the user
    $userRoleIds = $user->roles->pluck('id')->toArray();

    // Get the direct permissions assigned to the user (not via roles)
    $userDirectPerms = $user->directPermissions->keyBy('id'); // includes pivot->type

    // Get all permissions assigned to the user's roles (to exclude from available permissions)
    $assignedPermissions = $user->roles->flatMap(function ($role) {
        return $role->permissions;
    })->pluck('id')->unique();

    // Get the permissions that are not assigned to the user through their roles
    $permissionsNotAssigned = $permissions->filter(function ($permission) use ($assignedPermissions) {
        return !$assignedPermissions->contains($permission->id);
    });

    return view('admin.users.edit', compact(
        'user', 'roles', 'permissionsNotAssigned', 'userRoleIds', 'userDirectPerms'
    ));
}


    // Update roles for the user
    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles'   => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // Sync the roles for the user
        $user->roles()->sync($request->roles ?? []);

        return back()->with('success', 'User roles updated.');
    }

    // Update permissions for the user
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'types' => ['array'],
        ]);

        $types = $request->types ?? [];

        $syncData = [];
        foreach ($types as $permId => $type) {
            if (in_array($type, ['allow', 'deny'])) {
                $syncData[$permId] = ['type' => $type];
            }
        }

        // Sync the direct permissions (new ones) for the user
        $user->directPermissions()->sync($syncData);

        return back()->with('success', 'User direct permissions updated.');
    }
}
