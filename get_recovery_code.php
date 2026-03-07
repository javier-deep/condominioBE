<?php
// Herramienta para obtener el código de recuperación desde los logs
echo "=== OBTENER CÓDIGO DE RECUPERACIÓN ===\n\n";

$logFile = 'storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Archivo de log no encontrado: $logFile\n";
    echo "Asegúrate de haber solicitado el código primero.\n";
    exit;
}

echo "🔍 Buscando código de recuperación en los logs...\n\n";

// Leer el archivo de log
$logContent = file_get_contents($logFile);

// Buscar códigos de 6 dígitos en los últimos logs
preg_match_all('/\b(\d{6})\b/', $logContent, $matches);

if (!empty($matches[1])) {
    // Tomar los códigos más recientes
    $codes = array_unique(array_reverse($matches[1]));
    
    echo "📋 CÓDIGOS ENCONTRADOS (más reciente primero):\n";
    echo str_repeat("-", 40) . "\n";
    
    foreach (array_slice($codes, 0, 5) as $i => $code) {
        echo ($i + 1) . ". 🔑 Código: $code\n";
    }
    
    $latestCode = $codes[0];
    
    echo "\n✅ Código más reciente: $latestCode\n";
    echo "\n💡 INSTRUCCIONES:\n";
    echo "1. Ve a la pantalla de recuperación\n";
    echo "2. Ingresa tu email: admin@condominio.com\n";
    echo "3. Usa el código: $latestCode\n";
    echo "4. Establece tu nueva contraseña\n";
    
} else {
    echo "❌ No se encontraron códigos en los logs\n";
    echo "\n🔧 SOLUCIÓN:\n";
    echo "1. Ve al frontend y solicita un nuevo código\n";
    echo "2. Ejecuta este script nuevamente\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "INFO SOBRE EMAILS\n";
echo str_repeat("=", 50) . "\n";

echo "\n📧 MODO ACTUAL: Desarrollo (logs)\n";
echo "Los emails se guardan en: storage/logs/laravel.log\n";

echo "\n🔄 PARA RECIBIR EMAILS REALES:\n";
echo "Edita el archivo .env y cambia:\n";
echo "\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";  
echo "MAIL_USERNAME=tu-email@gmail.com\n";
echo "MAIL_PASSWORD=tu-app-password\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=tu-email@gmail.com\n";
echo "MAIL_FROM_NAME=\"Portal Condominio\"\n";

echo "\n💡 NOTA: Para Gmail necesitas una 'App Password'\n";
echo "Ve a: Cuenta Google > Seguridad > Verificación en 2 pasos > Contraseñas de aplicaciones\n";

// También verificar la base de datos
echo "\n" . str_repeat("=", 50) . "\n";
echo "VERIFICACIÓN EN BASE DE DATOS\n";
echo str_repeat("=", 50) . "\n";

try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=condominio_db', 'postgres', '1234');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT email, code, created_at, expires_at FROM password_reset_codes ORDER BY created_at DESC LIMIT 3");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($results) {
        echo "\n📊 CÓDIGOS EN BASE DE DATOS:\n";
        foreach ($results as $row) {
            $expired = strtotime($row['expires_at']) < time() ? '⚠️ EXPIRADO' : '✅ VÁLIDO';
            echo "Email: {$row['email']}\n";
            echo "Código: {$row['code']} ($expired)\n";  
            echo "Creado: {$row['created_at']}\n";
            echo "Expira: {$row['expires_at']}\n";
            echo str_repeat("-", 30) . "\n";
        }
    } else {
        echo "❌ No hay códigos en la base de datos\n";
        echo "Solicita un nuevo código desde el frontend\n";
    }
    
} catch (Exception $e) {
    echo "⚠️ No se pudo conectar a la base de datos\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 RESUMEN RÁPIDO:\n";
echo "1. Solictaste código ✅\n";
echo "2. Sistema envió a logs (no email real) ✅\n";
echo "3. Código disponible arriba ☝️\n";
echo "4. Úsalo en la app para cambiar contraseña ✅\n";