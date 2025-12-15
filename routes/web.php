<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\AccessControlController;
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

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware('permission:view_dashboard')
            ->name('dashboard');

        // ✅ Combined Access Control page (tabs)
      Route::get('access-control', [AccessControlController::class, 'index'])
    ->name('access-control.index');
        // Roles CRUD
        Route::resource('roles', RoleController::class)
            ->except(['show'])
            ->middleware('permission:manage_roles');

        // Role permissions assign page
        Route::get('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])
            ->middleware('permission:manage_roles')
            ->name('roles.permissions');

        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->middleware('permission:manage_roles')
            ->name('roles.updatePermissions');

        // Permissions CRUD
        Route::resource('permissions', PermissionController::class)
            ->except(['show'])
            ->middleware('permission:manage_permissions');

        Route::post('permissions/bulk-store', [PermissionController::class, 'bulkStore'])
            ->middleware('permission:manage_permissions')
            ->name('permissions.bulkStore');

        Route::put('permissions/{permission}/ajax-update', [PermissionController::class, 'ajaxUpdate'])
            ->middleware('permission:manage_permissions')
            ->name('permissions.ajaxUpdate');

        // ✅ These two are REQUIRED because your edit page calls them
        Route::post('users/{user}/roles', [UserAccessController::class, 'updateRoles'])
            ->middleware('permission:manage_users')
            ->name('users.roles.update');

        Route::post('users/{user}/permissions', [UserAccessController::class, 'updatePermissions'])
            ->middleware('permission:manage_users')
            ->name('users.permissions.update');

        // User Access
        Route::resource('users', UserAccessController::class)
            ->names('users')
            ->middleware('permission:manage_users');
    });


