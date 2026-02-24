<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RolesSeeder::class,
        ]);

        // Create test users
        $adminUser = User::factory()->create([
            'name' => 'Admin Usuario',
            'email' => 'admin@condominio.com',
            'password' => 'password123',
            'apartment_number' => 'Admin',
            'phone' => '+1 234 567 8900',
            'email_verified_at' => now(),
        ]);

        $managerUser = User::factory()->create([
            'name' => 'Manager Usuario',
            'email' => 'manager@condominio.com',
            'password' => 'password123',
            'apartment_number' => '100',
            'phone' => '+1 234 567 8901',
            'email_verified_at' => now(),
        ]);

        $residentUser = User::factory()->create([
            'name' => 'Residente Usuario',
            'email' => 'residente@condominio.com',
            'password' => 'password123',
            'apartment_number' => '201',
            'phone' => '+1 234 567 8902',
            'email_verified_at' => now(),
        ]);

        // Assign roles
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $managerRole = \App\Models\Role::where('name', 'manager')->first();
        $residentRole = \App\Models\Role::where('name', 'resident')->first();

        if ($adminRole) $adminUser->roles()->attach($adminRole);
        if ($managerRole) $managerUser->roles()->attach($managerRole);
        if ($residentRole) $residentUser->roles()->attach($residentRole);
    }
}
