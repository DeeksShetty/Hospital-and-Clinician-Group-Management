<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create admin user
         User::updateOrCreate(
            ['email' => 'admin@eg.com'], // unique check
            [
                'name' => 'Admin User',
                'email' => 'admin@eg.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        //create member user
        User::updateOrCreate(
            ['email' => 'member@eg.com'], // unique check
            [
                'name' => 'Member User',
                'email' => 'member@eg.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'member',
            ]
        );
    }
}
