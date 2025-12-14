<?php
namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    // Display the list of permissions
public function index()
{
    $permissions = Permission::withCount('roles')
        ->orderBy('id', 'desc')
        ->paginate(40);

    $permissionGroups = Permission::whereNotNull('group')
        ->where('group', '!=', '')
        ->distinct()
        ->orderBy('group')
        ->pluck('group');

    return view('admin.permissions.index', compact('permissions', 'permissionGroups'));
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
        'group' => 'nullable|string|max:255',
        'name' => 'required|string|max:255|unique:permissions,name',
        'slug' => 'nullable|string|max:255|unique:permissions,slug',
        'description' => 'nullable|string',
    ]);

    Permission::create([
        'group' => $request->group,
        'name' => $request->name,
        'slug' => $request->slug ?: Str::slug($request->name, '_'),
        'description' => $request->description,
    ]);

    return redirect()->route('admin.permissions.index')
        ->with('success', 'Permission created successfully!');
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
        'group' => 'nullable|string|max:255',
        'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $permission->id,
        'description' => 'nullable|string',
    ]);

    $slug = $request->slug ?: Str::slug($request->name, '_');

    $permission->update([
        'group' => $request->group,
        'name' => $request->name,
        'slug' => $slug,
        'description' => $request->description,
    ]);

    return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully!');
}


    // Delete the specified permission
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully!');
    }

    public function bulkStore(Request $request)
{
    $request->validate([
        'group' => 'required|string|max:255',
        'permissions' => 'required|array|min:1',
        'permissions.*' => 'required|string|max:255',
    ]);

    foreach ($request->permissions as $permName) {
        $slug = Str::slug($permName, '_');

        Permission::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $permName,
                'group' => $request->group,
            ]
        );
    }

    return redirect()
        ->route('admin.permissions.index')
        ->with('success', 'Permissions added successfully.');
}

public function ajaxUpdate(Request $request, Permission $permission)
{
    $request->validate([
        'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $permission->id,
        'group' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ]);

    $permission->update($request->only('name', 'slug', 'group', 'description'));

    return response()->json([
        'success' => true,
        'message' => 'Permission updated successfully.',
        'permission' => [
            'id' => $permission->id,
            'name' => $permission->name,
            'slug' => $permission->slug,
            'group' => $permission->group,
            'roles_count' => $permission->roles()->count(),
        ]
    ]);
}


}
