<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'name'      => 'Admin User',
            'email'     => 'admin@sms.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        // Create Teacher
        User::create([
            'name'      => 'Teacher One',
            'email'     => 'teacher@sms.com',
            'password'  => Hash::make('password'),
            'role'      => 'teacher',
            'is_active' => true,
        ]);

        // Create Student
        User::create([
            'name'      => 'Student One',
            'email'     => 'student@sms.com',
            'password'  => Hash::make('password'),
            'role'      => 'student',
            'is_active' => true,
        ]);
    }
}