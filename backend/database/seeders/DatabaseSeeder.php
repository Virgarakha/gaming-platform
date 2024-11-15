<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin users
        User::create([
            'username' => 'admin1',
            'password' => Hash::make('hellouniverse1!'),
            'role' => 'admin'
        ]);

        User::create([
            'username' => 'admin2',
            'password' => Hash::make('hellouniverse2!'),
            'role' => 'admin'
        ]);

        // Developer users
        User::create([
            'username' => 'dev1',
            'password' => Hash::make('hellobyte1!'),
            'role' => 'developer'
        ]);

        User::create([
            'username' => 'dev2',
            'password' => Hash::make('hellobyte2!'),
            'role' => 'developer'
        ]);

        // Player users
        User::create([
            'username' => 'player1',
            'password' => Hash::make('helloworld1!'),
            'role' => 'player'
        ]);

        User::create([
            'username' => 'player2',
            'password' => Hash::make('helloworld2!'),
            'role' => 'player'
        ]);
    }
}
