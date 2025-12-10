<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AuthPermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
 public function boot()
{
Blade::if('role', function ($role) {
$user = Auth::user();
return $user && $user->hasRole($role);
});


Blade::if('permission', function ($perm) {
$user = Auth::user();
return $user && $user->hasPermission($perm);
});
}
}
