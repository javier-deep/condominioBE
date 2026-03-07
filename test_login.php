<?php
// Prueba rápida del login
echo "Probando login con usuario admin...\n";

$url = 'http://localhost:8000/api/login';
$data = [
    'email' => 'admin@condominio.com',
    'password' => 'password123'
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome Browser Test'
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
    echo "Asegúrate de que el servidor esté ejecutándose con: php artisan serve\n";
} else {
    echo "HTTP Code: $httpCode\n";
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        echo "✅ Login exitoso!\n";
        echo "Usuario: " . $result['user']['name'] . "\n";
        echo "Email: " . $result['user']['email'] . "\n";
        echo "Dispositivo: " . $result['device_info']['device_name'] . "\n";
        echo "Token: " . substr($result['token'], 0, 30) . "...\n";
    } else {
        echo "❌ Login falló\n";
        echo "Respuesta: $response\n";
    }
}