<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'ari'],
            [
                'name' => 'Ari',
                'email' => null,
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );

        User::firstOrCreate(
            ['username' => 'budi'],
            [
                'name' => 'Budi',
                'email' => null,
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );

        User::firstOrCreate(
            ['username' => 'pemilik'],
            [
                'name' => 'Padang Motor',
                'email' => null,
                'password' => Hash::make('password'),
                'role' => 'pemilik',
            ]
        );
    }
}
