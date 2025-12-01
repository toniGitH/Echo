<div align="center">

<img src=".github/assets/logo-echo.png" alt="Echo Logo"/>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18-61DAFB?logo=react&logoColor=black)](https://reactjs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?logo=docker&logoColor=white)](https://docker.com)
[![DDD](https://img.shields.io/badge/Architecture-DDD-green)](https://en.wikipedia.org/wiki/Domain-driven_design)
[![Hexagonal](https://img.shields.io/badge/Architecture-Hexagonal-blue)](https://en.wikipedia.org/wiki/Hexagonal_architecture_(software))
[![PHPUnit](https://img.shields.io/badge/Testing-PHPUnit-3776AB?logo=php&logoColor=white)](https://phpunit.de)

</div>

---

<details>
<summary style="cursor: pointer;" id="index">
    <h1>🔎 Índice de contenidos</h1>
  </summary>
  
<br>

🎯 [Descripción de la aplicación](#-descripción-de-la-aplicación)

🚀 [Tecnologías utilizadas](#-tecnologías-utilizadas)

📋 [Requisitos previos](#-requisitos-previos)

🔌 [Puertos del proyecto](#-puertos-del-proyecto)

📖 [Documentación API](#-documentación-api)

🧩 [Servicios principales (Docker)](#-servicios-principales-docker)

🐋 [Docker: instalación y requisitos previos](#-docker-instalación-y-requisitos-previos)

🛠️ [Cómo levantar el proyecto](#️-cómo-levantar-el-proyecto)

💾 [Gestión de bases de datos con phpMyAdmin](#-gestión-de-bases-de-datos-con-phpmyadmin)

🧪 [Testing](#-testing)

</details>

---

## 🎯 Descripción de la aplicación

Aplicación de envío de notificaciones en desarrollo.

---

## 🚀 Tecnologías utilizadas

- Backend: **Laravel 12**
- Frontend: **React**
- Entorno de desarrollo: **Docker**
- Testing: **PHPUnit**
- Diseño y arquitectura: **DDD** + **Hexagonal**
- Documentación API: **l5-swagger** (OpenAPI 3.0)

🔝 [Volver al índice](#index)

---

## 📋 Requisitos previos

- Docker Engine/Daemon y Docker Compose Plugin (o Docker Desktop que los incluye)
- 4 GB de RAM disponible y ~2 GB de espacio en disco

🔝 [Volver al índice](#index)

---

## 🔌 Puertos del proyecto

| Servicio | Puerto | URL |
|----------|--------|-----|
| **Backend** (Nginx + Laravel) | 8988 | [http://localhost:8988](http://localhost:8988) |
| **Frontend** (React + Vite) | 8989 | [http://localhost:8989](http://localhost:8989) |
| **phpMyAdmin** | 8080 | [http://localhost:8080](http://localhost:8080) |
| **MySQL (desarrollo)** | 3700 | `localhost:3700` |
| **MySQL (tests)** | 3701 | `localhost:3701` |

> 📝 **NOTA**
>
> **Credenciales de base de datos por defecto:**
> - Usuario: `app`
> - Contraseña: `app`
> - Base de datos: `app`

🔝 [Volver al índice](#index)

---

## 📖 Documentación API

La documentación de la API se genera automáticamente con **l5-swagger** a partir de anotaciones PHP.

**👉 Swagger UI:** [http://localhost:8988/api/documentation](http://localhost:8988/api/documentation)

> 📘 **Guía completa de documentación**
>
> Para aprender cómo documentar nuevos endpoints, regenerar la documentación y más detalles, consulta:
>
> **[SWAGGER.md](SWAGGER.md)**

🔝 [Volver al índice](#index)

---

## 🧩 Servicios principales (Docker)

Este proyecto incluye un entorno Docker completo con **7 servicios**:

| Servicio | Descripción | Puerto |
|----------|-------------|--------|
| **Nginx** | Servidor web que expone Laravel | 8988 |
| **PHP-FPM 8.3** | Motor PHP que ejecuta el código de Laravel | - |
| **Laravel** | Contenedor utilitario para dependencias, migraciones y colas | - |
| **MySQL (desarrollo)** | Base de datos principal | 3700 |
| **MySQL (tests)** | Base de datos para pruebas automáticas | 3701 |
| **phpMyAdmin** | Interfaz web para gestionar las bases de datos MySQL | 8080 |
| **React (Vite)** | Interfaz frontend con servidor de desarrollo | 8989 |

> 📝 **NOTA**
>
> **Nginx** recibe las peticiones HTTP y las pasa a **PHP-FPM** para procesar la lógica de Laravel.
>
> **React** incluye su propio servidor de desarrollo y no depende de Nginx.

🔝 [Volver al índice](#index)

---

## 🐋 Docker: instalación y requisitos previos

Para ejecutar el proyecto necesitarás **Docker** instalado en tu sistema.

A continuación se detallan las instrucciones según tu sistema operativo.

### 🐧 Linux (Ubuntu/Debian)

> 💡 **CONSEJO**
>
> Estas instrucciones son para Ubuntu 22.04+ y Debian. Para otras distribuciones, consulta la [documentación oficial de Docker](https://docs.docker.com/engine/install/).

#### 1. Desinstalar versiones antiguas (opcional)

Elimina instalaciones previas de Docker para evitar conflictos:

```bash
sudo apt-get remove -y docker docker-engine docker.io containerd runc || true
```

#### 2. Preparar paquetes previos

Actualiza repositorios e instala utilidades necesarias:

```bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg lsb-release
```

#### 3. Descargar la clave GPG oficial de Docker

```bash
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
```

#### 4. Registrar el repositorio oficial de Docker

```bash
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $UBUNTU_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

#### 5. Instalar Docker Engine + plugins

```bash
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

#### 6. Usar Docker sin sudo (recomendado)

```bash
sudo usermod -aG docker $USER
```

> ⚠️ **IMPORTANTE**
>
> Cierra sesión y vuelve a entrar para aplicar los cambios.

#### 7. Verificar instalación

```bash
docker --version
docker compose version
docker run --rm hello-world
```

🔝 [Volver al índice](#index)

### 🪟 Windows 10/11

> 💡 **CONSEJO**
>
> Docker en Windows requiere WSL2. Asegúrate de tener la virtualización activada en BIOS/UEFI.

#### 1. Instalar Docker Desktop

Descarga e instala [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/) desde el sitio oficial.

#### 2. Habilitar WSL 2

Si Docker Desktop lo solicita, habilita WSL 2 siguiendo las instrucciones en pantalla.

#### 3. Reiniciar y verificar

Abre PowerShell, CMD o Ubuntu/WSL2 y ejecuta:

```bash
docker --version
docker compose version
```

#### 4. Iniciar Docker Desktop

Inicia la aplicación Docker Desktop y déjala corriendo en segundo plano.

Ya puedes ejecutar todos los comandos habituales de Docker desde la consola.

🔝 [Volver al índice](#index)

### 🍎 macOS (Intel / Apple Silicon)

> 💡 **CONSEJO**
>
> Docker Desktop funciona tanto en Apple Silicon (M1/M2/M3) como en Intel.

#### 1. Instalar Docker Desktop

Descarga [Docker Desktop para macOS](https://www.docker.com/products/docker-desktop/) desde el sitio oficial.

Descarga la versión correspondiente a tu chip (Intel o Apple Silicon) y arrastra el icono a *Aplicaciones*.

#### 2. Autorizar Docker Desktop

macOS puede mostrar un aviso para permitir extensiones del sistema.

Ve a: *Preferencias del Sistema → Seguridad y privacidad* y permite las extensiones si aparece el aviso.

#### 3. Iniciar Docker Desktop

Inicia Docker Desktop y espera a que esté "*Running*".

#### 4. Verificar instalación

Abre Terminal y ejecuta:

```bash
docker --version
docker compose version
```

#### 5. Ajustar recursos (opcional)

Puedes ajustar CPU, memoria RAM y disco asignados a Docker desde:

*Docker Desktop → Settings → Resources*

🔝 [Volver al índice](#index)

---

## 🛠️ Cómo levantar el proyecto

Para instrucciones detalladas sobre cómo configurar y levantar el proyecto en **Linux**, **macOS** o **Windows**, consulta el archivo:

**📄 [SETUP.md](SETUP.md)**

El archivo SETUP.md contiene:
- Instrucciones paso a paso para cada sistema operativo
- Configuración de permisos (solo Linux)
- Solución de problemas comunes
- Comandos útiles para el desarrollo diario

🔝 [Volver al índice](#index)

---

## 💾 Gestión de bases de datos con phpMyAdmin

phpMyAdmin es una interfaz web que te permite gestionar las bases de datos MySQL de forma visual y sencilla.

### 🌐 Acceso

Una vez que los contenedores estén levantados, accede a phpMyAdmin en:

**👉 URL:** [http://localhost:8080](http://localhost:8080)

### 🔑 Credenciales

Para acceder a las bases de datos, usa las siguientes credenciales:

| Campo | Valor |
|-------|-------|
| **Servidor** | `mysql` (desarrollo) o `mysql_test` (tests) |
| **Usuario** | `root` |
| **Contraseña** | `root` |

> 💡 **CONSEJO**
>
> En la pantalla de login de phpMyAdmin, encontrarás un dropdown para seleccionar el servidor.
>
> - Selecciona **`mysql`** para acceder a la base de datos de desarrollo
> - Selecciona **`mysql_test`** para acceder a la base de datos de tests

### 💾 Bases de datos disponibles

Una vez dentro de phpMyAdmin, encontrarás las siguientes bases de datos:

| Base de datos | Descripción | Servidor |
|---------------|-------------|----------|
| `app` | Base de datos principal de desarrollo | `mysql` |
| `app_testing` | Base de datos para pruebas automáticas | `mysql_test` |

### 📋 Funcionalidades disponibles

Con phpMyAdmin puedes:

- ✅ Explorar tablas y ver datos
- ✅ Ejecutar consultas SQL personalizadas
- ✅ Crear, modificar y eliminar tablas
- ✅ Importar y exportar bases de datos
- ✅ Gestionar usuarios y permisos
- ✅ Ver la estructura de las tablas
- ✅ Ejecutar operaciones de mantenimiento

> ⚠️ **IMPORTANTE**
>
> **Cuidado con las operaciones destructivas:**
>
> - Evita eliminar tablas en la base de datos de desarrollo (`app`) si contiene datos importantes
> - La base de datos de tests (`app_testing`) se limpia automáticamente en cada ejecución de tests

🔝 [Volver al índice](#index)

---

## 🧪 Testing

### Información previa

> 📝 **NOTA**
>
> **Tipos de tests:**
> - **Unitarios:** Para elementos del dominio (entidades, value objects, casos de uso, etc.)
> - **Integración:** Para probar la interacción entre componentes
> - **Feature:** Para probar endpoints completos
>
> Los tests unitarios utilizan `PHPUnit\Framework\TestCase` de **PHPUnit**.
>
> Los tests de integración y feature utilizan `Tests\TestCase` de **Laravel**.

> ⚠️ **IMPORTANTE**
>
> **Contenedor de ejecución:**
>
> ✅ Ejecuta los tests desde el **contenedor de PHP** (`echo-php`), no desde el contenedor de Laravel.
>
> ✅ El contenedor PHP no setea variables de entorno en `docker-compose.yml`, por lo que los valores de `phpunit.xml` se aplicarán correctamente.
>
> ❌ El contenedor Laravel setea variables en `docker-compose.yml` que tienen prioridad sobre `phpunit.xml`, lo que puede causar que los tests usen la base de datos incorrecta.

> 💡 **CONSEJO**
>
> **Base de datos de testing:**
>
> - El proyecto monta dos bases de datos independientes: una para **desarrollo** y otra para **tests**.
> - Puedes elegir qué base de datos utilizar: **SQLite** en memoria o **MySQL**.
> - En `phpunit.xml` tienes las dos configuraciones disponibles.
> - Algunos tests se omiten automáticamente en SQLite porque están diseñados para MySQL (case-insensitive).

### Cómo ejecutar los tests

#### 1. Elige el entorno de testing (SQLite o MySQL)

Abre el archivo `phpunit.xml` y:
- Mantén descomentado el bloque que corresponda a la configuración que quieras usar
- Comenta el bloque de la otra configuración

#### 2. Levanta los contenedores

```bash
docker compose up -d
```

#### 3. Entra dentro del contenedor PHP

```bash
docker compose exec php bash
```

#### 4. Dentro del contenedor PHP, ejecuta los tests

**Ejecutar todos los tests:**
```bash
php artisan test
```

**Ejecutar solo tests unitarios:**
```bash
php artisan test --testsuite=unit
```

**Ejecutar solo tests de integración:**
```bash
php artisan test --testsuite=integration
```

**Ejecutar solo tests de feature:**
```bash
php artisan test --testsuite=feature
```

🔝 [Volver al índice](#index)

---
