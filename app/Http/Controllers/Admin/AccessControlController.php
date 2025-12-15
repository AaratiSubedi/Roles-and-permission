<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccessControlController extends Controller
{
        public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if (
                !$user ||
                ( !$user->hasPermission('manage_roles') && !$user->hasPermission('manage_permissions') )
            ) {
                abort(403);
            }

            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $tab   = $request->get('tab', 'roles'); // roles | permissions
        $roleQ = $request->get('role_q');
        $permQ = $request->get('perm_q');

        // Roles
        $roles = Role::query()
            ->withCount('users')
            ->withCount('permissions')
            ->with('permissions')
            ->when($roleQ, function ($query) use ($roleQ) {
                $query->where(function ($qq) use ($roleQ) {
                    $qq->where('name', 'like', "%{$roleQ}%")
                       ->orWhere('slug', 'like', "%{$roleQ}%");
                });
            })
            ->orderBy('id', 'asc')
            ->get();

        // Permissions
        $permissions = Permission::query()
            ->withCount('roles')
            ->when($permQ, function ($query) use ($permQ) {
                $query->where(function ($qq) use ($permQ) {
                    $qq->where('name', 'like', "%{$permQ}%")
                       ->orWhere('slug', 'like', "%{$permQ}%")
                       ->orWhere('group', 'like', "%{$permQ}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(40)
            ->withQueryString();

        // For Role modal permissions group list + Permission modal datalist
        $allPermissions = Permission::orderBy('group')->orderBy('name')->get();
        $permissionsGrouped = $allPermissions->groupBy(fn($p) => $p->group ?: 'Other');

        $permissionGroups = Permission::whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        return view('admin.access-control.index', compact(
            'tab',
            'roles',
            'permissions',
            'permissionsGrouped',
            'permissionGroups'
        ));
    }
}

