<?php

echo "=== TEST DE LOGIN API ===\n";

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'http://127.0.0.1:8000/api/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode([
        'email' => 'moralesriosgerardojaviermorale@gmail.com',
        'password' => 'gerardo123'
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);

curl_close($curl);

echo "HTTP Status: $httpCode\n";

if ($error) {
    echo "Error cURL: $error\n";
} else {
    echo "Respuesta:\n$response\n\n";
    
    if ($httpCode == 200) {
        echo "✅ ¡LOGIN EXITOSO!\n";
        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            echo "Token generado: " . substr($data['access_token'], 0, 20) . "...\n";
        }
    } else {
        echo "❌ Error en login (HTTP $httpCode)\n";
        
        // Intentar decodificar el error
        $errorData = json_decode($response, true);
        if ($errorData && isset($errorData['message'])) {
            echo "Mensaje: " . $errorData['message'] . "\n";
        }
    }
}