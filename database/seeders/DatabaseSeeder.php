<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
        ]);

        User::create([
            'name' => 'Society',
            'email' => 'society@example.com',
            'phone' => '081298765432',
            'password' => Hash::make('society123'),
            'role' => 'society',
        ]);
    }
}
