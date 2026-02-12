<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\NominalLocation::factory(3)->create();
        \App\Models\Position::factory(3)->create();
        \App\Models\Personal::factory(10)->create();

        // Create Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'rrhh']);
        Role::create(['name' => 'comedor']);

        $user = \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin1234'),
        ]);

        $user->assignRole('admin');
    }
}
