<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require_once __DIR__ . '/vendor/autoload.php';

echo "=== DIAGNÓSTICO COMPLETO DEL SISTEMA ===\n\n";

try {
    // Configurar Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "✅ Laravel inicializado correctamente\n";

    // 1. Verificar configuración básica
    echo "\n1. CONFIGURACIÓN BÁSICA:\n";
    echo "APP_KEY: " . (env('APP_KEY') ? 'Configurada' : 'NO CONFIGURADA') . "\n";
    echo "DB_CONNECTION: " . env('DB_CONNECTION') . "\n";
    echo "SESSION_DRIVER: " . env('SESSION_DRIVER') . "\n";
    
    // 2. Verificar base de datos
    echo "\n2. BASE DE DATOS:\n";
    try {
        $userCount = User::count();
        echo "✅ Conexión DB exitosa - Usuarios: $userCount\n";
        
        if ($userCount > 0) {
            $users = User::all(['name', 'email']);
            foreach ($users as $user) {
                echo "   - {$user->name} ({$user->email})\n";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error DB: " . $e->getMessage() . "\n";
    }

    // 3. Test de autenticación directa
    echo "\n3. TEST DE AUTENTICACIÓN:\n";
    $email = 'moralesriosgerardojaviermorale@gmail.com';
    $password = 'gerardo123';
    
    $user = User::where('email', $email)->first();
    if ($user) {
        $passwordCheck = Hash::check($password, $user->password);
        echo "✅ Usuario encontrado: {$user->name}\n";
        echo ($passwordCheck ? "✅" : "❌") . " Contraseña: " . ($passwordCheck ? "Correcta" : "Incorrecta") . "\n";
        
        // Test de sesión
        try {
            $sessionId = session()->getId();
            echo "✅ Sesión ID generado: " . substr($sessionId, 0, 10) . "...\n";
        } catch (Exception $e) {
            echo "❌ Error de sesión: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Usuario no encontrado\n";
    }

    // 4. Verificar rutas API
    echo "\n4. RUTAS API DISPONIBLES:\n";
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $apiRoutes = [];
    foreach ($routes as $route) {
        if (str_starts_with($route->uri(), 'api/')) {
            $apiRoutes[] = $route->methods()[0] . ' ' . $route->uri();
        }
    }
    
    if (count($apiRoutes) > 0) {
        echo "✅ Rutas API encontradas:\n";
        foreach (array_slice($apiRoutes, 0, 5) as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "❌ No se encontraron rutas API\n";
    }

} catch (Exception $e) {
    echo "\n❌ ERROR FATAL:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace básico:\n" . substr($e->getTraceAsString(), 0, 500) . "...\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";