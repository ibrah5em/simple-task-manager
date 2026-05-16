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
            ['name' => 'Admin',  'email' => 'admin@example.com',  'phone' => '0500000001', 'role' => 'admin'],
            ['name' => 'User 1', 'email' => 'user1@example.com',  'phone' => '0500000002', 'role' => 'user'],
            ['name' => 'User 2', 'email' => 'user2@example.com',  'phone' => '0500000003', 'role' => 'user'],
            ['name' => 'User 3', 'email' => 'user3@example.com',  'phone' => '0500000004', 'role' => 'user'],
            ['name' => 'User 4', 'email' => 'user4@example.com',  'phone' => '0500000005', 'role' => 'user'],
            ['name' => 'User 5', 'email' => 'user5@example.com',  'phone' => '0500000006', 'role' => 'user'],
        ];

        foreach ($users as $data) {
            User::create(array_merge($data, ['password' => Hash::make('password123')]));
        }
    }
}
