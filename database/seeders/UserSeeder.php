<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'security_question' => 'What is your favorite color?',
            'security_answer' => Hash::make('blue'),
        ]);

        // Accountant User
        User::create([
            'name' => 'Accountant User',
            'email' => 'accountant@example.com',
            'password' => Hash::make('password'),
            'role' => 'accountant',
            'security_question' => 'What city were you born in?',
            'security_answer' => Hash::make('london'),
        ]);

        // Employee User
        User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'security_question' => 'What was your first pet\'s name?',
            'security_answer' => Hash::make('buddy'),
        ]);

        // Optional: Add more employees
        User::factory(3)->create([
            'role' => 'employee'
        ]);
    }
}
