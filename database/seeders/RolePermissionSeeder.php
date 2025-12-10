<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Roles
        $roles = [
            'Super Admin' => 'superadmin',
            'Admin'       => 'admin',
            'User'        => 'user',
            'Student'     => 'student',
            'Teacher'     => 'teacher',
        ];

        foreach ($roles as $name => $slug) {
            Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        // 2) Permissions / operations (example list)
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'view_dashboard',
            'manage_courses',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm],
                ['name' => Str::headline($perm)]
            );
        }

        // 3) Attach permissions to roles
        $superadmin = Role::where('slug', 'superadmin')->first();
        $admin      = Role::where('slug', 'admin')->first();
        $user       = Role::where('slug', 'user')->first();

        $allPermissions = Permission::all();

        // Superadmin → all permissions
        $superadmin->permissions()->sync($allPermissions->pluck('id'));

        // Admin → some permissions
        $adminPermissions = $allPermissions->whereIn('slug', [
            'view_dashboard',
            'manage_users',
            'manage_courses',
        ]);
        $admin->permissions()->sync($adminPermissions->pluck('id'));

        // User → just view dashboard
        $userPermissions = $allPermissions->whereIn('slug', [
            'view_dashboard',
        ]);
        $user->permissions()->sync($userPermissions->pluck('id'));
    }
}