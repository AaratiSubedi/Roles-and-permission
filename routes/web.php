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

Route::middleware(['auth', 'role:superadmin|admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Roles CRUD
        Route::resource('roles', RoleController::class)->except(['show']);
        // Assign permissions to a role
         Route::get('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.permissions');
         Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');



        // Permissions CRUD
        Route::resource('permissions', PermissionController::class)->except(['show']);

        // User access (roles + direct permissions)
        Route::get('users', [UserAccessController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [UserAccessController::class, 'edit'])->name('users.edit');
        Route::post('users/{user}/roles', [UserAccessController::class, 'updateRoles'])->name('users.roles.update');
        Route::post('users/{user}/permissions', [UserAccessController::class, 'updatePermissions'])->name('users.permissions.update');
    });
