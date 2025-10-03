<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rayhenrique@gmail.com'],
            [
                'name' => 'Ray Henrique',
                'password' => Hash::make('1508rcrc'),
                'role' => 'admin',
                'active' => true,
            ]
        );
    }
}
