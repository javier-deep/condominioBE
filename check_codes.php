<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PasswordResetCode;

echo "=== CÓDIGOS DE RECUPERACIÓN GENERADOS ===\n\n";

$codes = PasswordResetCode::orderBy('created_at', 'desc')->get();

if ($codes->isEmpty()) {
    echo "❌ No hay códigos en la base de datos\n";
} else {
    foreach ($codes as $code) {
        echo "📧 Email: {$code->email}\n";
        echo "🔑 Código: {$code->code}\n";
        echo "⏰ Expira: {$code->expires_at}\n";
        echo "📅 Creado: {$code->created_at}\n";
        echo "🔴 " . ($code->hasExpired() ? "EXPIRADO" : "VÁLIDO") . "\n";
        echo "---\n";
    }
}