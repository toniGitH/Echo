# 🚀 Puesta en marcha del proyecto Echo

> 📝 **NOTA**
>
> Esta guía funciona para **Linux** 🐧, **macOS** 🍎 y **Windows** 🪟.
>
> Algunos pasos son específicos para cada sistema operativo y están claramente marcados con sus respectivos iconos.
>
> Si ves una sección marcada solo para tu sistema operativo, síguelas. Si no, puedes omitirlas.

## 📋 Requisitos previos

- Docker y Docker Compose instalados
- Git instalado
- Sistema operativo: **Linux**, **macOS** o **Windows**

### 🐧 Solo para Linux

> 💡 **RECOMENDACIÓN**
>
> Permitir usar Docker sin sudo (solo una vez, a nivel global):
> ```bash
> sudo usermod -aG docker $USER
> ```
> Después de ejecutar esto, cierra sesión y vuelve a iniciarla para que los cambios surtan efecto.

### 🍎 Solo para macOS

> 💡 **RECOMENDACIÓN**
>
> Asegúrate de tener Docker Desktop instalado y corriendo antes de continuar.

### 🪟 Solo para Windows

> 💡 **RECOMENDACIÓN**
>
> Asegúrate de tener Docker Desktop con WSL2 habilitado antes de continuar.

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

#### 🐧 Solo para Linux

> ⚠️ **IMPORTANTE**
>
> Ejecuta esto ANTES de levantar los contenedores Docker.
>
> Es una medida **PREVENTIVA**, pero **RECOMENDADA**.
>
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

#### 🍎 macOS / 🪟 Windows

> 💡 **CONSEJO**
>
> En macOS y Windows, Docker Desktop maneja los permisos automáticamente.
>
> **No necesitas ejecutar ningún comando de permisos en este paso.**

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

> 📝 **NOTA**
>
>Variables NO necesarias en `.env`:
>
> Las siguientes variables ya se definen en `docker-compose.yml` para el contenedor de Laravel y tienen prioridad sobre las que pudieramos indicar en el archivo `.env`:
>
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

> 💡 **CONSEJO**
>
> Este comando te muestra el estado de todos los contenedores.
>
> Este comando en sí no forma parte del proceso de puesta en marcha del proyecto.
>
> Sólo es para que puedas comprobar que todos los contenedores muestren `STATUS: Up` antes de continuar.
>
> MySQL puede tardar 10-30 segundos en estar listo.

---

### 5. Configurar permisos para Laravel

#### 🐧 Solo para Linux

> ⚠️ **IMPORTANTE**
>
> Este es el comando más importante para evitar errores de permisos.

```bash
docker exec echo-php sh -c '
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
  find /var/www/html/storage -type d -exec chmod 775 {} \; &&
  find /var/www/html/storage -type f -exec chmod 664 {} \; &&
  find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; &&
  find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;
'
```

**¿Qué hace?**
- `chown -R www-data:www-data`: Cambia el propietario a `www-data` (usuario que ejecuta PHP-FPM)
- `find ... -type d -exec chmod 775`: Establece permisos `775` solo para directorios
  - `7` (propietario): rwx (leer, escribir, entrar)
  - `7` (grupo): rwx (leer, escribir, entrar)
  - `5` (otros): r-x (leer, entrar)
- `find ... -type f -exec chmod 664`: Establece permisos `664` solo para archivos
  - `6` (propietario): rw- (leer, escribir)
  - `6` (grupo): rw- (leer, escribir)
  - `4` (otros): r-- (solo leer)

**¿Por qué es necesario?**
- Laravel necesita escribir en `storage/` (logs, cache, sesiones, uploads)
- Laravel necesita escribir en `bootstrap/cache/` (cache de configuración y rutas)
- Sin estos permisos, verás errores como "Permission denied" al intentar escribir logs

**¿Por qué permisos diferentes para directorios y archivos?**
- **Directorios (`775`):** Necesitan permiso de ejecución (`x`) para que Laravel pueda entrar en ellos y crear archivos dentro
- **Archivos (`664`):** NO necesitan permiso de ejecución porque Laravel solo los lee/escribe (logs, cache, sesiones). PHP los interpreta, no los ejecuta directamente como scripts del sistema
- **Principio de mínimos privilegios:** Solo se otorgan los permisos estrictamente necesarios, mejorando la seguridad.

> 📝 **NOTA**
>
> Después de ejecutar `chown -R $USER:$USER ./laravel` (apartado 2) TODOS los archivos han pasado a ser propiedad de `tuUsuario`. Sin embargo, dentro del Docker, Laravel se ejecuta como el usuario `www-data`, por lo que necesita ser propietario de `storage/` y `bootstrap/cache/` para poder escribir en ellos, y por eso, sólo para esos dos directorios se vuelve a reasignas la propiedad, en este caso, a www-data.

#### 🍎 macOS / 🪟 Windows

> 💡 **CONSEJO**
>
> En macOS y Windows, Docker Desktop maneja los permisos automáticamente.
>
> **No necesitas ejecutar ningún comando de permisos en este paso.**
>
> Laravel podrá escribir en `storage/` y `bootstrap/cache/` sin problemas.

---

### 6. Verificar migraciones (automáticas)

> ⚠️ **IMPORTANTE**
>
> Las migraciones se ejecutan automáticamente al levantar los contenedores.
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

#### 🐧 Solo para Linux

> ⚠️ **IMPORTANTE**
> 
> ¿Necesitas ajustar permisos?
>
> - **Archivos creados localmente** (en VS Code): ✅ NO necesitas ajustar permisos
> - **Archivos creados desde contenedores** (con `php artisan make:...`): ⚠️ SÍ necesitas ajustar permisos

**Si creaste archivos desde un contenedor, ejecuta:**

```bash
# 1. Reasignar propiedad a tu usuario
sudo chown -R $USER:$USER ./laravel

# 2. Restaurar permisos de storage y bootstrap/cache
docker exec echo-php sh -c '
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
  find /var/www/html/storage -type d -exec chmod 775 {} \; &&
  find /var/www/html/storage -type f -exec chmod 664 {} \; &&
  find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; &&
  find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;
'
```

#### 🍎 macOS / 🪟 Windows

> 💡 **CONSEJO**
>
> En macOS y Windows, puedes crear archivos libremente desde cualquier lugar (local o contenedor) sin preocuparte por permisos.
>
> Docker Desktop maneja todo automáticamente.

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
docker exec echo-php sh -c '
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
  find /var/www/html/storage -type d -exec chmod 775 {} \; &&
  find /var/www/html/storage -type f -exec chmod 664 {} \; &&
  find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; &&
  find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;
'
```

---

### Problema: "Permission denied" al escribir logs

**Síntoma:** Error al intentar escribir en `storage/logs/laravel.log`.

**Solución:**

```bash
docker exec echo-php sh -c '
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache &&
  find /var/www/html/storage -type d -exec chmod 775 {} \; &&
  find /var/www/html/storage -type f -exec chmod 664 {} \; &&
  find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \; &&
  find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \;
'
```

---

### Problema: Cambios en `.env` no se reflejan

**Solución:**

```bash
docker exec echo-php php artisan config:clear
docker exec echo-php php artisan cache:clear
```

---

## 📝 Comandos personalizados

### Limpiar todas las cachés

Este proyecto incluye un comando personalizado para limpiar todas las cachés de Laravel de una sola vez.

**Ubicación:** `laravel/app/Console/Commands/ClearAllCaches.php`

```bash
# Limpiar todas las cachés (config, route, view, cache)
docker exec echo-php php artisan cache:clear-all

# Limpiar todas las cachés y recargar el autoload de Composer
docker exec echo-php php artisan cache:clear-all --reload
```

**¿Qué hace?**
- Limpia cache de configuración (`config:clear`)
- Limpia cache de rutas (`route:clear`)
- Limpia cache de vistas (`view:clear`)
- Limpia cache de aplicación (`cache:clear`)
- Con `--reload`: Además ejecuta `composer dump-autoload`

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

## 📚 Recursos adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Docker](https://docs.docker.com/)
- [Documentación de React](https://react.dev/)

---

**¿Problemas?** Revisa la sección de "Solución de Problemas" o consulta los logs:
```bash
docker compose logs -f
```
