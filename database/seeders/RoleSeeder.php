<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@taskcampus.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Lecturer User
        User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@taskcampus.com',
            'password' => Hash::make('password'),
            'role' => 'lecturer',
        ]);

        // Create Student User
        User::create([
            'name' => 'Student User',
            'email' => 'student@taskcampus.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}