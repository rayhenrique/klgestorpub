<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ray Henrique',
            'email' => 'rayhenrique@gmail.com',
            'password' => Hash::make('1508rcrc'),
            'role' => 'admin',
        ]);
    }
}
