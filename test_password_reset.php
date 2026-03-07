<?php

// Test de recuperación de contraseña
$url = 'http://127.0.0.1:8000/api/password/email';

$data = [
    'email' => 'moralesriosgerardojaviermorale@gmail.com'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

echo "=== TEST DE RECUPERACIÓN DE CONTRASEÑA ===\n";
echo "Enviando solicitud de código a: " . $data['email'] . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Status: $httpCode\n";
echo "Respuesta:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    echo "✅ ¡SOLICITUD EXITOSA!\n";
    echo "Mensaje: " . ($responseData['message'] ?? 'Email enviado') . "\n";
    echo "\n📧 Revisa tu email para el código de 6 dígitos\n";
    echo "📝 Nota: El código expira en 15 minutos\n";
} else {
    echo "❌ Error en recuperación de contraseña\n";
    $errorData = json_decode($response, true);
    if (isset($errorData['errors'])) {
        foreach ($errorData['errors'] as $field => $messages) {
            foreach ($messages as $message) {
                echo "   - $field: $message\n";
            }
        }
    }
}

curl_close($ch);