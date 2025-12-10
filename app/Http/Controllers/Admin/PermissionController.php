<?php
namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    // Display the list of permissions
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    // Show the form for creating a new permission
    public function create()
    {
        return view('admin.permissions.create');
    }

    // Store a newly created permission
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'slug' => 'nullable|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        Permission::create($request->only('name', 'slug', 'description'));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully!');
    }

    // Show the form for editing a permission
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    // Update the specified permission
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update($request->only('name', 'slug', 'description'));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully!');
    }

    // Delete the specified permission
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully!');
    }
}
