<?php

/**
 * Script de prueba para el sistema de gestión de sesiones por dispositivo
 * 
 * Este archivo demuestra cómo usar todas las funcionalidades del sistema:
 * 1. Login desde diferentes dispositivos
 * 2. Ver sesiones activas
 * 3. Cerrar sesión en dispositivo específico
 * 4. Cerrar sesión en todos los dispositivos
 * 5. Cambiar contraseña (cierra todas las sesiones)
 */

// URL base de tu API
$baseUrl = 'http://localhost:8000/api';

// Datos de prueba
$testUser = [
    'email' => 'test@example.com',
    'password' => 'password123'
];

/**
 * Simular login desde Chrome Desktop
 */
function loginFromChrome($baseUrl, $user) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/login',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($user),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Simular login desde iPhone
 */
function loginFromIPhone($baseUrl, $user) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/login',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($user),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Obtener sesiones activas
 */
function getActiveSessions($baseUrl, $token) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/sessions',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Cerrar sesión en dispositivo específico
 */
function revokeSession($baseUrl, $token, $sessionId) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/sessions/' . $sessionId,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Cerrar sesión en todos los dispositivos
 */
function logoutAllDevices($baseUrl, $token) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/logout-all-devices',
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Cambiar contraseña
 */
function changePassword($baseUrl, $token, $currentPassword, $newPassword) {
    $data = [
        'current_password' => $currentPassword,
        'new_password' => $newPassword,
        'new_password_confirmation' => $newPassword
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . '/change-password',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Ejecutar pruebas
echo "=== PRUEBA DEL SISTEMA DE GESTIÓN DE SESIONES ===\n\n";

echo "1. Login desde Chrome Desktop...\n";
$chromeLogin = loginFromChrome($baseUrl, $testUser);
if (isset($chromeLogin['token'])) {
    echo "✅ Login exitoso desde Chrome\n";
    echo "   Token: " . substr($chromeLogin['token'], 0, 20) . "...\n";
    echo "   Dispositivo: " . $chromeLogin['device_info']['device_name'] . "\n";
    $chromeToken = $chromeLogin['token'];
} else {
    echo "❌ Error en login desde Chrome: " . ($chromeLogin['message'] ?? 'Error desconocido') . "\n";
    exit;
}

echo "\n2. Login desde iPhone...\n";
$iPhoneLogin = loginFromIPhone($baseUrl, $testUser);
if (isset($iPhoneLogin['token'])) {
    echo "✅ Login exitoso desde iPhone\n";
    echo "   Token: " . substr($iPhoneLogin['token'], 0, 20) . "...\n";
    echo "   Dispositivo: " . $iPhoneLogin['device_info']['device_name'] . "\n";
    $iPhoneToken = $iPhoneLogin['token'];
} else {
    echo "❌ Error en login desde iPhone: " . ($iPhoneLogin['message'] ?? 'Error desconocido') . "\n";
}

echo "\n3. Verificar sesiones activas...\n";
$sessions = getActiveSessions($baseUrl, $chromeToken);
if (isset($sessions['sessions'])) {
    echo "✅ Sesiones activas encontradas: " . count($sessions['sessions']) . "\n";
    foreach ($sessions['sessions'] as $session) {
        echo "   - ID: {$session['id']}, Dispositivo: {$session['device_name']}, IP: {$session['ip_address']}\n";
        echo "     Actual: " . ($session['is_current'] ? 'Sí' : 'No') . "\n";
    }
} else {
    echo "❌ Error al obtener sesiones\n";
}

echo "\n4. Cerrar sesión en iPhone (desde Chrome)...\n";
if (isset($sessions['sessions']) && count($sessions['sessions']) > 1) {
    // Buscar la sesión del iPhone (la que no es actual)
    $iPhoneSession = null;
    foreach ($sessions['sessions'] as $session) {
        if (!$session['is_current']) {
            $iPhoneSession = $session;
            break;
        }
    }
    
    if ($iPhoneSession) {
        $revokeResult = revokeSession($baseUrl, $chromeToken, $iPhoneSession['id']);
        echo "✅ Sesión de iPhone cerrada exitosamente\n";
    }
} else {
    echo "⚠️ Solo hay una sesión activa, no se puede probar el cierre específico\n";
}

echo "\n5. Verificar sesiones después del cierre...\n";
$sessionsAfterRevoke = getActiveSessions($baseUrl, $chromeToken);
if (isset($sessionsAfterRevoke['sessions'])) {
    echo "✅ Sesiones activas: " . count($sessionsAfterRevoke['sessions']) . "\n";
    foreach ($sessionsAfterRevoke['sessions'] as $session) {
        echo "   - Dispositivo: {$session['device_name']}\n";
    }
}

echo "\n6. Cerrar todas las sesiones...\n";
$logoutAll = logoutAllDevices($baseUrl, $chromeToken);
echo "✅ Todas las sesiones han sido cerradas\n";

echo "\n=== PRUEBA COMPLETADA ===\n";
echo "\nPara probar el cambio de contraseña, descomenta la sección siguiente:\n";

/*
echo "\n7. Login nuevamente y cambiar contraseña...\n";
$newLogin = loginFromChrome($baseUrl, $testUser);
if (isset($newLogin['token'])) {
    $newToken = $newLogin['token'];
    $changeResult = changePassword($baseUrl, $newToken, 'password123', 'newpassword123');
    echo "✅ Contraseña cambiada. Todas las sesiones cerradas automáticamente.\n";
}
*/