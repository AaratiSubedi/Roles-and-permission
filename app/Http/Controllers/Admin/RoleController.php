<?php
namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    // Display the list of roles
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    // Show the form for creating a new role
    public function create()
    {
        return view('admin.roles.create');
    }

    // Store a newly created role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'nullable|string|max:255|unique:roles',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($request->only('name', 'slug', 'description'));

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    // Show the form for editing a role
public function edit(Role $role)
{
    $permissions = Permission::all();

    // Fetch the permission IDs that the role already has
    $rolePermissionIds = $role->permissions->pluck('id')->toArray();

    // Pass role, permissions, and rolePermissionIds to the view
    return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
}

    // Update the specified role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array|exists:permissions,id',
        ]);

        $role->update($request->only('name', 'slug', 'description'));
        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
    }

    // Delete the specified role
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
    }

    public function assignPermissions($roleId)
{
    // Get the role
    $role = Role::findOrFail($roleId);

    // Get all permissions
    $permissions = Permission::all();

    return view('admin.roles.assign_permissions', compact('role', 'permissions'));
}

public function updatePermissions(Request $request, $roleId)
{
    $role = Role::findOrFail($roleId);

    // Sync the permissions with the role (update them)
    $role->permissions()->sync($request->permissions); // Sync selected permissions

    return redirect()->route('admin.roles.index')->with('success', 'Permissions updated successfully.');
}
}
