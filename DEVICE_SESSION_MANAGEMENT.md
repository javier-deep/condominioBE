# Sistema de Gestión de Sesiones por Dispositivo

## Funcionalidades Implementadas

### 1. Login con Identificación de Dispositivo
- **Endpoint**: `POST /api/login`
- Cada login genera un token único identificado por dispositivo
- Se almacena información del dispositivo: User-Agent, IP, fecha/hora
- Respuesta incluye información del dispositivo actual

#### Ejemplo de respuesta:
```json
{
    "message": "Inicio de sesión exitoso",
    "user": {...},
    "token": "token_string",
    "token_type": "Bearer",
    "device_info": {
        "device_name": "Chrome Browser",
        "ip_address": "192.168.1.100",
        "logged_in_at": "2026-03-07T10:30:00Z"
    }
}
```

### 2. Logout por Dispositivo
- **Endpoint**: `POST /api/logout`
- Cierra sesión solo en el dispositivo actual
- Mantiene activas las sesiones en otros dispositivos

### 3. Logout en Todos los Dispositivos
- **Endpoint**: `POST /api/logout-all-devices`
- Cierra sesión en TODOS los dispositivos
- Elimina todos los tokens del usuario

### 4. Ver Sesiones Activas
- **Endpoint**: `GET /api/sessions`
- Lista todos los dispositivos con sesión activa
- Muestra información detallada de cada dispositivo

#### Ejemplo de respuesta:
```json
{
    "sessions": [
        {
            "id": 1,
            "name": "Chrome Browser - 192.168.1.100 - 2026-03-07 10:30:00",
            "device_name": "Chrome Browser",
            "ip_address": "192.168.1.100",
            "logged_in_at": "2026-03-07T10:30:00Z",
            "last_used_at": "2026-03-07T11:45:00Z",
            "is_current": true
        },
        {
            "id": 2,
            "name": "iPhone - 192.168.1.105 - 2026-03-06 15:20:00",
            "device_name": "iPhone",
            "ip_address": "192.168.1.105",
            "logged_in_at": "2026-03-06T15:20:00Z",
            "last_used_at": "2026-03-07T08:15:00Z",
            "is_current": false
        }
    ]
}
```

### 5. Cerrar Sesión en Dispositivo Específico
- **Endpoint**: `DELETE /api/sessions/{tokenId}`
- Permite cerrar sesión en un dispositivo específico
- No permite cerrar la sesión actual (debe usar `/logout`)

### 6. Cambio de Contraseña con Cierre de Sesiones
- **Endpoint**: `POST /api/change-password`
- Cambia la contraseña del usuario
- **Automáticamente cierra sesión en TODOS los dispositivos**
- Requiere contraseña actual para confirmar

#### Parámetros:
```json
{
    "current_password": "contraseña_actual",
    "new_password": "nueva_contraseña",
    "new_password_confirmation": "nueva_contraseña"
}
```

### 7. Reset de Contraseña con Cierre de Sesiones
- **Endpoint**: `POST /api/password/reset`
- Cuando se resetea la contraseña con código
- **Automáticamente cierra sesión en TODOS los dispositivos**
- Medida de seguridad ante compromiso de cuenta

## Detección de Dispositivos

El sistema identifica automáticamente el tipo de dispositivo basado en el User-Agent:

### Dispositivos Móviles:
- iPhone → "iPhone"
- iPad → "iPad" 
- Android → "Android Device"
- Windows Phone → "Windows Phone"
- Otros móviles → "Mobile Device"

### Navegadores de Escritorio:
- Chrome → "Chrome Browser"
- Firefox → "Firefox Browser"  
- Safari → "Safari Browser"
- Edge → "Edge Browser"
- Opera → "Opera Browser"
- Otros → "Desktop Browser"

## Casos de Uso

### 1. Usuario Normal
```javascript
// Login desde computadora
login() → Token A (Chrome Browser)

// Login desde móvil
login() → Token B (iPhone)

// Ver sesiones activas
getSessions() → [Chrome Browser, iPhone]

// Cerrar sesión solo en móvil
revokeSession(tokenB) → Chrome sigue activo
```

### 2. Cambio de Contraseña
```javascript
// Usuario tiene sesiones en: Chrome, iPhone, iPad
changePassword() → Todos los tokens eliminados
// Usuario debe iniciar sesión nuevamente en todos los dispositivos
```

### 3. Compromiso de Seguridad
```javascript
// Usuario sospecha que alguien más tiene acceso
logoutAllDevices() → Cierra todas las sesiones
// O desde la lista de sesiones, eliminar dispositivos sospechosos
```

## Consideraciones de Seguridad

1. **Rotación Automática**: Al cambiar contraseña, se cierran todas las sesiones
2. **Información del Dispositivo**: Se almacena para identificar accesos
3. **Control Granular**: Permite cerrar sesiones específicas
4. **Prevención de Autorevocación**: No se puede cerrar la sesión actual por error

## Integración en el Frontend

### Headers Requeridos:
```javascript
// Todas las peticiones autenticadas
headers: {
    'Authorization': `Bearer ${token}`,
    'User-Agent': navigator.userAgent // Para identificación de dispositivo
}
```

### Manejo de Respuestas:
- **401 Unauthorized**: Token inválido o expirado, redirigir a login
- **403 Forbidden**: Email no verificado
- **422 Validation Error**: Errores de validación en formularios