<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Customer Test',
                'email' => 'customer@laundrygo.test',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ],
            [
                'name' => 'Staff Test',
                'email' => 'staff@laundrygo.test',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Driver Test',
                'email' => 'driver@laundrygo.test',
                'password' => Hash::make('password'),
                'role' => 'driver',
            ],
            [
                'name' => 'Admin Test',
                'email' => 'admin@laundrygo.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}