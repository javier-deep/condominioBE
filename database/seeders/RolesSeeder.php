<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del condominio con acceso completo'
            ],
            [
                'name' => 'manager',
                'description' => 'Administrador con permisos limitados'
            ],
            [
                'name' => 'resident',
                'description' => 'Residente del condominio'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
