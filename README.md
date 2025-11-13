# Notifier – Guía de instalación y uso con Docker

Este proyecto incluye un entorno Docker con 5 servicios:
- Nginx (sirve Laravel) en puerto 8988
- MySQL (3306 dentro del contenedor) expuesto en 3700
- PHP-FPM 8.2 (para Laravel)
- Laravel (contenedor utilitario para composer/artisan)
- React (Vite) en puerto 8989

Requisitos
- Docker Engine/Daemon y Docker Compose Plugin (o Docker Desktop que los incluye)
- 4 GB de RAM disponible y ~2 GB de espacio en disco

Instalar Docker

Windows 10/11
1) Instala Docker Desktop para Windows desde el sitio oficial.
2) Habilita WSL 2 si Docker Desktop lo solicita.
3) Reinicia y verifica:
   - Abre PowerShell y ejecuta: docker --version y docker compose version

macOS (Intel/Apple Silicon)
1) Instala Docker Desktop para macOS desde el sitio oficial.
2) Inicia Docker Desktop y espera a que esté “Running”.
3) Verifica en Terminal: docker --version y docker compose version

Ubuntu/Debian (ejemplo para Ubuntu 22.04+)
1) Desinstala versiones antiguas (opcional):
   sudo apt-get remove -y docker docker-engine docker.io containerd runc || true
2) Paquetes previos y repositorio oficial de Docker:
   sudo apt-get update
   sudo apt-get install -y ca-certificates curl gnupg lsb-release
   sudo install -m 0755 -d /etc/apt/keyrings
   curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
   echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo $UBUNTU_CODENAME) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
3) Instala Docker Engine + Compose plugin:
   sudo apt-get update
   sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
4) (Recomendado) Permitir usar docker sin sudo:
   sudo usermod -aG docker $USER
   # Cierra sesión y vuelve a entrar (o reinicia) para aplicar el grupo
5) Verifica la instalación:
   docker --version
   docker compose version
   docker run --rm hello-world

Puertos del proyecto
- http://localhost:8988 → Nginx + Laravel (public/)
- http://localhost:8989 → React (Vite dev server)
- MySQL: host localhost puerto 3700 (3306 en contenedor)
  - Credenciales por defecto (si usas .env.example): usuario app / pass app / base app

Cómo levantar el proyecto
1) Clona el repositorio (o entra a la carpeta del proyecto):
   git clone <URL_DEL_REPO>
   cd notifier
2) (Opcional) Copia variables por defecto y ajústalas si es necesario:
   cp .env.example .env
3) Levanta la pila (construye imágenes si es la primera vez):
   docker compose up -d --build
4) Espera unos segundos y comprueba:
   - docker compose ps
   - curl -I http://localhost:8988/
   - curl -I http://localhost:8989/

Comandos útiles
- Ver estado: docker compose ps
- Ver logs de un servicio (ej. nginx): docker compose logs -f nginx
- Reconstruir y reiniciar: docker compose up -d --build
- Parar: docker compose down
- Parar y borrar volúmenes (atención: borra datos de MySQL): docker compose down -v

Notas de implementación
- Nginx sirve la carpeta ./laravel/public y envía PHP a php-fpm (servicio "php"). Configuración en nginx/conf.d/default.conf.
- El contenedor "laravel" instala dependencias, genera APP_KEY, ejecuta migraciones y deja un queue:work corriendo para mantenerse activo. Puedes cambiar el comando en docker-compose.yml según tus necesidades (horizon, scheduler, etc.).
- El contenedor "react" arranca Vite en 5173 (expuesto a 8989) y monta ./react en /app para hot reload.

Solución de problemas
1) Permisos de Laravel (500 por permisos en storage):
   docker exec notifier-php sh -lc 'cd /var/www/html && chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache'
2) Laravel no conecta a MySQL (SQLSTATE[HY000] [2002]):
   - Asegúrate de que DB_HOST=mysql, DB_PORT=3306, DB_DATABASE=app, DB_USERNAME=app, DB_PASSWORD=app en ./laravel/.env
   - Limpia cache de config: docker exec notifier-php php /var/www/html/artisan config:clear
   - Comprueba MySQL healthy: docker compose ps (estado del servicio mysql)
3) Puerto en uso (8988/8989/3700):
   - Cambia los puertos en docker-compose.yml o libera el puerto en tu máquina.
4) React devuelve 404/No response:
   - Espera a que npm install termine dentro del contenedor react
   - Revisa logs: docker compose logs -f react

Estructura de carpetas relevante
- ./docker-compose.yml → definición de servicios
- ./nginx/conf.d/default.conf → vhost de Nginx
- ./php/Dockerfile → imagen php-fpm con extensiones
- ./laravel/ → app Laravel (artisan, composer.json, public/)
- ./react/ → app React (Vite)

Soporte
Si necesitas ajustar puertos, añadir HTTPS (reverse proxy, certificados), o integrar otros servicios (Redis, Horizon, Mailhog), puedes ampliar docker-compose.yml y la configuración de Nginx en nginx/conf.d.
