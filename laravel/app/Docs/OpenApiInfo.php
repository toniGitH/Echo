<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     title="API Echo",
 *     version="1.0.0",
 *     description=OpenApiInfo::DESCRIPTION,
 *     @OA\Contact(
 *         name="Soporte API Echo",
 *         email="soporte@echo.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8988/api",
 *     description="API de desarrollo"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token de acceso Bearer obtenido del endpoint de login.
 *
 * Formato: `Authorization: Bearer {token}`"
 * )
 */
class OpenApiInfo
{
    public const DESCRIPTION = <<<'DESC'

<details>
<summary><strong>📋 DESCRIPCIÓN</strong></summary>

> API REST de Echo - Plataforma de notificaciones por suscripción.

> Permite a organizaciones publicar notificaciones que llegan automáticamente a sus seguidores suscritos.

</details>


<details>
<summary><strong>📋 CARACTERÍSTICAS</strong></summary>

- Arquitectura hexagonal con DDD.

- Validaciones completas en cada endpoint.

- Respuestas en formato JSON.

- Mensajes de error descriptivos.

- Autenticación mediante Bearer tokens.

</details>


<details>
<summary><strong>🔐 FLUJO DE AUTENTICACIÓN</strong></summary>

#### 1️⃣ CREA UN NUEVO USUARIO O UTILIZA UN USUARIO EXISTENTE

> Usa el endpoint **/auth/register** para crear un nuevo usuario, o utiliza cualquier usuario existente en la base de datos.

#### 2️⃣ INICIA SESIÓN

> Haz login mediante **/auth/login** y recibirás un **Bearer Token** en la respuesta.

> ⚠️ IMPORTANTE: Ese token obtenido **NO** se incluirá automáticamente en las nuevas peticiones tras el login.

#### 3️⃣ AUTORIZA EN SWAGGER UI

> Para probar endpoints protegidos (endpoints que requieren estar autenticado):

- Copia el token obtenido en la respuesta del login.

- Haz clic en el botón 🔓 Authorize (arriba a la derecha).

- Pega SOLO el token (sin la palabra Bearer).

- Haz clic en Authorize y luego Close.

- Ya puedes probar endpoints protegidos.

#### 4️⃣ CIERRA SESIÓN

> Para cerrar sesión, usa el endpoint **/auth/logout** (copia el token en el botón Authorize y ejecuta el endpoint).

> ⚠️ ADVERTENCIA:
- Si usas el endpoint de logout, tu token será REVOCADO inmediatamente (como debe ser).
- Aunque seguirá visible en el botón Authorize, ya no funcionará.
- Deberás hacer login nuevamente para obtener un nuevo token.
- Aunque tengas a varios usuarios logeados, el token que se mantiene activo es el último que se autenticó y que consta en el botón Authorize.

</details>

DESC;
}
