<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario de prueba con email verificado
        $testUser = User::updateOrCreate(
            ['email' => 'admin@condominio.com'],
            [
                'name' => 'Admin Usuario',
                'email' => 'admin@condominio.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(), // Email verificado
                'is_active' => true,
                'phone' => '1234567890',
                'apartment_number' => 'A101',
                'address' => 'Condominio Central'
            ]
        );

        // Asignar rol de admin si existe
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$testUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $testUser->roles()->attach($adminRole);
        }

        echo "Usuario de prueba creado:\n";
        echo "Email: admin@condominio.com\n";
        echo "Password: password123\n";
        echo "Email verificado: " . ($testUser->email_verified_at ? 'Sí' : 'No') . "\n";
        
        // Crear usuario residente también
        $residentUser = User::updateOrCreate(
            ['email' => 'residente@condominio.com'],
            [
                'name' => 'Usuario Residente',
                'email' => 'residente@condominio.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(), // Email verificado
                'is_active' => true,
                'phone' => '0987654321',
                'apartment_number' => 'B205',
                'address' => 'Condominio Central'
            ]
        );

        $residentRole = Role::where('name', 'resident')->first();
        if ($residentRole && !$residentUser->roles()->where('role_id', $residentRole->id)->exists()) {
            $residentUser->roles()->attach($residentRole);
        }

        echo "Usuario residente creado:\n";
        echo "Email: residente@condominio.com\n";
        echo "Password: password123\n";
        echo "Email verificado: " . ($residentUser->email_verified_at ? 'Sí' : 'No') . "\n";
    }
}