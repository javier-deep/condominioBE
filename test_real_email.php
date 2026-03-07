<?php
// Test de envío de email real
echo "=== PRUEBA DE ENVÍO DE EMAIL REAL ===\n\n";

// Cargar Laravel
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Mail\PasswordResetCodeMail;
use Illuminate\Support\Facades\Mail;

$testEmail = 'moralesriosgerardojaviermorale@gmail.com';
$testCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

echo "📧 Enviando email de prueba...\n";
echo "📍 Destinatario: $testEmail\n";  
echo "🔑 Código de prueba: $testCode\n\n";

try {
    // Enviar email de prueba
    Mail::to($testEmail)->send(new PasswordResetCodeMail([
        'code' => $testCode,
        'name' => 'Usuario de Prueba',
        'email' => $testEmail
    ]));
    
    echo "✅ EMAIL ENVIADO EXITOSAMENTE!\n\n";
    echo "📱 REVISA TU BANDEJA DE ENTRADA:\n";
    echo "   - Revisa tu carpeta principal\n";
    echo "   - Revisa SPAM/Correo no deseado\n";
    echo "   - Puede tardar hasta 2-3 minutos en llegar\n\n";
    
    echo "🎉 CONFIGURACIÓN SMTP FUNCIONANDO CORRECTAMENTE!\n";
    echo "💡 Ahora puedes usar la recuperación de contraseña normalmente\n";
    
} catch (Exception $e) {
    echo "❌ Error al enviar email:\n";
    echo "📝 Mensaje: " . $e->getMessage() . "\n\n";
    
    echo "🔧 POSIBLES SOLUCIONES:\n";
    echo "1. Verifica que la contraseña de aplicación esté correcta\n";
    echo "2. Asegúrate de tener habilitada la verificación en 2 pasos en Gmail\n";
    echo "3. Verifica que la contraseña sea de aplicación, no la normal\n";
    echo "4. Revisa que no haya espacios extra en la configuración\n\n";
    
    echo "📖 CÓMO GENERAR CONTRASEÑA DE APLICACIÓN:\n";
    echo "1. Ve a tu cuenta de Google\n";
    echo "2. Seguridad > Verificación en 2 pasos\n";
    echo "3. Contraseñas de aplicación\n";
    echo "4. Genera una nueva para 'Mail'\n";
    echo "5. Usa esa contraseña en el .env\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "CONFIGURACIÓN ACTUAL:\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_USERNAME=$testEmail\n";
echo "MAIL_FROM_ADDRESS=$testEmail\n";
echo str_repeat("=", 50) . "\n";