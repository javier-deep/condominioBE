<?php
// Solicitar nuevo código y obtenerlo inmediatamente
echo "=== SOLICITUD AUTOMÁTICA DE CÓDIGO ===\n\n";

$email = 'admin@condominio.com';
$baseUrl = 'http://localhost:8000/api';

echo "📧 Solicitando nuevo código para: $email\n";

// Solicitar código
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $baseUrl . '/password/email',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['email' => $email]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Error de conexión: $error\n";
    echo "🔧 Asegúrate de que el servidor esté ejecutándose: php artisan serve\n";
    exit;
}

if ($httpCode === 200) {
    echo "✅ Código solicitado exitosamente\n\n";
    
    // Esperar un momento para que se escriba en la base de datos
    sleep(1);
    
    // Obtener el código más reciente de la base de datos
    try {
        $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=condominio_db', 'postgres', '1234');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT code, expires_at FROM password_reset_codes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $code = $result['code'];
            $expiresAt = $result['expires_at'];
            
            echo "🔑 TU CÓDIGO DE RECUPERACIÓN: $code\n";
            echo "⏰ Válido hasta: $expiresAt\n";
            echo "📱 Tiempo restante: 15 minutos\n\n";
            
            echo "📋 INSTRUCCIONES:\n";
            echo "1. Ve a la pantalla de recuperación de contraseña\n";
            echo "2. Ingresa tu email: $email\n";
            echo "3. Haz clic en 'Enviar Código'\n";
            echo "4. En la siguiente pantalla, ingresa: $code\n";
            echo "5. Establece tu nueva contraseña\n";
            echo "6. ¡Listo! ✅\n\n";
            
            echo "🎯 CÓDIGO LISTO PARA USAR: $code\n";
            
        } else {
            echo "❌ No se pudo obtener el código de la base de datos\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
    }
    
} else {
    $responseData = json_decode($response, true);
    echo "❌ Error al solicitar código (HTTP $httpCode)\n";
    echo "Respuesta: " . ($responseData['message'] ?? $response) . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "💡 ALTERNATIVA: CONFIGURAR EMAIL REAL\n";
echo str_repeat("=", 50) . "\n";
echo "Para recibir emails reales, edita .env:\n\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=tu-email@gmail.com\n";
echo "MAIL_PASSWORD=tu-app-password-de-gmail\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=tu-email@gmail.com\n";
echo "MAIL_FROM_NAME=\"Portal Condominio\"\n\n";
echo "📝 Nota: Para Gmail, genera una 'Contraseña de aplicación'\n";
echo "🔗 Google Account > Security > 2-factor > App passwords\n";