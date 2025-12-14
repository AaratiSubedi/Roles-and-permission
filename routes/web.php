<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// ===================== ADMIN AREA =====================
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 📌 Dashboard (any role that has 'view_dashboard' can see it)
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware('permission:view_dashboard')
            ->name('dashboard');


        // 📌 Roles CRUD (only users with 'manage_roles')
        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:manage_roles');

        // Assign permissions to a role
        Route::get('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])
            ->middleware('permission:manage_roles')
            ->name('roles.permissions');

        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->middleware('permission:manage_roles')
            ->name('roles.updatePermissions');
        


        // 📌 Permissions CRUD (only users with 'manage_permissions')
        Route::resource('permissions', PermissionController::class)
            ->except(['show'])
            ->middleware('permission:manage_roles');

        Route::post('permissions/bulk-store', [PermissionController::class, 'bulkStore']
        )->name('permissions.bulkStore');
        Route::put('permissions/{permission}/ajax-update', [PermissionController::class, 'ajaxUpdate'])
         ->name('permissions.ajaxUpdate');


        // 📌 User Access (only users with 'manage_users')
        Route::get('users', [UserAccessController::class, 'index'])
            ->middleware('permission:manage_users')
            ->name('users.index');

        Route::get('users/{user}/edit', [UserAccessController::class, 'edit'])
            ->middleware('permission:manage_users')
            ->name('users.edit');

        Route::post('users/{user}/roles', [UserAccessController::class, 'updateRoles'])
            ->middleware('permission:manage_users')
            ->name('users.roles.update');

        Route::post('users/{user}/permissions', [UserAccessController::class, 'updatePermissions'])
            ->middleware('permission:manage_users')
            ->name('users.permissions.update');


        Route::resource('users', UserAccessController::class)
         ->names('users');
    });
