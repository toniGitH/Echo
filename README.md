<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 📋 Índice de contenidos

<details>
  <summary><strong>📂 Mostrar / ocultar índice</strong></summary>

- [🎯 Descripción de la aplicación](#-descripción-de-la-aplicación)
- [⚠️ Requisitos previos](#-requisitos-previos)
- [🔌 Puertos del proyecto](#-puertos-del-proyecto)
- [🔌 Documentación API](#-documentación-api)
- [🧩 Servicios principales (Docker)](#-servicios-principales-docker)
- [🐋 Docker: instalación y requisitos previos](#-docker-instalación-y-requisitos-previos)

</details>


# 🎯 Descripción de la aplicación

Aplicación de envío de notificaciones en desarrollo.

---

# ⚠️ Requisitos previos

- Docker Engine/Daemon y Docker Compose Plugin (o Docker Desktop que los incluye)
- 4 GB de RAM disponible y ~2 GB de espacio en disco

---

# 🔌 Puertos del proyecto

##### 👉 Backend: Nginx + Laravel (public/) ➡️ [http://localhost:8988](http://localhost:8988)

##### 👉 Frontend: React (Vite dev server) ➡️ [http://localhost:8989](http://localhost:8989)

##### 👉 MySQL de desarrollo: host localhost puerto 3700 (3306 en contenedor)

##### 👉 MySQL de tests: host localhost puerto 3701 (3306 en contenedor)

###### 🔑 Credenciales base de datos por defecto (si usas .env.example) ➡️ usuario: *app* / pass: *app* / base: *app*

---

# 📖 Documentación API
##### 👉 SwaggerUI ➡️ [http://localhost:8081](http://localhost:8081) 

---

# 🧩 Servicios principales (Docker)

Este proyecto incluye un entorno Docker completo con **8 servicios**:

📌 **Nginx** – Servidor web que expone la aplicación **Laravel** al puerto **8988**. Recibe las peticiones HTTP y las pasa a PHP-FPM para procesar la lógica de Laravel.

📌 **PHP-FPM 8.2** – Motor PHP que ejecuta el código de Laravel dentro del contenedor PHP. No expone puertos, solo procesa peticiones enviadas por Nginx.  

📌 **Laravel** – Contenedor utilitario encargado de instalar dependencias, generar la clave de aplicación, ejecutar migraciones y procesar colas. No expone puertos.  

📌 **MySQL (desarrollo)** – Base de datos principal para la aplicación. Puerto interno **3306**, expuesto en el host como **3700**.

📌 **MySQL (tests)** – Base de datos separada para pruebas automáticas. Puerto interno **3306**, expuesto en el host como **3701**.

📌 **React (Vite)** – Interfaz frontend de la aplicación. Incluye su propio servidor de desarrollo y expone el puerto **8989**. No depende de Nginx. 

📌 **Swagger Builder (Redocly)** – Contenedor utilitario que compila la documentación OpenAPI a partir de los archivos fuente YAML en `docs/`. No expone puertos.  

📌 **Swagger UI** – Servidor web que sirve la documentación generada. Escucha en su propio puerto interno y lo mapea al host como 8081.

---

# 🐋 Docker: instalación y requisitos previos

Para ejecutar el proyecto necesitarás utilizar **Docker**.
  
A continuación se detallan las diferencias según tu sistema operativo:

## 🪟 Windows 10/11
##### 1) Asegurate de tener la virtualización activada en BIOS/UEFI.
##### 2) Instala Docker Desktop para Windows desde el sitio oficial.
##### 3) Habilita WSL 2 si Docker Desktop lo solicita.
##### 4) Reinicia y verifica:
🔹 Abre la consola (PowerShell, CMD o Ubuntu/WSL2 si la tienes) y ejecuta:

``` bash
docker --version
docker compose version
```

##### 5) Inicia la aplicación Docker Desktop y déjala corriendo en segundo plano (no la necesitas para nada más, aunque puedes utilizar sus funciones, que pueden ser interesantes)
##### 6) En la consola (Ubuntu/WSL2 si la tienes, o CMD o PowerShell) ya podrás ejecutar todos los comandos habituales de Docker.

## 🍎 macOS (Intel / Apple Silicon)

##### 1) Comprueba compatibilidad del procesador.
Docker Desktop funciona tanto en Apple Silicon (M1/M2/M3) como en Intel, pero usa tecnologías distintas internamente (HyperKit vs Apple Virtualization Framework).
No tienes que configurar nada, solo asegurarte de que tu versión de macOS es compatible (macOS 12+ normalmente).

##### 2) Instala Docker Desktop para macOS desde el sitio oficial.
Descarga la versión correspondiente a tu chip y arrastra el icono a *Aplicaciones*.

##### 3) Autoriza Docker Desktop si macOS te lo solicita.
macOS puede mostrar un aviso para permitir extensiones o controladores del sistema.
Ve a: *Preferencias del Sistema → Seguridad y privacidad* y permite las extensiones si aparece el aviso.

##### 4)  Inicia Docker Desktop y espera a que esté "*Running*".
Igual que en Windows: una vez está ejecutándose, ya no necesitas abrir la app salvo que quieras gestionar contenedores visualmente.

##### 5)  Verifica la instalación desde Terminal:

``` bash
docker --version
docker compose version
```

##### 6)  Usa Docker normalmente desde Terminal.
No hay diferencias relevantes frente a Linux/WSL: puedes ejecutar *docker*, *docker compose*, etc.

##### 7) Opcional: justa recursos asignados a Docker Desktop.

CPU, memoria RAM, disco usados para las máquinas virtuales.
Se controla desde: *Docker Desktop → Settings → Resources*

##### 8) Opcional: habilita Docker Buildx y otras extensiones.
Docker Desktop en macOS lo trae activado por defecto, pero puedes ajustarlo en:
*Settings → Features in development / Extensions*


## 🐧 Ubuntu/Debian (ejemplo para Ubuntu 22.04+)

##### 1) Desinstalar versiones antiguas (opcional)

🔹 Eimina instalaciones previas de Docker para evitar conflictos.

``` bash
sudo apt-get remove -y docker docker-engine docker.io containerd runc || true
```

##### 2) Preparar paquetes previos

🔹 Actualiza repositorios e instala utilidades necesarias para añadir repositorios seguros.

``` bash
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg lsb-release
```

##### 3) Crear directorio de claves APT

🔹 Crea la carpeta recomendada por Debian/Ubuntu para almacenar claves GPG de  repositorios.

``` bash
sudo install -m 0755 -d /etc/apt/keyrings
```

##### 4) Descargar la clave GPG oficial de Docker

🔹 Permite verificar la autenticidad de los paquetes del repositorio de Docker.

``` bash
curl -fsSL https://download.docker.com/linux/ubuntu/gpg   | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
```

##### 5) Registrar el repositorio oficial de Docker en APT

🔹 Añade la fuente oficial de Docker para instalar versiones actualizadas.

``` bash
echo   "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg]   https://download.docker.com/linux/ubuntu   $(. /etc/os-release && echo $UBUNTU_CODENAME) stable"   | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```
##### 6) Instalar Docker Engine + plugins

🔹 Instala el motor de Docker, CLI, containerd, Buildx y Compose plugin.

``` bash
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io   docker-buildx-plugin docker-compose-plugin
```

##### 7) Recomendado: Usar Docker sin sudo

🔹 Permite ejecutar comandos Docker sin necesidad de sudo.

``` bash
sudo usermod -aG docker $USER
```

➡️ Cierra sesión y vuelve a entrar para aplicar los cambios.


##### 8) Verificar instalación

🔹 Comprueba que Docker y Compose funcionan correctamente.

``` bash
docker --version
docker compose version
docker run --rm hello-world
```




---


☝🏼 No necesitas ajustar permisos ni propiedad de archivos y carpetas (como sí pasa en Linux y Mac).
