<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Recuperación</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .code-box {
            background-color: #f8f9fa;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #667eea;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #666;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 Portal del Condominio</h1>
            <p>Código de Recuperación de Contraseña</p>
        </div>
        
        <div class="content">
            <h2>Hola, {{ $name }}!</h2>
            
            <p>Has solicitado recuperar tu contraseña. Usa el siguiente código para continuar:</p>
            
            <div class="code-box">
                {{ $code }}
            </div>
            
            <div class="warning">
                <strong>⚠️ Importante:</strong><br>
                • Este código expira en 15 minutos<br>
                • Solo se puede usar una vez<br>
                • Si no solicitaste este cambio, ignora este email
            </div>
            
            <p>Ingresa este código en la aplicación junto con tu nueva contraseña.</p>
        </div>
        
        <div class="footer">
            <p>Este email fue enviado a: <strong>{{ $email }}</strong></p>
            <p>Portal del Condominio - Sistema de Gestión</p>
            <p>Si tienes problemas, contacta al administrador.</p>
        </div>
    </div>
</body>
</html>
