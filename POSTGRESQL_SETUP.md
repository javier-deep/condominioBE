# Configuración de PostgreSQL para el Proyecto Condominio

## Requisitos
- PostgreSQL 12+ instalado en tu sistema
- PHP con extensión pgsql habilitada

## Instalación de PostgreSQL

### Windows
1. Descargar PostgreSQL desde: https://www.postgresql.org/download/windows/
2. Ejecutar el instalador y seguir las instrucciones
3. Recordar el password del usuario `postgres`

### Pasos de Configuración

1. **Crear la base de datos:**
   ```sql
   -- Conectarse a PostgreSQL como usuario postgres
   psql -U postgres
   
   -- Crear la base de datos
   CREATE DATABASE condominio_db;
   
   -- Crear un usuario específico (opcional)
   CREATE USER condominio_user WITH PASSWORD 'tu_password_aqui';
   GRANT ALL PRIVILEGES ON DATABASE condominio_db TO condominio_user;
   ```

2. **Actualizar el archivo .env:**
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=condominio_db
   DB_USERNAME=postgres
   DB_PASSWORD=tu_password_de_postgres
   ```

3. **Ejecutar las migraciones:**
   ```bash
   php artisan migrate
   ```

4. **Verificar la conexión:**
   ```bash
   php artisan tinker
   # En el shell de Tinker:
   DB::connection()->getPdo();
   ```

## Extensiones PHP Necesarias

Asegúrate de que las siguientes extensiones estén habilitadas en tu `php.ini`:
- `extension=pdo_pgsql`
- `extension=pgsql`

## Solución de Problemas Comunes

### Error: "could not find driver"
- Verificar que la extensión `pdo_pgsql` esté habilitada
- Reiniciar el servidor web después de modificar `php.ini`

### Error de conexión
- Verificar que PostgreSQL esté ejecutándose
- Verificar credenciales en el archivo `.env`
- Verificar que el puerto 5432 esté disponible

### Error: "database does not exist"
- Crear la base de datos manualmente como se describe arriba
- Verificar el nombre de la base de datos en `.env`

## Migración desde SQLite (si es necesario)

Si tienes datos existentes en SQLite, puedes usar herramientas como:
- `pgloader` para migración automática
- Exportar/importar datos manualmente usando Laravel seeders

## Comandos Útiles

```bash
# Verificar estado de migraciones
php artisan migrate:status

# Ejecutar migraciones
php artisan migrate

# Rollback de migraciones
php artisan migrate:rollback

# Rebuild completo
php artisan migrate:fresh --seed
```