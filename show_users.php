<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== USUARIOS ACTUALES EN LA BASE DE DATOS ===\n\n";

$users = User::all();

if ($users->isEmpty()) {
    echo "❌ No hay usuarios en la base de datos\n";
} else {
    echo "Total de usuarios: " . $users->count() . "\n\n";
    
    foreach ($users as $user) {
        echo "👤 ID: {$user->id}\n";
        echo "📝 Nombre: {$user->name}\n";
        echo "📧 Email: {$user->email}\n";
        echo "🏠 Apartamento: " . ($user->apartment_number ?? 'N/A') . "\n";
        echo "📞 Teléfono: " . ($user->phone ?? 'N/A') . "\n";
        echo "📅 Creado: {$user->created_at}\n";
        echo "✅ Verificado: " . ($user->email_verified_at ? 'Sí' : 'No') . "\n";
        echo "---\n";
    }
}

echo "\n=== CONFIGURACIÓN DE BASE DE DATOS ===\n";
echo "Tipo: " . config('database.default') . "\n";
echo "Archivo: " . database_path('database.sqlite') . "\n";
echo "Existe: " . (file_exists(database_path('database.sqlite')) ? 'Sí' : 'No') . "\n";