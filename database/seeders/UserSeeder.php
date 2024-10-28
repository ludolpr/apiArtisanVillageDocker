<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name_user' => 'Ludolpr',
            'password' => Hash::make('Ll11oxyuuddoo-'),
            'email' => 'ludolpr@gmail.com',
            'email_verified_at' => now(),
            'picture_user' => 'user.jpg',
            'remember_token' => Str::random(10),
            'id_role' => 3,
        ]);
        User::create([
            'name_user' => 'Standard User',
            'password' => Hash::make('password'),
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'picture_user' => 'user.jpg',
            'id_role' => 1,
        ]);

        User::create([
            'name_user' => 'Artisan User',
            'password' => Hash::make('password'),
            'email' => 'artisan@example.com',
            'email_verified_at' => now(),
            'picture_user' => 'user.jpg',
            'id_role' => 2,
        ]);
        // Création de 8 utilisateurs aléatoires
        // User::factory(8)->create();
    }
}