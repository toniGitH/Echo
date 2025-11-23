# 🚀 Puesta en marcha del proyecto Echo

## 📋 Requisitos previos

- Docker y Docker Compose instalados
- Git instalado
- Sistema operativo: Linux (Ubuntu, Mint, Debian, etc.)

> [!TIP]
> **Recomendado:** Permitir usar Docker sin sudo (solo una vez, a nivel global):
> ```bash
> sudo usermod -aG docker $USER
> ```
> Después de ejecutar esto, cierra sesión y vuelve a iniciarla para que los cambios surtan efecto.

---

## 🆕 Primera vez: configuración inicial

### 1. Clonar el repositorio

```bash
git clone https://github.com/toniGitH/Echo.git
cd Echo
```

Si no puedes clonarlo, puedes hacer un Fork o descargarlo directamente.

---

### 2. Reasignar propiedad de archivos

> [!IMPORTANT]
> **Ejecuta esto ANTES de levantar los contenedores Docker.**
> Es una medida **PREVENTIVA**.
> No es necesario en el 100% de las situaciones, pero hacerlo incluso aunque fuera en un caso innecesario, no daña nada.

```bash
sudo chown -R $USER:$USER ./laravel
```

**¿Qué hace?**
- `chown`: Change Owner (cambiar propietario)
- `-R`: Recursivo (todos los archivos y subdirectorios)
- `$USER:$USER`: Tu usuario y grupo (ej: TuUsuario:TuUsuario)
- Esto asegura que TÚ puedes editar los archivos desde tu IDE sin problemas de permisos

**¿Por qué es necesario?**
- Los archivos clonados pueden tener permisos extraños
- Necesitas ser propietario para editarlos en VS Code, PHPStorm, etc.

---

### 3. Crear archivo `.env`

```bash
cp laravel/.env.example laravel/.env
```

Asegúrate de que el archivo `.env` contenga al menos:

```env
APP_KEY=
APP_URL=http://localhost:8988
```

> [!NOTE]
> **Variables NO necesarias en `.env`:**
> Las siguientes variables se definen en `docker-compose.yml` y tienen prioridad:
> - `APP_ENV`, `APP_DEBUG`
> - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

### 4. Levantar los contenedores

```bash
docker compose up -d --build
```

**¿Qué hace?**
- `up`: Inicia los contenedores
- `-d`: Modo detached (en segundo plano)
- `--build`: Construye las imágenes (necesario la primera vez)

**Verifica que todos los contenedores estén corriendo:**
```bash
docker compose ps
```

> [!TIP]
> Este comando te muestra el estado de todos los contenedores. Asegúrate de que todos muestren `STATUS: Up` antes de continuar. MySQL puede tardar 10-30 segundos en estar listo.

---

### 5. Configurar permisos para Laravel

> [!IMPORTANT]
> **Este es el comando más importante para evitar errores de permisos.**

```bash
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'
```

**¿Qué hace?**
- `chown -R www-data:www-data`: Cambia el propietario a `www-data` (usuario que ejecuta PHP-FPM)
- `chmod -R 775`: Establece permisos de lectura/escritura/ejecución para propietario y grupo
  - `7` (propietario): rwx (leer, escribir, ejecutar)
  - `7` (grupo): rwx (leer, escribir, ejecutar)
  - `5` (otros): r-x (leer, ejecutar)

**¿Por qué es necesario?**
- Laravel necesita escribir en `storage/` (logs, cache, sesiones, uploads)
- Laravel necesita escribir en `bootstrap/cache/` (cache de configuración y rutas)
- Sin estos permisos, verás errores como "Permission denied" al intentar escribir logs

> [!NOTE]
> Después de ejecutar `chown -R $USER:$USER ./laravel`, TODOS los archivos son propiedad de `tuUsuario`. Sin embargo, Laravel se ejecuta dentro del contenedor como el usuario `www-data`, por lo que necesita ser propietario de `storage/` y `bootstrap/cache/` para poder escribir en ellos.

**¿Por qué 775 y no 777?**
- `777` da permisos de escritura a TODOS (inseguro)
- `775` da permisos solo al propietario y grupo (seguro)

---

### 6. Verificar migraciones (automáticas)

> [!IMPORTANT]
> **Las migraciones se ejecutan automáticamente** al levantar los contenedores.
> 
> El contenedor `echo-laravel` ejecuta `php artisan migrate --force` cada vez que se inicia.

**No necesitas hacer nada**, pero si quieres verificar que se ejecutaron correctamente:

```bash
# Ver las migraciones ejecutadas
docker exec echo-php php artisan migrate:status
```

**¿Cuándo ejecutar migraciones manualmente?**

Solo cuando crees una **nueva migración** durante el desarrollo:

```bash
# Opción 1: Reiniciar el contenedor laravel (ejecuta migraciones automáticamente)
docker compose restart laravel

# Opción 2: Ejecutar manualmente
docker exec echo-php php artisan migrate
```

---

### 7. Verificar que todo funciona

Abre tu navegador y ve a:

- **Laravel API**: http://localhost:8988
- **React Frontend**: http://localhost:3000
- **Swagger UI**: http://localhost:8081

---

## 🔄 Uso diario: iniciar el proyecto

### 1️⃣ Empezar a trabajar

```bash
# Desde la raíz del proyecto
docker compose up -d
```

**¡Eso es todo!** Los contenedores se inician y estás listo para trabajar.

---

### 2️⃣ Si creas nuevos archivos

> [!IMPORTANT]
> **¿Necesitas ajustar permisos?**
> 
> - **Archivos creados localmente** (en VS Code): ✅ NO necesitas ajustar permisos
> - **Archivos creados desde contenedores** (con `php artisan make:...`): ⚠️ SÍ necesitas ajustar permisos

**Si creaste archivos desde un contenedor, ejecuta:**

```bash
# 1. Reasignar propiedad a tu usuario
sudo chown -R $USER:$USER ./laravel

# 2. Restaurar permisos de storage y bootstrap/cache
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'
```

---

### 3️⃣ Dejar de trabajar

```bash
docker compose down
```

---

## 🛠️ Solución de problemas

### Problema: Archivos son propiedad de `root`

**Síntoma:** No puedes editar archivos desde tu IDE, o ves que el propietario es `root`.

**Causa:** Ejecutaste comandos como `php artisan make:model` dentro del contenedor.

**Solución:**

```bash
# Paso 1: Reasignar propiedad a tu usuario
sudo chown -R $USER:$USER ./laravel

# Paso 2: Volver a dar permisos a storage y bootstrap/cache
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'
```

---

### Problema: "Permission denied" al escribir logs

**Síntoma:** Error al intentar escribir en `storage/logs/laravel.log`.

**Solución:**

```bash
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'
```

---

### Problema: Cambios en `.env` no se reflejan

**Solución:**

```bash
docker exec echo-php php artisan config:clear
docker exec echo-php php artisan cache:clear
```

---

## 📝 Comandos útiles

### Ejecutar comandos artisan

```bash
# Crear un modelo
docker exec echo-php php artisan make:model NombreModelo

# Crear una migración
docker exec echo-php php artisan make:migration create_tabla_table

# Ejecutar migraciones
docker exec echo-php php artisan migrate

# Rollback de migraciones
docker exec echo-php php artisan migrate:rollback

# Limpiar cache
docker exec echo-php php artisan cache:clear
docker exec echo-php php artisan config:clear
docker exec echo-php php artisan route:clear
docker exec echo-php php artisan view:clear
```

### Ver logs de Laravel

```bash
docker exec echo-php tail -f /var/www/html/storage/logs/laravel.log
```

### Acceder al contenedor PHP

```bash
docker exec -it echo-php sh
```

### Acceder a MySQL

```bash
docker exec -it echo-mysql mysql -u root -proot app
```

---

## 🔐 Permisos: explicación técnica

### ¿Por qué hay problemas de permisos en Docker?

En Linux, los permisos se basan en **UID/GID** (números), no en nombres de usuario:

- Tu usuario en el host (`tuUsuario`) tiene UID **1000** (típico en Ubuntu/Mint)
- El usuario `www-data` dentro del contenedor tiene UID **33**
- Cuando montas `./laravel` en el contenedor, los archivos mantienen el UID del host

**Resultado:** Si un archivo es propiedad de `tuUsuario` (UID 1000) en el host, dentro del contenedor sigue siendo UID 1000, pero `www-data` (UID 33) no puede escribir en él.

### Solución: dos tipos de permisos

1. **Archivos de código** (controllers, models, etc.): Propietario = tu usuario (para editar en IDE)
2. **Directorios de escritura** (`storage/`, `bootstrap/cache/`): Propietario = `www-data` (para que Laravel escriba)

---

## 📦 Estructura de contenedores

| Contenedor | Puerto | Descripción |
|------------|--------|-------------|
| `echo-php` | - | PHP 8.3 + FPM |
| `echo-nginx` | 8988 | Servidor web Nginx |
| `echo-mysql` | 3306 | Base de datos MySQL 8.0 |
| `echo-phpmyadmin` | 8080 | Interfaz web para MySQL |
| `echo-react` | 3000 | Frontend React (dev server) |
| `echo-swagger-ui` | 8081 | Documentación API Swagger |
| `echo-swagger-builder` | - | Compilador de OpenAPI |

---

## 🎯 Resumen de comandos esenciales

**Primera vez (configuración inicial):**
```bash
git clone https://github.com/toniGitH/Laravel-React-Docker-Template.git
cd Laravel-React-Docker-Template
sudo chown -R $USER:$USER ./laravel
cp laravel/.env.example laravel/.env
docker compose up -d --build
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'

# Opcional: Verificar que las migraciones se ejecutaron
docker exec echo-php php artisan migrate:status
```

> [!NOTE]
> **APP_KEY y migraciones se generan automáticamente** gracias al contenedor `echo-laravel`.

**Uso diario:**
```bash
docker compose up -d    # Iniciar
docker compose down     # Detener
```

**Si archivos son de root:**
```bash
sudo chown -R $USER:$USER ./laravel
docker exec echo-php sh -c 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache'
```

---

## 📚 Recursos adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Docker](https://docs.docker.com/)
- [Documentación de React](https://react.dev/)

---

**¿Problemas?** Revisa la sección de "Solución de Problemas" o consulta los logs:
```bash
docker compose logs -f
```
