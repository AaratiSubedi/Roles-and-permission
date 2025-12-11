<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // SuperAdmin
        $super = User::firstOrCreate(
            ['email' => 'super@admin.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );

        // Teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Teacher User', 'password' => Hash::make('password')]
        );

        // Student
        $student = User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'Student User', 'password' => Hash::make('password')]
        );

        // Assign roles
        $super->roles()->sync([Role::where('slug', 'superadmin')->first()->id]);
        $admin->roles()->sync([Role::where('slug', 'admin')->first()->id]);
        $teacher->roles()->sync([Role::where('slug', 'teacher')->first()->id]);
        $student->roles()->sync([Role::where('slug', 'student')->first()->id]);
    }
}
