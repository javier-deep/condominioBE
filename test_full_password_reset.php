<?php

// Test completo de reset de contraseña con código
$resetUrl = 'http://127.0.0.1:8000/api/password/reset';

// Usar el código que obtuvimos de la base de datos
$data = [
    'email' => 'moralesriosgerardojaviermorale@gmail.com',
    'code' => '638704', // El código que vimos en la base de datos
    'password' => 'nuevapassword123',
    'password_confirmation' => 'nuevapassword123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $resetUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

echo "=== TEST DE RESET CON CÓDIGO ===\n";
echo "Email: " . $data['email'] . "\n";
echo "Código: " . $data['code'] . "\n";
echo "Nueva contraseña: " . $data['password'] . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "HTTP Status: $httpCode\n";
echo "Respuesta:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    echo "✅ ¡CONTRASEÑA ACTUALIZADA EXITOSAMENTE!\n";
    echo "🔄 Ahora puedes probar el login con la nueva contraseña\n";
} else {
    echo "❌ Error en el reset de contraseña\n";
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

// Test de login con la nueva contraseña
echo "\n=== TEST DE LOGIN CON NUEVA CONTRASEÑA ===\n";

$loginUrl = 'http://127.0.0.1:8000/api/login';
$loginData = [
    'email' => 'moralesriosgerardojaviermorale@gmail.com',
    'password' => 'nuevapassword123'
];

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $loginUrl);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch2, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch2);
$loginHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);

echo "HTTP Status: $loginHttpCode\n";
echo "Respuesta:\n";
print_r(json_decode($loginResponse, true));

if ($loginHttpCode === 200) {
    echo "\n✅ ¡LOGIN EXITOSO CON NUEVA CONTRASEÑA!\n";
} else {
    echo "\n❌ Error en login con nueva contraseña\n";
}

curl_close($ch2);