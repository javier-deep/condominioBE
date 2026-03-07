<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREANDO TU USUARIO ===\n";

$email = 'moralesriosgerardojaviermorale@gmail.com';
$password = 'gerardo123';

// Verificar si ya existe
$existingUser = User::where('email', $email)->first();
if ($existingUser) {
    echo "Usuario ya existe, actualizando contraseña...\n";
    $existingUser->password = Hash::make($password);
    $existingUser->save();
    $user = $existingUser;
} else {
    // Crear nuevo usuario
    $user = User::create([
        'name' => 'Gerardo Javier Morales',
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified_at' => now(),
        'phone' => '123-456-7890',
        'address' => 'Tu dirección'
    ]);
    echo "Usuario creado exitosamente!\n";
}

// Asignar rol de residente
$residentRole = Role::where('name', 'residente')->first();
if ($residentRole && !$user->roles->contains($residentRole)) {
    $user->roles()->attach($residentRole->id);
    echo "Rol de residente asignado!\n";
}

echo "\n✅ LISTO PARA INICIAR SESIÓN:\n";
echo "Email: $email\n";
echo "Contraseña: $password\n";