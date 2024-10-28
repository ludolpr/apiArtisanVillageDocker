<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['id' => 1, 'name_role' => 'utilisateur']);
        Role::create(['id' => 2, 'name_role' => 'artisan']);
        Role::create(['id' => 3, 'name_role' => 'administrateur']);
    }
}