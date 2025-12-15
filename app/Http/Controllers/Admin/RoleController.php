<?php
namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    // Display the list of roles
public function index(Request $request)
{
    return redirect()->route('admin.access-control.index', [
        'tab' => 'roles',
        'role_q' => $request->get('q'), // keep search if any
    ]);
}



    // Show the form for creating a new role
public function create()
{
    $permissionsGrouped = Permission::orderBy('group')
        ->orderBy('name')
        ->get()
        ->groupBy(fn($p) => $p->group ?: 'Other');

    return view('admin.access-control.roles.create', compact('permissionsGrouped'));
}


    // Store a newly created role
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:roles,name',
        'slug' => 'nullable|string|max:255|unique:roles,slug',
        'description' => 'nullable|string',
    ]);

    Role::create([
        'name' => $request->name,
        'slug' => $request->slug ?: Str::slug($request->name),
        'description' => $request->description,
    ]);

    return redirect()
        ->route('admin.access-control.index', ['tab' => 'roles'])
        ->with('success', 'Role created successfully.');
}


    // Show the form for editing a role
// public function edit(Role $role)
// {
//     // Group-wise permissions
//     $permissionsGrouped = Permission::orderBy('group')
//         ->orderBy('name')
//         ->get()
//         ->groupBy(fn($p) => $p->group ?: 'Other');

//     $rolePermissionIds = $role->permissions->pluck('id')->toArray();

//     return view('admin.roles.edit', compact('role', 'permissionsGrouped', 'rolePermissionIds'));
// }

    // Update the specified role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
             'permissions.*' => 'exists:permissions,id',

        ]);

        $role->update($request->only('name', 'slug', 'description'));
        $role->permissions()->sync($request->permissions);

       return redirect()
        ->route('admin.access-control.index', ['tab' => 'roles'])
        ->with('success', 'Role updated successfully.');
}

    // Delete the specified role
public function destroy(Role $role)
{
    if ($role->slug === 'super-admin') {
        return back()->with('error', 'Super Admin role cannot be deleted.');
    }

    $role->delete();
    return redirect()
        ->route('admin.access-control.index', ['tab' => 'roles'])
        ->with('success', 'Role deleted successfully.');}

public function assignPermissions($roleId)
{
    $role = Role::findOrFail($roleId);

    $permissionsGrouped = Permission::orderBy('group')
        ->orderBy('name')
        ->get()
        ->groupBy(fn($p) => $p->group ?: 'Other');

    $rolePermissionIds = $role->permissions->pluck('id')->toArray();

    return view('admin.access-control.roles.assign_permissions', compact('role', 'permissionsGrouped', 'rolePermissionIds'));
}



public function updatePermissions(Request $request, $roleId)
{
    $role = Role::findOrFail($roleId);

    // Sync the permissions with the role (update them)
    $role->permissions()->sync($request->permissions); // Sync selected permissions

    return redirect()->route('admin.access-control.index',['tab' => 'roles'])->with('success', 'Permissions updated successfully.');
}
}
