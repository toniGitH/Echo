# 📚 Documentación de Swagger (l5-swagger)

Este proyecto usa **l5-swagger** para generar documentación interactiva de la API mediante anotaciones PHP.

---

## 🌐 Acceso a Swagger UI

Una vez levantados los contenedores, accede a la documentación en:

👉 **http://localhost:8988/api/documentation**

---

## 🔄 Regeneración de Documentación

### Automática (al levantar contenedores)

✅ **La documentación se regenera automáticamente** cada vez que ejecutas:

```bash
docker compose up -d
```

Esto está configurado en el contenedor `laravel` del `docker-compose.yml`.

### Manual (cuando modificas anotaciones)

Si modificas archivos de documentación y los contenedores ya están corriendo, regenera manualmente:

```bash
docker compose exec laravel php artisan l5-swagger:generate
```

---

## 📁 Estructura de Archivos

La documentación vive en archivos PHP con anotaciones OpenAPI:

```
laravel/app/Docs/
├── OpenApiInfo.php              # Información general de la API
└── Endpoints/
    └── Auth/
        ├── RegisterEndpoint.php  # Documentación de /auth/register
        ├── LoginEndpoint.php     # Documentación de /auth/login
        └── LogoutEndpoint.php    # Documentación de /auth/logout
```

### 📄 Archivo generado (NO commitear)

```
laravel/storage/api-docs/api-docs.json
```

Este archivo se genera automáticamente y está excluido en `.gitignore`.

---

## ➕ Cómo Documentar un Nuevo Endpoint

### 1. Crear el archivo de documentación

Crea un nuevo archivo PHP en la ruta correspondiente. Por ejemplo, para documentar `/users`:

```
laravel/app/Docs/Endpoints/Users/GetUsersEndpoint.php
```

### 2. Estructura del archivo

```php
<?php

namespace App\Docs\Endpoints\Users;

class GetUsersEndpoint
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Obtener lista de usuarios",
     *     description="Retorna todos los usuarios del sistema",
     *     operationId="getUsers",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid", example="9d4e8c1a-3b2f-4d5e-8f9a-1b2c3d4e5f6a"),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", format="email", example="juan@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
    }
}
```

### 3. Puntos importantes

- ✅ **Namespace**: Debe coincidir con la ruta del archivo (`App\Docs\Endpoints\Users`)
- ✅ **Método `__invoke()`**: Las anotaciones deben estar en el docblock de este método
- ✅ **Tags**: Agrupa endpoints relacionados (ej: "Auth", "Users", "Posts")
- ✅ **Security**: Usa `security={{"bearerAuth":{}}}` para endpoints protegidos
- ✅ **Escape de comillas**: En las descripciones, usa `""` para escapar comillas (ej: `""Authorize""`)

### 4. Regenerar documentación

```bash
docker compose exec laravel php artisan l5-swagger:generate
```

---

## 🔐 Autenticación en Swagger UI

Para probar endpoints protegidos:

1. **Haz login** en `/auth/login` → obtendrás un `token`
2. **Copia el token** de la respuesta
3. **Haz clic en el botón "Authorize"** 🔓 (arriba a la derecha)
4. **Pega el token** en el campo "Value" (solo el token, sin "Bearer")
5. **Haz clic en "Authorize"** y luego "Close"
6. Ahora puedes probar endpoints protegidos ✅

---

## 📖 Recursos Útiles

- **Documentación oficial de l5-swagger**: https://github.com/DarkaOnLine/L5-Swagger
- **Especificación OpenAPI 3.0**: https://swagger.io/specification/
- **Anotaciones PHP (swagger-php)**: https://zircote.github.io/swagger-php/

---

## ⚙️ Configuración

La configuración de l5-swagger está en:

```
laravel/config/l5-swagger.php
```

**Configuraciones clave:**
- `annotations`: Ruta donde se escanean las anotaciones (`app/Docs`)
- `routes.api`: Ruta de Swagger UI (`/api/documentation`)
- `generate_always`: Si se regenera en cada petición (desactivado en producción)

---

## 🚨 Troubleshooting

### La documentación no se actualiza

```bash
# Limpia la caché de Laravel
docker compose exec laravel php artisan config:clear
docker compose exec laravel php artisan cache:clear

# Regenera la documentación
docker compose exec laravel php artisan l5-swagger:generate
```

### Error "Required @OA\PathItem() not found"

- Verifica que las anotaciones estén en el método `__invoke()`, no en la clase
- Asegúrate de que el namespace coincida con la ruta del archivo

### Swagger UI muestra error 404

- Verifica que el contenedor `laravel` esté corriendo
- Comprueba que se haya generado `laravel/storage/api-docs/api-docs.json`
