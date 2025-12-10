<?php
// app/Http/Controllers/Admin/UserAccessController.php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserAccessController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'directPermissions')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles       = Role::all();
        $permissions = Permission::all();

        $userRoleIds     = $user->roles->pluck('id')->toArray();
        $userDirectPerms = $user->directPermissions->keyBy('id'); // includes pivot->type

        return view('admin.users.edit', compact(
            'user', 'roles', 'permissions', 'userRoleIds', 'userDirectPerms'
        ));
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles'   => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->roles()->sync($request->roles ?? []);

        return back()->with('success', 'User roles updated.');
    }

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

        $user->directPermissions()->sync($syncData);

        return back()->with('success', 'User direct permissions updated.');
    }
}
    