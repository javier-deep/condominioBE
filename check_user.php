<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $existing = User::where('email', 'moralesriosgerardojaviermorale@gmail.com')->first();
    
    if ($existing) {
        echo "Usuario ya existe: Gerardo Javier Morales\n";
        echo "ID: " . $existing->id . "\n";
        echo "Email: " . $existing->email . "\n";
    } else {
        $user = User::create([
            'name' => 'Gerardo Javier Morales',
            'email' => 'moralesriosgerardojaviermorale@gmail.com',
            'password' => Hash::make('gerardo123'),
            'phone' => '123-456-7890',
            'address' => 'Tu dirección',
            'is_active' => true
        ]);
        
        echo "Usuario creado exitosamente:\n";
        echo "ID: " . $user->id . "\n";
        echo "Nombre: " . $user->name . "\n";
        echo "Email: " . $user->email . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}