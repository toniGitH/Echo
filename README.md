<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 📚 Menú

- [🧩 Servicios principales](#-servicios-principales)
- [🐋 Docker: requisitos previos](#-docker-requisitos-previos)

# 🧩 Servicios principales

Este proyecto incluye un entorno Docker completo con **8 servicios**:

📌 **Nginx** – Servidor web que expone la aplicación **Laravel** al puerto **8988**. Recibe las peticiones HTTP y las pasa a PHP-FPM para procesar la lógica de Laravel. ➡️ [localhost:8988](http://localhost:8988)  

📌 **PHP-FPM 8.2** – Motor PHP que ejecuta el código de Laravel dentro del contenedor PHP. No expone puertos, solo procesa peticiones enviadas por Nginx.  

📌 **Laravel** – Contenedor utilitario encargado de instalar dependencias, generar la clave de aplicación, ejecutar migraciones y procesar colas. No expone puertos.  

📌 **MySQL (desarrollo)** – Base de datos principal para la aplicación. Puerto interno **3306**, expuesto en el host como **3700**.

📌 **MySQL (tests)** – Base de datos separada para pruebas automáticas. Puerto interno **3306**, expuesto en el host como **3701**.

📌 **React (Vite)** – Interfaz frontend de la aplicación. Incluye su propio servidor de desarrollo y expone el puerto **8989**. No depende de Nginx. ➡️ [localhost:8989](http://localhost:8989)  

📌 **Swagger Builder (Redocly)** – Contenedor utilitario que compila la documentación OpenAPI a partir de los archivos fuente YAML en `docs/`. No expone puertos.  

📌 **Swagger UI** – Servidor web que sirve la documentación generada en [http://localhost:8081](http://localhost:8081). Escucha en su propio puerto interno y lo mapea al host como 8081. ➡️ [localhost:8081](http://localhost:8081)


---

# 🐋 Docker: requisitos previos

Para ejecutar correctamente el entorno necesitarás utilizar **Docker**.
  
A continuación se detallan las diferencias según tu sistema operativo:

## 🪟 Windows
- Asegurate de tener la virtualización activada en BIOS/UEFI.
- Instala WSL2.
- Instala **Docker Desktop** para Windows.
- Docker Desktop incluye todo lo necesario (Docker Engine + Docker Compose).
- Docker Desktop crea una máquina virtual que gestiona permisos y volúmenes de forma automática.
- No necesitas ajustar permisos ni propiedad de archivos y carpetas (como sí pasa en Linux y Mac).

💡 Cómo trabajar con Docker en Windows:

   **1.** Ejecuta la aplicación Docker Desktop y déjala corriendo en segundo plano (no la necesitas para nada más, aunque puedes utilizar sus funciones, que pueden ser interesantes)

  **2.** Abre la consola (Ubuntu/WSL2 si la tienes, o CMD o PowerShell) y ya podrás ejecutar todos los comandos habituales de Docker.
